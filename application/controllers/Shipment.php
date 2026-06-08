<?php
defined('BASEPATH') or exit('No direct script access allowed');


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Shipment extends Authenticated_Controller
{
	protected $allowed_roles = ['admin-kribo', 'finance-kribo', 'admin-mitra', 'staff-mitra', 'checker', 'driver'];

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['pdfgenerator', 'api_whatsapp']);
		$this->load->model(['M_Shipment', 'M_Pricelist']); // Model yang bikin AWB tadi
	}

	public function index()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$is_agent = ($sess['role_scope'] === 'agent');
		$agent_id = $is_agent ? $sess['agent_id'] : NULL;

		// 1. Ambil Filter
		$filters = [
			'q'      => $this->input->get('q', TRUE),
			'status' => $this->input->get('status', TRUE),
			'start'  => $this->input->get('start', TRUE),
			'end'    => $this->input->get('end', TRUE),
		];

		// 2. Hitung Total terfilter
		$total = $this->M_Shipment->count_filtered($agent_id, $filters);

		// 3. Hitung Pagination (Pakai helper/base method lu)
		$per_page = 15;
		$paginate = $this->paginate($total, $per_page, $filters);

		// 4. Ambil Data Paged
		$shipments = $this->M_Shipment->get_paged(
			$paginate['per_page'],
			$paginate['offset'],
			$agent_id,
			$filters
		);

		// 5. Statistik tetap diambil (untuk cards di atas)
		$stats = $this->M_Shipment->get_stats($agent_id);

		$data = array_merge([
			'title'     => 'Daftar Shipment',
			'shipments' => $shipments,
			'stats'     => $stats,
			'total'     => $total,
			'filters'   => $filters,
			'is_agent'  => $is_agent,
			'role_slug'  => $sess['role_slug'],
		], $paginate);

		$this->render('app/pages/shipment/index', $data);
	}

	// Fungsi helper internal untuk membersihkan format angka Indonesia
	private function _parse_indo_number($str)
	{
		if (empty($str)) return 0;
		$str = str_replace('.', '', $str); // Hilangkan titik (ribuan)
		$str = str_replace(',', '.', $str); // Ubah koma jadi titik (desimal)
		return floatval($str);
	}

	public function create()
	{
		$this->_check_access();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			// 1. Dapatkan Input Dasar
			$origin      = $this->input->post('origin', TRUE);
			$destination = $this->input->post('destination', TRUE);
			$service_id  = $this->input->post('service_type_id', TRUE);
			$sender_name  = $this->input->post('sender_name', TRUE);
			$sender_phone = $this->input->post('sender_phone', TRUE);
			$payment_type = $this->input->post('payment_type', TRUE);

			// 2. Validasi Master Pricelist
			$pricelist = $this->db->get_where('pricelist', [
				'origin'          => $origin,
				'destination'     => $destination,
				'service_type_id' => $service_id,
				'is_active'       => 1
			])->row();

			if (!$pricelist) {
				$this->session->set_flashdata('error', 'Rute atau layanan tidak tersedia!');
				redirect('shipment/create');
			}

			// 3. Hitung Berat (Actual vs Volume) & Siapkan Data Dimensi Fisik Per Dus
			$actual_weight = $this->_parse_indo_number($this->input->post('actual_weight'));
			$dim_length = $this->input->post('dim_length');
			$dim_width  = $this->input->post('dim_width');
			$dim_height = $this->input->post('dim_height');
			$dim_qty    = $this->input->post('dim_qty');

			$no_resi = $this->M_Shipment->generate_no_resi();

			$insert_dimensions = [];
			$total_volume_weight = 0;
			$total_koli = 0;
			$koli_counter = 1;

			if (!empty($dim_qty)) {
				foreach ($dim_qty as $key => $qty) {
					if ($qty > 0) {
						$total_koli += $qty;

						$p = str_replace(',', '.', $dim_length[$key]);
						$l = str_replace(',', '.', $dim_width[$key]);
						$t = str_replace(',', '.', $dim_height[$key]);

						$vol_weight_per_item = ($p * $l * $t) / 5000;
						$total_volume_weight += ($vol_weight_per_item * $qty);

						for ($i = 0; $i < $qty; $i++) {
							$barcode_koli = $no_resi . '-' . str_pad($koli_counter, 2, '0', STR_PAD_LEFT);

							$insert_dimensions[] = [
								'shipment_id'  => NULL,
								'barcode_koli' => $barcode_koli,
								'qty'          => 1,
								'length'       => $p,
								'width'        => $l,
								'height'       => $t
							];
							$koli_counter++;
						}
					}
				}
			}

			// 4. Tentukan Chargeable Weight
			$chargeable = max($actual_weight, $total_volume_weight);
			if ($chargeable < $pricelist->min_weight_kg) {
				$chargeable = $pricelist->min_weight_kg;
			}
			$chargeable = ceil($chargeable);

			// 5. Logic Penentuan Harga & Margin Ongkir
			$cost_per_kg = $pricelist->price_kribo;
			$sell_per_kg = $pricelist->price_smesco;

			if ($pricelist->is_tiered == 1) {
				$tier = $this->db->where('pricelist_id', $pricelist->id)
					->where('min_weight <=', $chargeable)
					->where('max_weight >=', $chargeable)
					->get('pricelist_tiers')->row();
				if ($tier) {
					$cost_per_kg = $tier->price_kribo;
					$sell_per_kg = $tier->price_smesco;
				} else {
					$last_tier = $this->db->where('pricelist_id', $pricelist->id)
						->order_by('max_weight', 'DESC')->limit(1)->get('pricelist_tiers')->row();
					if ($last_tier) {
						$cost_per_kg = $last_tier->price_kribo;
						$sell_per_kg = $last_tier->price_smesco;
					}
				}
			}

			$shipping_total  = $chargeable * $sell_per_kg;
			$shipping_margin = ($sell_per_kg - $cost_per_kg) * $chargeable;

			// 6. Logic Pickup Service
			$pickup_fee = 0;
			$pickup_id  = NULL;

			if ($this->input->post('use_pickup') == 1) {
				$p_id = $this->input->post('pickup_rate_id');
				$rate_db = $this->db->get_where('master_pickup_rates', ['id' => $p_id])->row();

				if ($rate_db && $chargeable >= $rate_db->min_weight) {
					$pickup_id     = $p_id;
					$pickup_fee    = $rate_db->price_smesco;
				}
			}

			// 7. Kalkulasi Final & Persiapan Data Surcharge Addons
			$sess         = $this->session->userdata('user');
			$is_valuable  = $this->input->post('is_valuable') ? 1 : 0;
			$addon_codes      = $this->input->post('addons') ?: [];
			$total_addon_fee  = 0;
			$insert_addons    = [];

			if (!empty($addon_codes)) {
				$this->db->where_in('code', $addon_codes)->where('is_active', 1);
				$selected_addons = $this->db->get('master_addons')->result();

				foreach ($selected_addons as $addon) {
					$fee = 0;
					if (!empty($insert_dimensions)) {
						foreach ($insert_dimensions as $dim) {
							$p = $dim['length'];
							$l = $dim['width'];
							$t = $dim['height'];
							$q = $dim['qty'];
							$min = $addon->min_charge;

							if ($addon->calc_method === 'VOLUME') {
								$fee_per_koli = $p * $l * $t * $addon->base_factor;
								$fee += max($fee_per_koli, $min) * $q;
							} elseif ($addon->calc_method === 'VOLUME_PLUS') {
								$fee_per_koli = ($p + 10) * ($l + 10) * ($t + 10) * $addon->base_factor;
								$fee += max($fee_per_koli, $min) * $q;
							} elseif ($addon->calc_method === 'PER_KOLI') {
								$fee_per_koli = $addon->base_factor;
								$fee += max($fee_per_koli, $min) * $q;
							}
						}
					} else {
						if ($addon->calc_method === 'PER_KOLI') {
							$fee = $addon->base_factor * ($total_koli ?: 1);
						}
					}

					if ($fee > 0) {
						$total_addon_fee += $fee;
						$insert_addons[] = [
							'shipment_id'  => NULL,
							'addon_id'     => $addon->id,
							'addon_amount' => $fee,
						];
					}
				}
			}

			$total_margin = $shipping_margin;

			// Helper Nama Alamat
			$sender_prov_name  = $this->db->get_where('mt_provinsi',   ['id' => $this->input->post('sender_provinsi')])->row();
			$sender_kota_name  = $this->db->get_where('mt_kota',       ['id' => $this->input->post('sender_kota')])->row();
			$sender_kec_name   = $this->db->get_where('mt_kecamatan',  ['id' => $this->input->post('sender_kecamatan')])->row();
			$sender_kel_name   = $this->db->get_where('mt_kelurahan',  ['id' => $this->input->post('sender_kelurahan')])->row();

			$receiver_prov_name = $this->db->get_where('mt_provinsi',  ['id' => $this->input->post('receiver_provinsi')])->row();
			$receiver_kota_name = $this->db->get_where('mt_kota',       ['id' => $this->input->post('receiver_kota')])->row();
			$receiver_kec_name  = $this->db->get_where('mt_kecamatan', ['id' => $this->input->post('receiver_kecamatan')])->row();
			$receiver_kel_name  = $this->db->get_where('mt_kelurahan', ['id' => $this->input->post('receiver_kelurahan')])->row();

			$full_sender_address = implode(', ', array_filter([
				$this->input->post('sender_address_detail', TRUE),
				$sender_kel_name  ? $sender_kel_name->nama_kelurahan  : '',
				$sender_kec_name  ? 'Kec. ' . $sender_kec_name->nama_kecamatan  : '',
				$sender_kota_name ? 'Kab/Kota ' . $sender_kota_name->nama_kota      : '',
				$sender_prov_name ? $sender_prov_name->nama_provinsi  : '',
			]));

			$full_receiver_address = implode(', ', array_filter([
				$this->input->post('receiver_address_detail', TRUE),
				$receiver_kel_name  ? $receiver_kel_name->nama_kelurahan  : '',
				$receiver_kec_name  ? 'Kec. ' . $receiver_kec_name->nama_kecamatan  : '',
				$receiver_kota_name ? 'Kab/Kota ' . $receiver_kota_name->nama_kota      : '',
				$receiver_prov_name ? $receiver_prov_name->nama_provinsi  : '',
			]));

			// ============================================================
			// INSIGHT PENYESUAIAN: FILE UPLOAD MANAGEMENT (FOLDER BARU)
			// ============================================================
			$photo_name = NULL;
			$pending_upload = [];

			if (isset($_FILES['shipment_photo']) && $_FILES['shipment_photo']['error'] === UPLOAD_ERR_OK) {
				$file    = $_FILES['shipment_photo'];
				$allowed = ['image/jpeg', 'image/jpg', 'image/png'];

				if (!in_array($file['type'], $allowed)) {
					$this->session->set_flashdata('error', 'Format foto tidak valid.');
					redirect('shipment/create');
				}

				if ($file['size'] > 2 * 1024 * 1024) {
					$this->session->set_flashdata('error', 'Ukuran foto maksimal 2MB.');
					redirect('shipment/create');
				}

				$pending_upload = [
					'tmp_name' => $file['tmp_name'],
					'ext'      => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
				];
			}

			// Proses upload jika ada file
			if (!empty($pending_upload)) {
				// Diseragamkan masuk folder uploads/shipments/booking/
				$upload_dir = FCPATH . 'uploads/shipments/booking/';
				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0755, TRUE);
				}

				$photo_name = 'BOOKING_' . $no_resi . '_' . time() . '.' . $pending_upload['ext'];
				$full_path = $upload_dir . $photo_name;

				if (!move_uploaded_file($pending_upload['tmp_name'], $full_path)) {
					$this->session->set_flashdata('error', 'Gagal mengupload foto kargo awal. Coba lagi bro.');
					redirect('shipment/create');
				}
			}

			$payment_expired_at = NULL;
			if ($payment_type === 'TRANSFER') {
				$payment_expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
			}

			// Map Data Objek Induk Shipments (Tanpa kolom shipment_photo karena dipindah ke tracking)
			$insert_shipment = [
				'no_resi'                 => $no_resi,
				'agent_id'                => $sess['agent_id'] ?? NULL,
				'origin'                  => $origin,
				'destination'             => $destination,
				'service_type_id'         => $service_id,
				'category'                => $pricelist->category,
				'sender_name'             => $this->input->post('sender_name', TRUE),
				'sender_phone'            => $this->input->post('sender_phone', TRUE),
				'sender_provinsi'         => $sender_prov_name  ? $sender_prov_name->nama_provinsi  : NULL,
				'sender_kota'             => $sender_kota_name  ? $sender_kota_name->nama_kota      : NULL,
				'sender_kecamatan'        => $sender_kec_name   ? $sender_kec_name->nama_kecamatan  : NULL,
				'sender_kelurahan'        => $sender_kel_name   ? $sender_kel_name->nama_kelurahan  : NULL,
				'sender_address_detail'   => $this->input->post('sender_address_detail', TRUE),
				'sender_address'          => $full_sender_address,
				'receiver_name'           => $this->input->post('receiver_name', TRUE),
				'receiver_phone'          => $this->input->post('receiver_phone', TRUE),
				'receiver_provinsi'       => $receiver_prov_name  ? $receiver_prov_name->nama_provinsi  : NULL,
				'receiver_kota'           => $receiver_kota_name  ? $receiver_kota_name->nama_kota      : NULL,
				'receiver_kecamatan'      => $receiver_kec_name   ? $receiver_kec_name->nama_kecamatan  : NULL,
				'receiver_kelurahan'      => $receiver_kel_name   ? $receiver_kel_name->nama_kelurahan  : NULL,
				'receiver_address_detail' => $this->input->post('receiver_address_detail', TRUE),
				'receiver_address'        => $full_receiver_address,
				'commodity_id'            => $this->input->post('commodity_id', TRUE),
				'commodity_detail'        => $this->input->post('commodity_detail', TRUE),
				'is_valuable'             => $is_valuable,
				'goods_value'             => $is_valuable ? $this->_parse_indo_number($this->input->post('goods_value')) : 0,
				'payment_type'            => $payment_type,
				'koli'                    => $total_koli ?: 1,
				'actual_weight'           => $actual_weight,
				'volume_weight'           => $total_volume_weight,
				'chargeable_weight'       => $chargeable,
				'cost_price'              => $cost_per_kg,
				'sell_price'              => $sell_per_kg,
				'pickup_rate_id'          => $pickup_id,
				'pickup_fee'              => $pickup_fee,
				'total_addon_fee'         => $total_addon_fee,
				'total_amount'            => $shipping_total + $pickup_fee + $total_addon_fee,
				'margin_amount'           => $total_margin,
				'payment_expired_at'      => $payment_expired_at,
				'status'                  => 'BOOKED',
				'is_lartas_agreed'        => $this->input->post('is_lartas_agreed') ?? 1,
				'created_by'              => $sess['id']
			];

			// 8. Database Transaction START
			$this->db->trans_start();

			// Insert Induk Shipments Terlebih Dahulu
			$this->db->insert('shipments', $insert_shipment);
			$shipment_id = $this->db->insert_id();

			// Inject shipment_id yang baru lahir ke dalam array Dimensi
			if (!empty($insert_dimensions)) {
				foreach ($insert_dimensions as &$dim) {
					$dim['shipment_id'] = $shipment_id;
				}
				$this->db->insert_batch('shipment_dimensions', $insert_dimensions);
			}

			// Inject shipment_id yang baru lahir ke dalam array Addons
			if (!empty($insert_addons)) {
				foreach ($insert_addons as &$addon_row) {
					$addon_row['shipment_id'] = $shipment_id;
				}
				$this->db->insert_batch('shipment_addons', $insert_addons);
			}

			// INSIGHT PENYESUAIAN: Simpan Foto Awal Masuk ke tabel shipment_tracking (photo_proof)
			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => 'BOOKED',
				'location'    => $origin,
				'note'        => 'Shipment berhasil dibuat.',
				'photo_proof' => $photo_name, // Foto nempel di log BOOKED sekarang, bro!
				'created_by'  => $sess['id']
			]);

			$this->db->trans_complete();
			// Database Transaction END

			// ── Upsert Master Customer ──
			$customer_data = [
				'name'           => $this->input->post('sender_name', TRUE),
				'phone'          => $this->input->post('sender_phone', TRUE),
				'nik'            => $this->input->post('sender_nik', TRUE) ?: NULL,
				'provinsi_id'    => $this->input->post('sender_provinsi'),
				'provinsi_name'  => $sender_prov_name  ? $sender_prov_name->nama_provinsi  : NULL,
				'kota_id'        => $this->input->post('sender_kota'),
				'kota_name'      => $sender_kota_name  ? $sender_kota_name->nama_kota      : NULL,
				'kecamatan_id'   => $this->input->post('sender_kecamatan'),
				'kecamatan_name' => $sender_kec_name   ? $sender_kec_name->nama_kecamatan  : NULL,
				'kelurahan_id'   => $this->input->post('sender_kelurahan'),
				'kelurahan_name' => $sender_kel_name   ? $sender_kel_name->nama_kelurahan  : NULL,
				'address_detail' => $this->input->post('sender_address_detail', TRUE),
				'updated_at'     => date('Y-m-d H:i:s'),
				'created_by'     => $sess['id'],
			];

			$existing = $this->db->get_where('master_customers', ['phone' => $customer_data['phone']])->row();
			if ($existing) {
				$this->db->where('phone', $customer_data['phone'])->update('master_customers', $customer_data);
			} else {
				$this->db->insert('master_customers', $customer_data);
			}

			if ($this->db->trans_status() === FALSE) {
				// Cleanup file jika db transaction gagal
				if ($photo_name && file_exists(FCPATH . 'uploads/shipments/booking/' . $photo_name)) {
					unlink(FCPATH . 'uploads/shipments/booking/' . $photo_name);
				}
				$this->session->set_flashdata('error', 'Gagal menyimpan data.');
				redirect('shipment/create');
			} else {
				if ($payment_type === 'TRANSFER') {
					$url = base_url('home/confirm_payment/' . $no_resi);
					$total_rp = number_format($insert_shipment['total_amount'], 0, ',', '.');

					$pesan_user = "*SMESCO EXPRESS*\n\n" .
						"*RESERVASI BERHASIL*\n" .
						"---------------------\n\n" .
						"No. Resi: *$no_resi*\n" .
						"Atas Nama: *$sender_name*\n" .
						"Rute: $origin - $destination\n" .
						"Total Pembayaran: *Rp $total_rp*\n\n" .
						"📤 *Upload bukti transfer dalam 10 menit:*\n" .
						"$url\n\n" .
						"Lakukan pembayaran sebelum: *" . date('H:i', strtotime($payment_expired_at)) . " WIB*\n\n" .
						"_Terima kasih telah memilih Smesco Express_";

					try {
						$this->api_whatsapp->wa_notif_v2($sender_phone, $pesan_user);
					} catch (Exception $e) {
						log_message('error', 'WA Notif Error: ' . $e->getMessage());
					}
				}

				$this->session->set_flashdata('success', "Resi <b>$no_resi</b> berhasil dibuat!");
				redirect('shipment/detail/' . $shipment_id);
			}
			return;
		}

		// Tampilan Form (GET)
		$data = [
			'title'       => 'Buat Booking',
			'origins'     => $this->db->select('origin')->where('category', 'DOMESTIC')->group_by('origin')->get('pricelist')->result(),
			'services'    => $this->M_Pricelist->get_services('DOMESTIC'),
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result(),
			'addons'      => $this->db->get_where('master_addons', ['is_active' => 1])->result()
		];
		$this->render('app/pages/shipment/create', $data);
	}

	public function create_intl()
	{
		$this->_check_access();

		// Nanti lu butuh master data negara untuk dropdown tujuan internasional
		$data = [
			'title'     => 'Buat Booking (Internasional)',
			'origins'	  => $this->db->select('origin')->where('category', 'INTERNATIONAL')->group_by('origin')->get('pricelist')->result(),
			'cities'    => $this->M_Pricelist->get_cities(), // Asal (Origin)
			'countries' => $this->db->select('destination')->where('category', 'INTERNATIONAL')->get('pricelist')->result(), // Tujuan (Destination)
			'services'  => $this->M_Pricelist->get_services('INTERNATIONAL'),
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result(),
			'addons'    => $this->db->get_where('master_addons', ['is_active' => 1])->result()
		];

		$this->render('app/pages/shipment/create_intl', $data);
	}

	public function save_intl()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		// ── 1. Validasi & Ambil Data Pricelist ──
		$origin      = $this->input->post('origin', TRUE);
		$destination = $this->input->post('destination_country', TRUE);
		$service_id  = $this->input->post('service_type_id', TRUE);
		$sender_name  = $this->input->post('sender_name', TRUE);
		$sender_phone = $this->input->post('sender_phone', TRUE);
		$payment_type = $this->input->post('payment_type', TRUE);

		$pricelist = $this->db->get_where('pricelist', [
			'origin'          => $origin,
			'destination'     => $destination,
			'service_type_id' => $service_id,
			'is_active'       => 1
		])->row();

		if (!$pricelist) {
			$this->session->set_flashdata('error', 'Rute atau layanan internasional tidak tersedia!');
			redirect('shipment/create_intl');
		}

		// ── FIX BUG 1: GENERATE NO RESI DI AWAL AGAR BISA DIPAKAI UNTUK DIMENSI & FOTO ──
		$no_resi = $this->M_Shipment->generate_no_resi();

		// ── 2. Hitung Berat ──
		$actual_weight = $this->_parse_indo_number($this->input->post('actual_weight'));
		$dim_length = $this->input->post('dim_length');
		$dim_width  = $this->input->post('dim_width');
		$dim_height = $this->input->post('dim_height');
		$dim_qty    = $this->input->post('dim_qty');

		// FIX BUG 2: Inisialisasi awal variabel akumulasi volume kargo
		$insert_dimensions   = [];
		$total_volume_weight = 0;
		$total_koli          = 0;
		$koli_counter        = 1;

		if (!empty($dim_qty)) {
			foreach ($dim_qty as $key => $qty) {
				if ($qty > 0) {
					$total_koli += $qty;

					$p = str_replace(',', '.', $dim_length[$key]);
					$l = str_replace(',', '.', $dim_width[$key]);
					$t = str_replace(',', '.', $dim_height[$key]);

					$vol_weight_per_item = ($p * $l * $t) / 5000;
					$total_volume_weight += ($vol_weight_per_item * $qty);

					for ($i = 0; $i < $qty; $i++) {
						$barcode_koli = $no_resi . '-' . str_pad($koli_counter, 2, '0', STR_PAD_LEFT);

						$insert_dimensions[] = [
							'shipment_id'  => NULL,
							'barcode_koli' => $barcode_koli,
							'qty'          => 1,
							'length'       => $p,
							'width'        => $l,
							'height'       => $t
						];
						$koli_counter++;
					}
				}
			}
		}

		// ── 3. Chargeable Weight ──
		$chargeable = max($actual_weight, $total_volume_weight);
		if ($chargeable < $pricelist->min_weight_kg) {
			$chargeable = $pricelist->min_weight_kg;
		}
		$chargeable = ceil($chargeable);

		// ── 4. Harga & Tiered Pricing ──
		$cost_per_kg = $pricelist->harga_modal;
		$sell_per_kg = $pricelist->harga_jual;

		if ($pricelist->is_tiered == 1) {
			$tier = $this->db->where('pricelist_id', $pricelist->id)
				->where('min_weight <=', $chargeable)
				->where('max_weight >=', $chargeable)
				->get('pricelist_tiers')->row();

			if ($tier) {
				$cost_per_kg = $tier->harga_modal;
				$sell_per_kg = $tier->harga_jual;
			} else {
				$last_tier = $this->db->where('pricelist_id', $pricelist->id)
					->order_by('max_weight', 'DESC')
					->limit(1)
					->get('pricelist_tiers')->row();
				if ($last_tier) {
					$cost_per_kg = $last_tier->harga_modal;
					$sell_per_kg = $last_tier->harga_jual;
				}
			}
		}

		$shipping_total  = $chargeable * $sell_per_kg;
		$shipping_margin = ($sell_per_kg - $cost_per_kg) * $chargeable;

		// ── 5. Pickup Service ──
		$pickup_fee = 0;
		$pickup_id  = NULL;

		if ($this->input->post('use_pickup') == 1) {
			$p_id    = $this->input->post('pickup_rate_id');
			$rate_db = $this->db->get_where('master_pickup_rates', ['id' => $p_id])->row();

			if ($rate_db && $chargeable >= $rate_db->min_weight) {
				$pickup_id  = $p_id;
				$pickup_fee = $rate_db->harga_jual;
			}
		}

		// ── 6. Addon Calculation ──
		$addon_codes     = $this->input->post('addons') ?: [];
		$total_addon_fee = 0;
		$insert_addons   = [];

		if (!empty($addon_codes)) {
			$this->db->where_in('code', $addon_codes)->where('is_active', 1);
			$selected_addons = $this->db->get('master_addons')->result();

			foreach ($selected_addons as $addon) {
				$fee = 0;

				if (!empty($insert_dimensions)) {
					foreach ($insert_dimensions as $dim) {
						$p = $dim['length'];
						$l = $dim['width'];
						$t = $dim['height'];
						$q = $dim['qty'];

						if ($addon->calc_method === 'VOLUME') {
							$fee += ($p * $l * $t * $addon->base_factor) * $q;
						} elseif ($addon->calc_method === 'VOLUME_PLUS') {
							$fee += (($p + 10) * ($l + 10) * ($t + 10) * $addon->base_factor) * $q;
						} elseif ($addon->calc_method === 'PER_KOLI') {
							$fee += $addon->base_factor * $q;
						}
					}
				} else {
					if ($addon->calc_method === 'PER_KOLI') {
						$fee = $addon->base_factor * ($total_koli ?: 1);
					}
				}

				if ($fee > 0) {
					$total_addon_fee += $fee;
					$insert_addons[] = [
						'shipment_id'  => NULL, // Di-inject nanti
						'addon_id'     => $addon->id,
						'addon_amount' => $fee,
					];
				}
			}
		}

		$total_margin = $shipping_margin;

		// ── 7. Resolve Nama Wilayah Pengirim ──
		$sender_prov_name = $this->db->get_where('mt_provinsi',   ['id' => $this->input->post('sender_provinsi')])->row();
		$sender_kota_name = $this->db->get_where('mt_kota',       ['id' => $this->input->post('sender_kota')])->row();
		$sender_kec_name  = $this->db->get_where('mt_kecamatan', ['id' => $this->input->post('sender_kecamatan')])->row();
		$sender_kel_name  = $this->db->get_where('mt_kelurahan', ['id' => $this->input->post('sender_kelurahan')])->row();

		$full_sender_address = implode(', ', array_filter([
			$this->input->post('sender_address_detail', TRUE),
			$sender_kel_name  ? $sender_kel_name->nama_kelurahan                   : '',
			$sender_kec_name  ? 'Kec. '      . $sender_kec_name->nama_kecamatan   : '',
			$sender_kota_name ? 'Kab/Kota '  . $sender_kota_name->nama_kota       : '',
			$sender_prov_name ? $sender_prov_name->nama_provinsi                   : '',
		]));

		// Penerima internasional — tidak pakai cascade wilayah, langsung teks
		$full_receiver_address = implode(', ', array_filter([
			$this->input->post('receiver_address_detail', TRUE),
			$this->input->post('receiver_city', TRUE),
			$this->input->post('receiver_zipcode', TRUE),
			$destination,
		]));

		// ── 8. Upload Foto (Standardisasi Lintasan Folder) ──
		$photo_name      = NULL;
		$pending_upload  = [];

		if (isset($_FILES['shipment_photo']) && $_FILES['shipment_photo']['error'] === UPLOAD_ERR_OK) {
			$file    = $_FILES['shipment_photo'];
			$allowed = ['image/jpeg', 'image/jpg', 'image/png'];

			if (!in_array($file['type'], $allowed)) {
				$this->session->set_flashdata('error', 'Format foto tidak valid.');
				redirect('shipment/create_intl');
			}

			if ($file['size'] > 2 * 1024 * 1024) {
				$this->session->set_flashdata('error', 'Ukuran foto maksimal 2MB.');
				redirect('shipment/create_intl');
			}

			$pending_upload = [
				'tmp_name' => $file['tmp_name'],
				'ext'      => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
			];
		} else {
			$this->session->set_flashdata('error', 'Foto barang wajib diupload untuk pengiriman internasional!');
			redirect('shipment/create_intl');
		}

		// ── 10. Eksekusi Upload Foto ke Folder /booking/ ──
		if (!empty($pending_upload)) {
			$upload_dir = FCPATH . 'uploads/shipments/booking/';

			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0755, TRUE);
			}

			$photo_name = 'BOOKING_' . $no_resi . '_' . time() . '.' . $pending_upload['ext'];
			$full_path  = $upload_dir . $photo_name;

			if (!move_uploaded_file($pending_upload['tmp_name'], $full_path)) {
				$this->session->set_flashdata('error', 'Gagal mengupload foto kargo awal. Coba lagi bro.');
				redirect('shipment/create_intl');
			}
		}

		// ── 11. Payment Expired ──
		$payment_expired_at = NULL;
		if ($payment_type === 'TRANSFER') {
			$payment_expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
		}

		// ── 12. Susun Data Insert Shipment ──
		$insert_shipment = [
			'no_resi'                 => $no_resi,
			'agent_id'                => $sess['agent_id'] ?? NULL,
			'category'                => 'INTERNATIONAL',
			'origin'                  => $origin,
			'destination'             => $destination,
			'service_type_id'         => $service_id,

			// Barang & Bea Cukai
			'commodity_id'            => $this->input->post('commodity_id', TRUE),
			'commodity_detail'        => $this->input->post('commodity_detail', TRUE),
			'commodity_detail_en'     => $this->input->post('commodity_detail_en', TRUE),
			'customs_value_usd'       => $this->_parse_indo_number($this->input->post('customs_value_usd')),
			'payment_type'            => $payment_type,

			// Pengirim
			'sender_name'             => $sender_name,
			'sender_phone'            => $sender_phone,
			'sender_nik'              => $this->input->post('sender_nik', TRUE),
			'sender_provinsi'         => $sender_prov_name ? $sender_prov_name->nama_provinsi : NULL,
			'sender_kota'             => $sender_kota_name ? $sender_kota_name->nama_kota     : NULL,
			'sender_kecamatan'        => $sender_kec_name  ? $sender_kec_name->nama_kecamatan : NULL,
			'sender_kelurahan'        => $sender_kel_name  ? $sender_kel_name->nama_kelurahan : NULL,
			'sender_address_detail'   => $this->input->post('sender_address_detail', TRUE),
			'sender_address'          => $full_sender_address,

			// Penerima Internasional (tanpa cascade wilayah)
			'receiver_name'           => $this->input->post('receiver_name', TRUE),
			'receiver_phone'          => $this->input->post('receiver_phone', TRUE),
			'receiver_kota'           => $this->input->post('receiver_city', TRUE),
			'receiver_zipcode'        => $this->input->post('receiver_zipcode', TRUE),
			'receiver_address_detail' => $this->input->post('receiver_address_detail', TRUE),
			'receiver_address'        => $full_receiver_address,

			// Berat & Harga
			'koli'                    => $total_koli ?: 1,
			'actual_weight'           => $actual_weight,
			'volume_weight'           => $total_volume_weight,
			'chargeable_weight'       => $chargeable,
			'cost_price'              => $cost_per_kg,
			'sell_price'              => $sell_per_kg,
			'price_per_kg'            => $sell_per_kg,
			'pickup_rate_id'          => $pickup_id,
			'pickup_fee'              => $pickup_fee,
			'total_addon_fee'         => $total_addon_fee,
			'total_amount'            => $shipping_total + $pickup_fee + $total_addon_fee,
			'margin_amount'           => $total_margin,

			// Misc
			'is_valuable'             => 0,
			'goods_value'             => 0,
			'is_lartas_agreed'        => $this->input->post('is_lartas_agreed') ?? 1,
			'payment_expired_at'      => $payment_expired_at,
			'status'                  => 'BOOKED',
			'created_by'              => $sess['id'],
			'created_at'              => date('Y-m-d H:i:s'),
		];

		// ── 13. Database Transaction START ──
		$this->db->trans_begin();

		$this->db->insert('shipments', $insert_shipment);
		$shipment_id = $this->db->insert_id();

		// Inject ID & Insert Dimensi
		if (!empty($insert_dimensions)) {
			foreach ($insert_dimensions as &$dim) {
				$dim['shipment_id'] = $shipment_id;
			}
			$this->db->insert_batch('shipment_dimensions', $insert_dimensions);
		}

		// Inject ID & Insert Addon
		if (!empty($insert_addons)) {
			foreach ($insert_addons as &$addon_row) {
				$addon_row['shipment_id'] = $shipment_id;
			}
			$this->db->insert_batch('shipment_addons', $insert_addons);
		}

		// FIX LOGIKA TRACKING FOTO: Tempel nama file booking di kolom photo_proof
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $shipment_id,
			'status'      => 'BOOKED',
			'location'    => $origin,
			'note'        => 'Shipment internasional berhasil dibuat.',
			'photo_proof' => $photo_name,
			'created_by'  => $sess['id'],
		]);

		// ── 14. Upsert Master Customer ──
		$customer_data = [
			'name'           => $sender_name,
			'phone'          => $sender_phone,
			'nik'            => $this->input->post('sender_nik', TRUE) ?: NULL,
			'provinsi_id'    => $this->input->post('sender_provinsi'),
			'provinsi_name'  => $sender_prov_name ? $sender_prov_name->nama_provinsi : NULL,
			'kota_id'        => $this->input->post('sender_kota'),
			'kota_name'      => $sender_kota_name ? $sender_kota_name->nama_kota     : NULL,
			'kecamatan_id'   => $this->input->post('sender_kecamatan'),
			'kecamatan_name' => $sender_kec_name  ? $sender_kec_name->nama_kecamatan : NULL,
			'kelurahan_id'   => $this->input->post('sender_kelurahan'),
			'kelurahan_name' => $sender_kel_name  ? $sender_kel_name->nama_kelurahan : NULL,
			'address_detail' => $this->input->post('sender_address_detail', TRUE),
			'updated_at'     => date('Y-m-d H:i:s'),
			'created_by'     => $sess['id'],
		];

		$existing = $this->db->get_where('master_customers', ['phone' => $sender_phone])->row();
		if ($existing) {
			$this->db->where('phone', $sender_phone)->update('master_customers', $customer_data);
		} else {
			$this->db->insert('master_customers', $customer_data);
		}

		// ── 15. Response & Transaction Check ──
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			// Rollback file fisik di storage jika database bermasalah
			if ($photo_name && file_exists(FCPATH . 'uploads/shipments/booking/' . $photo_name)) {
				unlink(FCPATH . 'uploads/shipments/booking/' . $photo_name);
			}
			$this->session->set_flashdata('error', 'Gagal menyimpan data booking internasional!');
			redirect('shipment/create_intl');
		} else {
			$this->db->trans_commit(); // Sukses total, kunci data

			if ($payment_type === 'TRANSFER') {
				$url      = base_url('home/confirm_payment/' . $no_resi);
				$total_rp = number_format($shipping_total + $pickup_fee + $total_addon_fee, 0, ',', '.');

				$pesan_user = "*SMESCO EXPRESS*\n\n" .
					"*RESERVASI BERHASIL*\n" .
					"---------------------\n\n" .
					"No. Resi: *{$no_resi}*\n" .
					"Atas Nama: *{$sender_name}*\n" .
					"Rute: {$origin} → {$destination}\n" .
					"Total Pembayaran: *Rp {$total_rp}*\n\n" .
					"📤 *Upload bukti transfer dalam 10 menit:*\n" .
					"{$url}\n\n" .
					"Lakukan pembayaran sebelum: *" . date('H:i', strtotime($payment_expired_at)) . " WIB*\n\n" .
					"_Terima kasih telah memilih Smesco Express_";

				try {
					$this->api_whatsapp->wa_notif_v2($sender_phone, $pesan_user);
				} catch (Exception $e) {
					log_message('error', 'WA Notif Error (INTL): ' . $e->getMessage());
				}
			}

			$this->session->set_flashdata('success', 'Booking Internasional berhasil! No Resi: ' . $no_resi);
			redirect('shipment/detail/' . $shipment_id);
		}
	}

	// ============================================================
	// EDIT DOMESTIK
	// ============================================================
	public function edit($id)
	{
		$this->_check_access();

		$shipment = $this->db->get_where('shipments', ['id' => $id])->row();

		if (!$shipment) {
			$this->session->set_flashdata('error', 'Shipment tidak ditemukan!');
			redirect('shipment');
		}

		if ($shipment->status !== 'BOOKED') {
			$this->session->set_flashdata('error', 'Shipment tidak bisa diedit karena statusnya sudah ' . $shipment->status);
			redirect('shipment/detail/' . $id);
		}

		// Load dimensi & addons yang sudah ada
		$dimensions = $this->db->get_where('shipment_dimensions', ['shipment_id' => $id])->result();
		$addons     = $this->db->get_where('shipment_addons', ['shipment_id' => $id])->result_array();
		$addon_ids  = array_column($addons, 'addon_id');

		$data = [
			'title'       => 'Edit Shipment #' . $shipment->no_resi,
			'shipment'    => $shipment,
			'dimensions'  => $dimensions,
			'addon_ids'   => $addon_ids,
			'origins'     => $this->db->select('origin')->where('category', 'DOMESTIC')->group_by('origin')->get('pricelist')->result(),
			'services'    => $this->M_Pricelist->get_services('DOMESTIC'),
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result(),
			'addons'      => $this->db->get_where('master_addons', ['is_active' => 1])->result(),
		];

		$this->render('app/pages/shipment/edit', $data);
	}

	public function save_edit($id)
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$shipment = $this->db->get_where('shipments', ['id' => $id])->row();

		if (!$shipment || $shipment->status !== 'BOOKED') {
			$this->session->set_flashdata('error', 'Shipment tidak valid atau tidak bisa diedit!');
			redirect('shipment');
		}

		// ── 1. Input Dasar ──
		$origin      = $this->input->post('origin', TRUE);
		$destination = $this->input->post('destination', TRUE);
		$service_id  = $this->input->post('service_type_id', TRUE);
		$sender_name  = $this->input->post('sender_name', TRUE);
		$sender_phone = $this->input->post('sender_phone', TRUE);
		$payment_type = $this->input->post('payment_type', TRUE);

		// ── 2. Validasi Pricelist ──
		$pricelist = $this->db->get_where('pricelist', [
			'origin'          => $origin,
			'destination'     => $destination,
			'service_type_id' => $service_id,
			'is_active'       => 1
		])->row();

		if (!$pricelist) {
			$this->session->set_flashdata('error', 'Rute atau layanan tidak tersedia!');
			redirect('shipment/edit/' . $id);
		}

		// ── 3. Hitung Berat & Dimensi ──
		$actual_weight = $this->_parse_indo_number($this->input->post('actual_weight'));
		$dim_length    = $this->input->post('dim_length');
		$dim_width     = $this->input->post('dim_width');
		$dim_height    = $this->input->post('dim_height');
		$dim_qty       = $this->input->post('dim_qty');

		$insert_dimensions   = [];
		$total_volume_weight = 0;
		$total_koli          = 0;
		$koli_counter        = 1;

		if (!empty($dim_qty)) {
			foreach ($dim_qty as $key => $qty) {
				if ($qty > 0) {
					$total_koli += $qty;
					$p = str_replace(',', '.', $dim_length[$key]);
					$l = str_replace(',', '.', $dim_width[$key]);
					$t = str_replace(',', '.', $dim_height[$key]);

					$vol_weight_per_item = ($p * $l * $t) / 5000;
					$total_volume_weight += ($vol_weight_per_item * $qty);

					for ($i = 0; $i < $qty; $i++) {
						$barcode_koli = $shipment->no_resi . '-' . str_pad($koli_counter, 2, '0', STR_PAD_LEFT);
						$insert_dimensions[] = [
							'shipment_id'  => $id,
							'barcode_koli' => $barcode_koli,
							'qty'          => 1,
							'length'       => $p,
							'width'        => $l,
							'height'       => $t,
						];
						$koli_counter++;
					}
				}
			}
		}

		// ── 4. Chargeable Weight ──
		$chargeable = max($actual_weight, $total_volume_weight);
		if ($chargeable < $pricelist->min_weight_kg) {
			$chargeable = $pricelist->min_weight_kg;
		}
		$chargeable = ceil($chargeable);

		// ── 5. Harga & Tiered ──
		$cost_per_kg = $pricelist->price_kribo;
		$sell_per_kg = $pricelist->price_smesco;

		if ($pricelist->is_tiered == 1) {
			$tier = $this->db->where('pricelist_id', $pricelist->id)
				->where('min_weight <=', $chargeable)
				->where('max_weight >=', $chargeable)
				->get('pricelist_tiers')->row();
			if ($tier) {
				$cost_per_kg = $tier->price_kribo;
				$sell_per_kg = $tier->price_smesco;
			} else {
				$last_tier = $this->db->where('pricelist_id', $pricelist->id)
					->order_by('max_weight', 'DESC')->limit(1)->get('pricelist_tiers')->row();
				if ($last_tier) {
					$cost_per_kg = $last_tier->price_kribo;
					$sell_per_kg = $last_tier->price_smesco;
				}
			}
		}

		$shipping_total  = $chargeable * $sell_per_kg;
		$shipping_margin = ($sell_per_kg - $cost_per_kg) * $chargeable;

		// ── 6. Pickup ──
		$pickup_fee = 0;
		$pickup_id  = NULL;

		if ($this->input->post('use_pickup') == 1) {
			$p_id    = $this->input->post('pickup_rate_id');
			$rate_db = $this->db->get_where('master_pickup_rates', ['id' => $p_id])->row();
			if ($rate_db && $chargeable >= $rate_db->min_weight) {
				$pickup_id  = $p_id;
				$pickup_fee = $rate_db->price_smesco;
			}
		}

		// ── 7. Addons ──
		$addon_codes     = $this->input->post('addons') ?: [];
		$total_addon_fee = 0;
		$insert_addons   = [];

		if (!empty($addon_codes)) {
			$this->db->where_in('code', $addon_codes)->where('is_active', 1);
			$selected_addons = $this->db->get('master_addons')->result();

			foreach ($selected_addons as $addon) {
				$fee = 0;
				if (!empty($insert_dimensions)) {
					foreach ($insert_dimensions as $dim) {
						$p   = $dim['length'];
						$l   = $dim['width'];
						$t   = $dim['height'];
						$q   = $dim['qty'];
						$min = $addon->min_charge;

						if ($addon->calc_method === 'VOLUME') {
							$fee += max($p * $l * $t * $addon->base_factor, $min) * $q;
						} elseif ($addon->calc_method === 'VOLUME_PLUS') {
							$fee += max(($p + 10) * ($l + 10) * ($t + 10) * $addon->base_factor, $min) * $q;
						} elseif ($addon->calc_method === 'PER_KOLI') {
							$fee += max($addon->base_factor, $min) * $q;
						}
					}
				} else {
					if ($addon->calc_method === 'PER_KOLI') {
						$fee = $addon->base_factor * ($total_koli ?: 1);
					}
				}

				if ($fee > 0) {
					$total_addon_fee += $fee;
					$insert_addons[] = [
						'shipment_id'  => $id,
						'addon_id'     => $addon->id,
						'addon_amount' => $fee,
					];
				}
			}
		}

		// ── 8. Resolve Nama Wilayah ──
		$sender_prov_name  = $this->db->get_where('mt_provinsi',  ['id' => $this->input->post('sender_provinsi')])->row();
		$sender_kota_name  = $this->db->get_where('mt_kota',      ['id' => $this->input->post('sender_kota')])->row();
		$sender_kec_name   = $this->db->get_where('mt_kecamatan', ['id' => $this->input->post('sender_kecamatan')])->row();
		$sender_kel_name   = $this->db->get_where('mt_kelurahan', ['id' => $this->input->post('sender_kelurahan')])->row();

		$receiver_prov_name = $this->db->get_where('mt_provinsi',  ['id' => $this->input->post('receiver_provinsi')])->row();
		$receiver_kota_name = $this->db->get_where('mt_kota',      ['id' => $this->input->post('receiver_kota')])->row();
		$receiver_kec_name  = $this->db->get_where('mt_kecamatan', ['id' => $this->input->post('receiver_kecamatan')])->row();
		$receiver_kel_name  = $this->db->get_where('mt_kelurahan', ['id' => $this->input->post('receiver_kelurahan')])->row();

		$full_sender_address = implode(', ', array_filter([
			$this->input->post('sender_address_detail', TRUE),
			$sender_kel_name  ? $sender_kel_name->nama_kelurahan                 : '',
			$sender_kec_name  ? 'Kec. '     . $sender_kec_name->nama_kecamatan  : '',
			$sender_kota_name ? 'Kab/Kota ' . $sender_kota_name->nama_kota      : '',
			$sender_prov_name ? $sender_prov_name->nama_provinsi                 : '',
		]));

		$full_receiver_address = implode(', ', array_filter([
			$this->input->post('receiver_address_detail', TRUE),
			$receiver_kel_name  ? $receiver_kel_name->nama_kelurahan                   : '',
			$receiver_kec_name  ? 'Kec. '     . $receiver_kec_name->nama_kecamatan    : '',
			$receiver_kota_name ? 'Kab/Kota ' . $receiver_kota_name->nama_kota        : '',
			$receiver_prov_name ? $receiver_prov_name->nama_provinsi                   : '',
		]));

		// ── 9. Upload Foto (opsional, kalau tidak diupload pakai yang lama) ──
		$photo_path = $shipment->shipment_photo; // default: foto lama

		if (isset($_FILES['shipment_photo']) && $_FILES['shipment_photo']['error'] === UPLOAD_ERR_OK) {
			$file    = $_FILES['shipment_photo'];
			$allowed = ['image/jpeg', 'image/jpg', 'image/png'];

			if (!in_array($file['type'], $allowed)) {
				$this->session->set_flashdata('error', 'Format foto tidak valid.');
				redirect('shipment/edit/' . $id);
			}
			if ($file['size'] > 2 * 1024 * 1024) {
				$this->session->set_flashdata('error', 'Ukuran foto maksimal 2MB.');
				redirect('shipment/edit/' . $id);
			}

			$upload_dir = FCPATH . 'uploads/shipments/' . date('Y/m/');
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0755, TRUE);
			}

			$ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$file_name = $shipment->no_resi . '_photo_' . time() . '.' . $ext;
			$full_path = $upload_dir . $file_name;

			if (move_uploaded_file($file['tmp_name'], $full_path)) {
				// Hapus foto lama kalau ada
				if ($shipment->shipment_photo && file_exists(FCPATH . $shipment->shipment_photo)) {
					unlink(FCPATH . $shipment->shipment_photo);
				}
				$photo_path = 'uploads/shipments/' . date('Y/m/') . $file_name;
			}
		}

		// ── 10. Payment Expired (reset kalau payment_type berubah ke TRANSFER) ──
		$payment_expired_at = $shipment->payment_expired_at;
		if ($payment_type === 'TRANSFER' && $shipment->payment_type !== 'TRANSFER') {
			$payment_expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
		} elseif ($payment_type === 'CASH') {
			$payment_expired_at = NULL;
		}

		// ── 11. Susun Data Update ──
		$is_valuable = $this->input->post('is_valuable') ? 1 : 0;

		$update_shipment = [
			'origin'                  => $origin,
			'destination'             => $destination,
			'service_type_id'         => $service_id,
			'category'                => $pricelist->category,
			'sender_name'             => $sender_name,
			'sender_phone'            => $sender_phone,
			'sender_provinsi'         => $sender_prov_name  ? $sender_prov_name->nama_provinsi  : NULL,
			'sender_kota'             => $sender_kota_name  ? $sender_kota_name->nama_kota      : NULL,
			'sender_kecamatan'        => $sender_kec_name   ? $sender_kec_name->nama_kecamatan  : NULL,
			'sender_kelurahan'        => $sender_kel_name   ? $sender_kel_name->nama_kelurahan  : NULL,
			'sender_address_detail'   => $this->input->post('sender_address_detail', TRUE),
			'sender_address'          => $full_sender_address,
			'receiver_name'           => $this->input->post('receiver_name', TRUE),
			'receiver_phone'          => $this->input->post('receiver_phone', TRUE),
			'receiver_provinsi'       => $receiver_prov_name ? $receiver_prov_name->nama_provinsi : NULL,
			'receiver_kota'           => $receiver_kota_name ? $receiver_kota_name->nama_kota     : NULL,
			'receiver_kecamatan'      => $receiver_kec_name  ? $receiver_kec_name->nama_kecamatan : NULL,
			'receiver_kelurahan'      => $receiver_kel_name  ? $receiver_kel_name->nama_kelurahan : NULL,
			'receiver_address_detail' => $this->input->post('receiver_address_detail', TRUE),
			'receiver_address'        => $full_receiver_address,
			'commodity_id'            => $this->input->post('commodity_id', TRUE),
			'commodity_detail'        => $this->input->post('commodity_detail', TRUE),
			'is_valuable'             => $is_valuable,
			'goods_value'             => $is_valuable ? $this->_parse_indo_number($this->input->post('goods_value')) : 0,
			'payment_type'            => $payment_type,
			'koli'                    => $total_koli ?: 1,
			'actual_weight'           => $actual_weight,
			'volume_weight'           => $total_volume_weight,
			'chargeable_weight'       => $chargeable,
			'cost_price'              => $cost_per_kg,
			'sell_price'              => $sell_per_kg,
			'pickup_rate_id'          => $pickup_id,
			'pickup_fee'              => $pickup_fee,
			'total_addon_fee'         => $total_addon_fee,
			'total_amount'            => $shipping_total + $pickup_fee + $total_addon_fee,
			'margin_amount'           => $shipping_margin,
			'shipment_photo'          => $photo_path,
			'payment_expired_at'      => $payment_expired_at,
			'updated_by'              => $sess['id'],
			'updated_at'              => date('Y-m-d H:i:s'),
		];

		// ── 12. Transaction ──
		$this->db->trans_start();

		$this->db->where('id', $id)->update('shipments', $update_shipment);

		// Replace dimensi lama dengan yang baru
		$this->db->where('shipment_id', $id)->delete('shipment_dimensions');
		if (!empty($insert_dimensions)) {
			$this->db->insert_batch('shipment_dimensions', $insert_dimensions);
		}

		// Replace addons lama dengan yang baru
		$this->db->where('shipment_id', $id)->delete('shipment_addons');
		if (!empty($insert_addons)) {
			$this->db->insert_batch('shipment_addons', $insert_addons);
		}

		// Tracking log perubahan
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $id,
			'status'      => 'BOOKED',
			'location'    => $origin,
			'note'        => 'Data shipment diperbarui oleh petugas. Total: Rp ' . number_format($shipping_total + $pickup_fee + $total_addon_fee, 0, ',', '.'),
			'created_by'  => $sess['id'],
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal menyimpan perubahan!');
			redirect('shipment/edit/' . $id);
		} else {
			$this->session->set_flashdata('success', 'Shipment <b>' . $shipment->no_resi . '</b> berhasil diperbarui!');
			redirect('shipment/detail/' . $id);
		}
	}

	// ============================================================
	// EDIT INTERNASIONAL
	// ============================================================
	public function edit_intl($id)
	{
		$this->_check_access();

		$shipment = $this->db->get_where('shipments', ['id' => $id, 'category' => 'INTERNATIONAL'])->row();

		if (!$shipment) {
			$this->session->set_flashdata('error', 'Shipment tidak ditemukan!');
			redirect('shipment');
		}

		if ($shipment->status !== 'BOOKED') {
			$this->session->set_flashdata('error', 'Shipment tidak bisa diedit karena statusnya sudah ' . $shipment->status);
			redirect('shipment/detail/' . $id);
		}

		$dimensions = $this->db->get_where('shipment_dimensions', ['shipment_id' => $id])->result();
		$addons     = $this->db->get_where('shipment_addons', ['shipment_id' => $id])->result_array();
		$addon_ids  = array_column($addons, 'addon_id');

		$data = [
			'title'       => 'Edit Shipment Internasional #' . $shipment->no_resi,
			'shipment'    => $shipment,
			'dimensions'  => $dimensions,
			'addon_ids'   => $addon_ids,
			'origins'     => $this->db->select('origin')->where('category', 'INTERNATIONAL')->group_by('origin')->get('pricelist')->result(),
			'services'    => $this->M_Pricelist->get_services('INTERNATIONAL'),
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result(),
			'addons'      => $this->db->get_where('master_addons', ['is_active' => 1])->result(),
			'countries'   => $this->db->select('destination')->where('category', 'INTERNATIONAL')->get('pricelist')->result(),
		];

		$this->render('app/pages/shipment/edit_intl', $data);
	}

	public function save_edit_intl($id)
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$shipment = $this->db->get_where('shipments', ['id' => $id, 'category' => 'INTERNATIONAL'])->row();

		if (!$shipment || $shipment->status !== 'BOOKED') {
			$this->session->set_flashdata('error', 'Shipment tidak valid atau tidak bisa diedit!');
			redirect('shipment');
		}

		$origin      = $this->input->post('origin', TRUE);
		$destination = $this->input->post('destination_country', TRUE);
		$service_id  = $this->input->post('service_type_id', TRUE);
		$sender_name  = $this->input->post('sender_name', TRUE);
		$sender_phone = $this->input->post('sender_phone', TRUE);
		$payment_type = $this->input->post('payment_type', TRUE);

		$pricelist = $this->db->get_where('pricelist', [
			'origin'          => $origin,
			'destination'     => $destination,
			'service_type_id' => $service_id,
			'is_active'       => 1
		])->row();

		if (!$pricelist) {
			$this->session->set_flashdata('error', 'Rute atau layanan tidak tersedia!');
			redirect('shipment/edit_intl/' . $id);
		}

		$actual_weight = $this->_parse_indo_number($this->input->post('actual_weight'));
		$dim_length    = $this->input->post('dim_length');
		$dim_width     = $this->input->post('dim_width');
		$dim_height    = $this->input->post('dim_height');
		$dim_qty       = $this->input->post('dim_qty');

		$insert_dimensions   = [];
		$total_volume_weight = 0;
		$total_koli          = 0;
		$koli_counter        = 1;

		if (!empty($dim_qty)) {
			foreach ($dim_qty as $key => $qty) {
				if ($qty > 0) {
					$total_koli += $qty;
					$p = str_replace(',', '.', $dim_length[$key]);
					$l = str_replace(',', '.', $dim_width[$key]);
					$t = str_replace(',', '.', $dim_height[$key]);

					$total_volume_weight += (($p * $l * $t) / 5000) * $qty;

					for ($i = 0; $i < $qty; $i++) {
						$barcode_koli = $shipment->no_resi . '-' . str_pad($koli_counter, 2, '0', STR_PAD_LEFT);
						$insert_dimensions[] = [
							'shipment_id'  => $id,
							'barcode_koli' => $barcode_koli,
							'qty'          => 1,
							'length'       => $p,
							'width'        => $l,
							'height'       => $t,
						];
						$koli_counter++;
					}
				}
			}
		}

		$chargeable = max($actual_weight, $total_volume_weight);
		if ($chargeable < $pricelist->min_weight_kg) $chargeable = $pricelist->min_weight_kg;
		$chargeable = ceil($chargeable);

		$cost_per_kg = $pricelist->harga_modal;
		$sell_per_kg = $pricelist->harga_jual;

		if ($pricelist->is_tiered == 1) {
			$tier = $this->db->where('pricelist_id', $pricelist->id)
				->where('min_weight <=', $chargeable)
				->where('max_weight >=', $chargeable)
				->get('pricelist_tiers')->row();
			if ($tier) {
				$cost_per_kg = $tier->harga_modal;
				$sell_per_kg = $tier->harga_jual;
			} else {
				$last_tier = $this->db->where('pricelist_id', $pricelist->id)
					->order_by('max_weight', 'DESC')->limit(1)->get('pricelist_tiers')->row();
				if ($last_tier) {
					$cost_per_kg = $last_tier->harga_modal;
					$sell_per_kg = $last_tier->harga_jual;
				}
			}
		}

		$shipping_total  = $chargeable * $sell_per_kg;
		$shipping_margin = ($sell_per_kg - $cost_per_kg) * $chargeable;

		$pickup_fee = 0;
		$pickup_id  = NULL;
		if ($this->input->post('use_pickup') == 1) {
			$p_id    = $this->input->post('pickup_rate_id');
			$rate_db = $this->db->get_where('master_pickup_rates', ['id' => $p_id])->row();
			if ($rate_db && $chargeable >= $rate_db->min_weight) {
				$pickup_id  = $p_id;
				$pickup_fee = $rate_db->harga_jual;
			}
		}

		$addon_codes     = $this->input->post('addons') ?: [];
		$total_addon_fee = 0;
		$insert_addons   = [];

		if (!empty($addon_codes)) {
			$this->db->where_in('code', $addon_codes)->where('is_active', 1);
			$selected_addons = $this->db->get('master_addons')->result();

			foreach ($selected_addons as $addon) {
				$fee = 0;
				if (!empty($insert_dimensions)) {
					foreach ($insert_dimensions as $dim) {
						$p = $dim['length'];
						$l = $dim['width'];
						$t = $dim['height'];
						$q = $dim['qty'];
						if ($addon->calc_method === 'VOLUME')       $fee += ($p * $l * $t * $addon->base_factor) * $q;
						elseif ($addon->calc_method === 'VOLUME_PLUS') $fee += (($p + 10) * ($l + 10) * ($t + 10) * $addon->base_factor) * $q;
						elseif ($addon->calc_method === 'PER_KOLI')  $fee += $addon->base_factor * $q;
					}
				} elseif ($addon->calc_method === 'PER_KOLI') {
					$fee = $addon->base_factor * ($total_koli ?: 1);
				}

				if ($fee > 0) {
					$total_addon_fee += $fee;
					$insert_addons[] = ['shipment_id' => $id, 'addon_id' => $addon->id, 'addon_amount' => $fee];
				}
			}
		}

		$sender_prov_name = $this->db->get_where('mt_provinsi',  ['id' => $this->input->post('sender_provinsi')])->row();
		$sender_kota_name = $this->db->get_where('mt_kota',      ['id' => $this->input->post('sender_kota')])->row();
		$sender_kec_name  = $this->db->get_where('mt_kecamatan', ['id' => $this->input->post('sender_kecamatan')])->row();
		$sender_kel_name  = $this->db->get_where('mt_kelurahan', ['id' => $this->input->post('sender_kelurahan')])->row();

		$full_sender_address = implode(', ', array_filter([
			$this->input->post('sender_address_detail', TRUE),
			$sender_kel_name  ? $sender_kel_name->nama_kelurahan                 : '',
			$sender_kec_name  ? 'Kec. '     . $sender_kec_name->nama_kecamatan  : '',
			$sender_kota_name ? 'Kab/Kota ' . $sender_kota_name->nama_kota      : '',
			$sender_prov_name ? $sender_prov_name->nama_provinsi                 : '',
		]));

		$full_receiver_address = implode(', ', array_filter([
			$this->input->post('receiver_address_detail', TRUE),
			$this->input->post('receiver_city', TRUE),
			$this->input->post('receiver_zipcode', TRUE),
			$destination,
		]));

		// Foto — pakai lama kalau tidak diupload baru
		$photo_path = $shipment->shipment_photo;
		if (isset($_FILES['shipment_photo']) && $_FILES['shipment_photo']['error'] === UPLOAD_ERR_OK) {
			$file    = $_FILES['shipment_photo'];
			$allowed = ['image/jpeg', 'image/jpg', 'image/png'];
			if (!in_array($file['type'], $allowed)) {
				$this->session->set_flashdata('error', 'Format foto tidak valid.');
				redirect('shipment/edit_intl/' . $id);
			}
			if ($file['size'] > 2 * 1024 * 1024) {
				$this->session->set_flashdata('error', 'Ukuran foto maksimal 2MB.');
				redirect('shipment/edit_intl/' . $id);
			}
			$upload_dir = FCPATH . 'uploads/shipments/' . date('Y/m/');
			if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, TRUE);
			$ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$file_name = $shipment->no_resi . '_photo_' . time() . '.' . $ext;
			if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
				if ($shipment->shipment_photo && file_exists(FCPATH . $shipment->shipment_photo)) {
					unlink(FCPATH . $shipment->shipment_photo);
				}
				$photo_path = 'uploads/shipments/' . date('Y/m/') . $file_name;
			}
		}

		$payment_expired_at = $shipment->payment_expired_at;
		if ($payment_type === 'TRANSFER' && $shipment->payment_type !== 'TRANSFER') {
			$payment_expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
		} elseif ($payment_type === 'CASH') {
			$payment_expired_at = NULL;
		}

		$update_shipment = [
			'origin'                  => $origin,
			'destination'             => $destination,
			'service_type_id'         => $service_id,
			'commodity_id'            => $this->input->post('commodity_id', TRUE),
			'commodity_detail'        => $this->input->post('commodity_detail', TRUE),
			'commodity_detail_en'     => $this->input->post('commodity_detail_en', TRUE),
			'customs_value_usd'       => $this->_parse_indo_number($this->input->post('customs_value_usd')),
			'sender_name'             => $sender_name,
			'sender_phone'            => $sender_phone,
			'sender_nik'              => $this->input->post('sender_nik', TRUE),
			'sender_provinsi'         => $sender_prov_name ? $sender_prov_name->nama_provinsi : NULL,
			'sender_kota'             => $sender_kota_name ? $sender_kota_name->nama_kota     : NULL,
			'sender_kecamatan'        => $sender_kec_name  ? $sender_kec_name->nama_kecamatan : NULL,
			'sender_kelurahan'        => $sender_kel_name  ? $sender_kel_name->nama_kelurahan : NULL,
			'sender_address_detail'   => $this->input->post('sender_address_detail', TRUE),
			'sender_address'          => $full_sender_address,
			'receiver_name'           => $this->input->post('receiver_name', TRUE),
			'receiver_phone'          => $this->input->post('receiver_phone', TRUE),
			'receiver_kota'           => $this->input->post('receiver_city', TRUE),
			'receiver_zipcode'        => $this->input->post('receiver_zipcode', TRUE),
			'receiver_address_detail' => $this->input->post('receiver_address_detail', TRUE),
			'receiver_address'        => $full_receiver_address,
			'payment_type'            => $payment_type,
			'koli'                    => $total_koli ?: 1,
			'actual_weight'           => $actual_weight,
			'volume_weight'           => $total_volume_weight,
			'chargeable_weight'       => $chargeable,
			'cost_price'              => $cost_per_kg,
			'sell_price'              => $sell_per_kg,
			'pickup_rate_id'          => $pickup_id,
			'pickup_fee'              => $pickup_fee,
			'total_addon_fee'         => $total_addon_fee,
			'total_amount'            => $shipping_total + $pickup_fee + $total_addon_fee,
			'margin_amount'           => $shipping_margin,
			'shipment_photo'          => $photo_path,
			'payment_expired_at'      => $payment_expired_at,
			'updated_by'              => $sess['id'],
			'updated_at'              => date('Y-m-d H:i:s'),
		];

		$this->db->trans_start();

		$this->db->where('id', $id)->update('shipments', $update_shipment);

		$this->db->where('shipment_id', $id)->delete('shipment_dimensions');
		if (!empty($insert_dimensions)) $this->db->insert_batch('shipment_dimensions', $insert_dimensions);

		$this->db->where('shipment_id', $id)->delete('shipment_addons');
		if (!empty($insert_addons)) $this->db->insert_batch('shipment_addons', $insert_addons);

		$this->db->insert('shipment_tracking', [
			'shipment_id' => $id,
			'status'      => 'BOOKED',
			'location'    => $origin,
			'note'        => 'Data shipment internasional diperbarui. Total: Rp ' . number_format($shipping_total + $pickup_fee + $total_addon_fee, 0, ',', '.'),
			'created_by'  => $sess['id'],
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal menyimpan perubahan!');
			redirect('shipment/edit_intl/' . $id);
		} else {
			$this->session->set_flashdata('success', 'Shipment Internasional <b>' . $shipment->no_resi . '</b> berhasil diperbarui!');
			redirect('shipment/detail/' . $id);
		}
	}

	public function detail($id)
	{
		$this->_check_access();

		$shipment = $this->M_Shipment->get_by_id($id);

		if (!$shipment) {
			$this->session->set_flashdata('error', 'Data shipment tidak ditemukan.');
			redirect('shipment');
		}

		$data = [
			'title'      => 'Detail Shipment - ' . $shipment['no_resi'],
			'shipment'   => $shipment,
			'dimensions' => $this->M_Shipment->get_dimensions($id),
			'history'    => $this->M_Shipment->get_tracking_public($id)
		];

		$this->render('app/pages/shipment/detail', $data);
	}

	public function autocompleteCustomer()
	{
		$term = $this->input->get('term');

		$this->db->like('nama_customer', $term);
		$query = $this->db->get('customer');

		$result = $query->result_array();
		$items = [];
		foreach ($result as $row) {
			$items[] = [
				'label' => $row['nama_customer'] . ' - ' . $row['telepon_customer'],
				'value' => $row['nama_customer'],
				'nama_customer' => $row['nama_customer'],
				'alamat_customer' => $row['alamat_customer'],
				'telepon_customer' => $row['telepon_customer'],
			];
		}
		echo json_encode($items);
	}

	public function ajax_confirm_paid()
	{
		$id = $this->input->post('id');
		// Status tetap READY_TO_PICKUP, tapi note-nya kita perjelas
		$update = $this->M_Shipment->update_status(
			$id,
			'READY_TO_PICKUP',
			'Pembayaran lunas. Menunggu penjemputan driver untuk dikirim ke Warehouse.',
			'Lokasi Mitra'
		);
		echo json_encode(['status' => $update]);
	}

	public function ajax_bulk_manifest()
	{
		// Tangkap data dari JS
		$ids = json_decode($this->input->post('ids')); // Array ID Shipment
		$data_manifest = [
			'smu_number'       => $this->input->post('smu_number', TRUE),
			'flight_number'    => $this->input->post('flight_number', TRUE),
			'origin_warehouse' => $this->input->post('origin_warehouse', TRUE),
			'departure_date'   => $this->input->post('departure_date', TRUE),
		];

		if (empty($ids)) {
			echo json_encode(['status' => false, 'message' => 'Tidak ada resi yang dipilih']);
			return;
		}

		// Eksekusi di Model (Pastikan lu buat fungsi bulk_manifest di M_Shipment)
		$res = $this->M_Shipment->bulk_manifest($ids, $data_manifest);

		if ($res) {
			echo json_encode(['status' => true, 'message' => count($ids) . ' Resi berhasil dimanifestkan.']);
		} else {
			echo json_encode(['status' => false, 'message' => 'Gagal memproses manifest.']);
		}
	}

	public function print_label($no_resi)
	{
		$resi = $this->M_Shipment->getResi($no_resi);

		if (!$resi) {
			show_404();
			return;
		}

		$no_print_statuses = ['BOOKED', 'CANCELLED'];
		if (in_array($resi['status'], $no_print_statuses)) {
			$this->session->set_flashdata('error', 'Shipment dengan status ' . $resi['status'] . ' tidak bisa dicetak.');
			redirect('shipment/detail/' . $resi['id']); // ← sesuaikan dengan route detail kamu
		}

		$this->load->library('ciqrcode');
		$this->load->library('cibarcode');
		$tempDir = sys_get_temp_dir();

		// 1. Logo Base64 (Cukup sekali saja)
		// $logoPath = FCPATH . 'assets/logo/icon-smesco.png';
		$logoPath = FCPATH . 'assets/logo/logo-smesco-hera-2-small.png';
		$logoBase64 = (file_exists($logoPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

		// 2. QR Tracking (Sama untuk semua koli karena merujuk ke halaman tracking yang sama)
		$linkTracking = base_url("home/tracking?awb=$no_resi");
		$fileTracking = 'qr_trc_' . $no_resi . '.png';
		$pathTracking = $tempDir . DIRECTORY_SEPARATOR . $fileTracking;
		$this->ciqrcode->generate([
			'data' => $linkTracking,
			'level' => 'M',
			'size' => 4,
			'savename' => $pathTracking,
		]);
		$qrTracking = 'data:image/png;base64,' . base64_encode(file_get_contents($pathTracking));
		unlink($pathTracking);

		// 3. Looping Label per Koli
		$total_koli = intval($resi['koli']);
		$labels = [];

		for ($i = 1; $i <= $total_koli; $i++) {
			// Generate Piece ID (Contoh: SMC2604050001-01)
			$piece_id = $resi['no_resi'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

			// QR Internal per Piece (Isinya Piece ID untuk di-scan Inbound/Acceptance)
			$internalString = "AWB: {$resi['no_resi']} | PIECE: $piece_id | TO: {$resi['destination']} | KG: {$resi['chargeable_weight']}";
			$fileInternal = 'qr_int_' . $piece_id . '.png';
			$pathInternal = $tempDir . DIRECTORY_SEPARATOR . $fileInternal;

			$this->ciqrcode->generate([
				'data' => $piece_id, // Scan yang ini di Gudang/Bandara
				'level' => 'M',
				'size' => 5,
				'savename' => $pathInternal,
			]);

			$qrInternal = 'data:image/png;base64,' . base64_encode(file_get_contents($pathInternal));
			unlink($pathInternal);

			// Barcode 1D (Bisa pakai No Resi atau Piece ID, saran: Piece ID)
			$barcodeData = $this->cibarcode->generate($piece_id);

			$labels[] = [
				'piece_id'    => $piece_id,
				'koli_ke'     => $i,
				'qr_internal' => $qrInternal,
				'barcode'     => $barcodeData
			];
		}

		$data = [
			'resi'        => $resi,
			'labels'      => $labels,
			'total_koli'  => $total_koli,
			'qr_tracking' => $qrTracking,
			'logo_base64' => $logoBase64
		];

		// $this->load->view('app/pages/shipment/print', $data);


		$html = $this->load->view('app/pages/shipment/print', $data, true);
		$this->pdfgenerator->generate($html, 'Resi-' . $no_resi, 'A6', 'portrait');
	}

	public function pickup_scan()
	{
		$this->_check_access();

		// Ambil semua barang yang siap dijemput (READY_TO_PICKUP)
		$this->db->select('shipments.*, agents.name as agent_name');
		$this->db->from('shipments');
		$this->db->join('agents', 'agents.id = shipments.agent_id', 'left');
		$this->db->where('shipments.status', 'READY_TO_PICKUP');
		$pending_list = $this->db->get()->result();

		$data = [
			'title' => 'Scan Pickup Barang',
			'pending_list' => $pending_list
		];

		$this->render('app/pages/shipment/pickup_scan', $data);
	}

	public function ajax_update_pickup()
	{
		$input_scan = $this->input->post('no_resi', TRUE); // Isinya bisa SMC...-01

		// ── LOGIC STRIP PIECE ID ──
		// Kita pecah string berdasarkan tanda "-". 
		// Kalau driver scan SMC2604050001-01, kita ambil depannya saja.
		$parts = explode('-', $input_scan);
		$no_resi = $parts[0];

		// 1. Cari resi di database
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();

		if (!$shipment) {
			echo json_encode(['status' => false, 'message' => "Resi $no_resi tidak terdaftar!"]);
			return;
		}

		// 2. Validasi status
		if ($shipment->status !== 'READY_TO_PICKUP') {
			echo json_encode(['status' => false, 'message' => "Gagal! Status resi: $shipment->status"]);
			return;
		}

		// 3. Update status Master Resi
		// Karena dipickup, kita asumsikan satu resi (semua koli) dibawa semua oleh driver
		$update = $this->M_Shipment->update_status(
			$shipment->id,
			'PICKED_UP',
			"Pickup berhasil. Driver membawa {$shipment->koli} koli menuju Warehouse.",
			'Lokasi Mitra'
		);

		if ($update) {
			echo json_encode([
				'status' => true,
				'data' => [
					'no_resi'    => $shipment->no_resi,
					'penerima'   => $shipment->receiver_name,
					'tujuan'     => $shipment->destination,
					'total_koli' => $shipment->koli // Kirim info koli buat ditampilin di HP driver
				]
			]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Gagal update database.']);
		}
	}

	public function acceptance_scan()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		// Ambil daftar barang yang statusnya PICKED_UP (yang harus discan checker)
		$this->db->select('no_resi, receiver_name, destination, koli');
		$this->db->where('status', 'PICKED_UP');
		$pending_list = $this->db->get('shipments')->result();

		$data = [
			'title' => 'Checker: Acceptance Gudang',
			'pending_list' => $pending_list
		];

		$this->render('app/pages/shipment/acceptance_mobile', $data);
	}

	public function ajax_process_acceptance()
	{
		$input_scan = $this->input->post('no_resi', TRUE); // Contoh: SMC2604050001-01

		$parts = explode('-', $input_scan);
		$no_resi  = $parts[0];                          // ✅ "SMC2604050001"
		$piece_no = isset($parts[1]) ? $parts[1] : '01'; // Ambil nomor urut kolinya

		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();

		if (!$shipment) {
			echo json_encode(['status' => false, 'message' => 'Resi tidak ditemukan!']);
			return;
		}

		// 1. CEK: Apakah koli (Piece ID) ini sudah pernah di-scan sebelumnya?
		// Kita cek di tabel tracking, apakah sudah ada note yang mengandung Piece ID ini
		$check_piece = $this->db->get_where('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'note LIKE' => "%Box $input_scan OK%"
		])->num_rows();

		if ($check_piece > 0) {
			echo json_encode(['status' => false, 'message' => "Koli $input_scan sudah pernah di-scan, bro!"]);
			return;
		}

		// 2. CATAT Piece ID ini masuk ke log tracking
		// Kita belum ubah status master 'shipments', kita cuma nambahin log per koli
		$this->M_Shipment->insert_tracking_log($shipment->id, 'PIECE_RECEIVED', "Box $input_scan OK", 'Warehouse Origin');

		// 3. HITUNG: Sudah berapa koli yang terkumpul untuk Resi ini?
		$received_count = $this->db->get_where('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status' => 'PIECE_RECEIVED'
		])->num_rows();

		// 4. LOGIC UPDATE MASTER: Hanya jika koli sudah LENGKAP
		$is_complete = false;
		if ($received_count >= $shipment->koli) {
			$this->M_Shipment->update_status($shipment->id, 'RECEIVED_ORIGIN', 'Seluruh koli diterima lengkap.', 'Warehouse Origin');
			$is_complete = true;
		}

		echo json_encode([
			'status' => true,
			'message' => "Koli $piece_no diterima ($received_count/{$shipment->koli})",
			'data' => [
				'no_resi'    => $shipment->no_resi,
				'received'   => $received_count,
				'total_koli' => $shipment->koli,
				'is_complete' => $is_complete
			]
		]);
	}

	public function ajax_confirm_departure()
	{
		$this->_check_access();
		$smu_number = $this->input->post('smu_number', TRUE);

		if (empty($smu_number)) {
			echo json_encode(['status' => false, 'message' => 'Nomor SMU wajib diisi!']);
			return;
		}

		// Eksekusi di Model
		$res = $this->M_Shipment->depart_by_smu($smu_number);

		if ($res) {
			echo json_encode([
				'status' => true,
				'message' => "Pesawat dengan SMU $smu_number resmi berangkat (Status: DEPARTED)."
			]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Gagal memproses keberangkatan atau SMU tidak ditemukan.']);
		}
	}

	// public function manifest()
	// {
	// 	$this->_check_access();

	// 	$filters = [
	// 		'status' => $this->input->get('status', TRUE),
	// 	];

	// 	$data = [
	// 		'title'     => 'Manajemen Manifest & Flight',
	// 		'manifests' => $this->M_Shipment->get_manifest_list($filters),
	// 		'filters'   => $filters,
	// 	];

	// 	$this->render('app/pages/shipment/manifest', $data);
	// }

	// public function create_awb()
	// {

	// }

	public function manifest()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		// Filter status dari URL GET
		$filters = [
			'status'   => $this->input->get('status', TRUE),
			'agent_id' => $sess['agent_id'] ?? NULL // Filter agent jika bukan pusat
		];

		// Memanggil data dari query master_awb yang baru
		$manifests = $this->M_Shipment->get_master_awb_list($filters);

		$data = [
			'title'     => 'Flight & Manifest Monitoring',
			'manifests' => $manifests,
			'filters'   => $filters,
		];

		$this->render('app/pages/shipment/manifest', $data);
	}

	public function create_awb()
	{
		$this->_check_access();

		$data = [
			'title'     => 'Buat Master AWB Udara Baru',
			'airlines'  => $this->db->get_where('airlines', ['is_active' => 1])->result(),
			'airports'  => $this->db->select('code, name')->get_where('cities', ['is_active' => 1])->result() // Menggunakan data kota/bandara
		];

		$this->render('app/pages/shipment/create_awb', $data);
	}

	public function save_awb()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$data_awb = [
			'awb_number'     => $this->input->post('awb_number', TRUE),
			'airline_id'     => $this->input->post('airline_id', TRUE),
			'flight_number'  => $this->input->post('flight_number', TRUE),
			'departure_date' => $this->input->post('departure_date', TRUE),
			'origin'         => $this->input->post('origin', TRUE),
			'destination'    => $this->input->post('destination', TRUE),
			'status'         => 'DRAFT',
			'created_by'     => $sess['id']
		];

		$insert = $this->db->insert('master_awb', $data_awb);
		if ($insert) {
			$awb_id = $this->db->insert_id();
			// Bawa user ke halaman khusus untuk proses packing/scanning resi ke koli
			$this->session->set_flashdata('success', 'Master AWB berhasil dibuat. Silakan tambahkan koli karung dan scan resi!');
			redirect('shipment/awb_console/' . $awb_id);
		} else {
			$this->session->set_flashdata('error', 'Gagal membuat Master AWB baru.');
			redirect('shipment/create_awb');
		}
	}

	public function awb_console($awb_id)
	{
		$this->_check_access();

		// 1. Ambil data master AWB induknya
		$this->db->select('ma.*, al.name as airline_name');
		$this->db->from('master_awb ma');
		$this->db->join('airlines al', 'al.id = ma.airline_id', 'left');
		$this->db->where('ma.id', $awb_id);
		$awb = $this->db->get()->row();

		if (!$awb) {
			$this->session->set_flashdata('error', 'Data AWB tidak ditemukan bro!');
			redirect('shipment/manifest');
		}

		// 2. Ambil daftar koli (karung) yang sudah dibuat di dalam AWB ini
		$kolis = $this->db->get_where('awb_koli', ['awb_id' => $awb_id])->result();

		// 3. Loop untuk ngambil detail koli fisik (dus) yang ada di dalam masing-masing karung koli
		foreach ($kolis as $k) {
			// Kita ambil kode barcode dusnya dari shipment_dimensions beserta data penunjang dari shipments induk
			$this->db->select('
                s.id as shipment_id, 
                sd.barcode_koli as no_resi, 
                s.destination, 
                s.commodity_detail,
                (s.chargeable_weight / s.koli) as chargeable_weight,
                "1/1" as koli
            ');
			$this->db->from('shipment_dimensions sd');
			$this->db->join('shipments s', 's.id = sd.shipment_id');
			$this->db->where('sd.awb_koli_id', $k->id);
			$this->db->order_by('sd.id', 'DESC');

			$k->items = $this->db->get()->result();
		}

		$data = [
			'title' => 'Console Packing AWB: ' . $awb->awb_number,
			'awb'   => $awb,
			'kolis' => $kolis
		];

		$this->render('app/pages/shipment/awb_console', $data);
	}

	// AJAX: Membuat Karung Koli Baru di Dalam AWB
	public function ajax_create_koli()
	{
		$this->_check_access();
		$awb_id = $this->input->post('awb_id', TRUE);

		// Hitung koli ke berapa yang mau dibuat biar dapet nomor berurutan
		$this->db->where('awb_id', $awb_id);
		$total_existing = $this->db->count_all_results('awb_koli');
		$next_number = str_pad($total_existing + 1, 3, '0', STR_PAD_LEFT);

		$koli_number = 'KOLI-' . $next_number;

		$data_koli = [
			'awb_id'        => $awb_id,
			'koli_number'   => $koli_number,
			'actual_weight' => 0.00
		];

		if ($this->db->insert('awb_koli', $data_koli)) {
			echo json_encode(['status' => true, 'message' => 'Karung ' . $koli_number . ' berhasil dibuat!']);
		} else {
			echo json_encode(['status' => false, 'message' => 'Gagal membuat karung baru.']);
		}
	}

	// AJAX: Tembak Barcode Resi untuk dimasukkan ke Karung Koli
	public function ajax_bind_resi_to_koli()
	{
		$this->_check_access();

		// Petugas menembak barcode per kardus fisik (e.g., SMC2604001-01)
		$barcode_koli = strtoupper(trim($this->input->post('no_resi', TRUE)));
		$awb_koli_id  = $this->input->post('awb_koli_id', TRUE);

		// 1. Cek di tabel anak dimensi koli
		$dimensi = $this->db->get_where('shipment_dimensions', ['barcode_koli' => $barcode_koli])->row();

		// echo json_encode(['dimensi' => $dimensi]);return;

		if (!$dimensi) {
			echo json_encode(['status' => false, 'message' => 'Barcode Koli ' . $barcode_koli . ' tidak valid!']);
			return;
		}

		if (!empty($dimensi->awb_koli_id)) {
			$karung = $this->db->get_where('awb_koli', ['id' => $dimensi->awb_koli_id])->row();
			echo json_encode(['status' => false, 'message' => 'Koli ini sudah di-packing di ' . $karung->koli_number]);
			return;
		}

		// Ambil data resi induknya untuk keperluan info display & estimasi berat proporsional
		$shipment = $this->db->get_where('shipments', ['id' => $dimensi->shipment_id])->row();

		// Hitung estimasi berat per koli (Berat total resi dibagi jumlah koli induk)
		$berat_per_koli = $shipment->chargeable_weight / $shipment->koli;

		$this->db->trans_start();

		// Update tabel dimensi koli: bind ke karung udara
		$this->db->where('id', $dimensi->id);
		$this->db->update('shipment_dimensions', ['awb_koli_id' => $awb_koli_id]);

		// Hitung total berat karung saat ini dari akumulasi koli di dalamnya
		$this->db->select('sd.shipment_id, s.chargeable_weight, s.koli');
		$this->db->from('shipment_dimensions sd');
		$this->db->join('shipments s', 's.id = sd.shipment_id');
		$this->db->where('sd.awb_koli_id', $awb_koli_id);
		$packed_items = $this->db->get()->result();

		$total_berat_karung = 0;
		foreach ($packed_items as $item) {
			$total_berat_karung += ($item->chargeable_weight / $item->koli);
		}

		// Update total timbangan karung
		$this->db->where('id', $awb_koli_id);
		$this->db->update('awb_koli', ['actual_weight' => $total_berat_karung]);

		// ── 🔥 LOGIKA EVALUASI STATUS RESI INDUK (PROPORSIONAL SINKRON) ──
		$this->db->where('shipment_id', $shipment->id);
		$this->db->where('awb_koli_id IS NULL');
		$sisa_koli = $this->db->count_all_results('shipment_dimensions');

		if ($sisa_koli == 0) {
			// Skenario A: Semua koli fisik resi ini sudah lengkap masuk karung kargo
			$this->db->where('id', $shipment->id);
			$this->db->update('shipments', ['status' => 'CONSOLIDATED']);
		} else {
			// Skenario B: Paket baru masuk sebagian ke karung (misal 1 dari 3 koli)
			$this->db->where('id', $shipment->id);
			$this->db->update('shipments', ['status' => 'PARTIAL_CONSOLIDATED']);
		}

		$this->db->trans_complete();

		echo json_encode([
			'status' => true,
			'message' => 'Koli ' . $barcode_koli . ' masuk karung!',
			'data' => [
				'no_resi'             => $barcode_koli,
				'destination'         => $shipment->destination,
				'koli'                => '1/1',
				'weight'              => number_format($berat_per_koli, 1),
				'commodity'           => $shipment->commodity_detail,
				'updated_koli_weight' => number_format($total_berat_karung, 2)
			]
		]);
	}

	public function ajax_finalize_awb()
	{
		$this->_check_access();
		$awb_id = $this->input->post('awb_id', TRUE);
		$sess   = $this->session->userdata('user');

		// 1. Validasi keberadaan AWB
		$awb = $this->db->get_where('master_awb', ['id' => $awb_id])->row();
		if (!$awb) {
			echo json_encode(['status' => false, 'message' => 'Data AWB tidak ditemukan bro.']);
			return;
		}

		// 2. Ambil semua resi unik yang ada di dalam karung-karung AWB ini
		$this->db->select('sd.shipment_id');
		$this->db->from('shipment_dimensions sd');
		$this->db->join('awb_koli ak', 'ak.id = sd.awb_koli_id');
		$this->db->where('ak.awb_id', $awb_id);
		$this->db->group_by('sd.shipment_id');
		$shipments = $this->db->get()->result();

		if (empty($shipments)) {
			echo json_encode(['status' => false, 'message' => 'Gagal! Kamu belum men-scan resi satu pun ke dalam koli karung.']);
			return;
		}

		$this->db->trans_start();

		// 3. Update status Master AWB menjadi MANIFESTED
		$this->db->where('id', $awb_id);
		$this->db->update('master_awb', ['status' => 'MANIFESTED']);

		// 4. Update status shipments & inject data manifest udara ke resi induk
		$shipment_ids = array_column($shipments, 'shipment_id');

		$this->db->where_in('id', $shipment_ids);
		$this->db->update('shipments', [
			'status'           => 'MANIFESTED',
			'smu_number'       => $awb->awb_number,
			'flight_number'    => $awb->flight_number,
			'departure_date'   => $awb->departure_date,
			'origin_warehouse' => 'GUDANG UTAMA LINI 1'
		]);

		// 5. Insert history tracking untuk semua resi sekaligus (batch)
		$tracking_batch = [];
		foreach ($shipment_ids as $s_id) {
			$tracking_batch[] = [
				'shipment_id' => $s_id,
				'status'      => 'MANIFESTED',
				'location'    => $awb->origin,
				'note'        => 'Paket telah dimasukkan ke manifest penerbangan ' . $awb->flight_number . ' dengan nomor AWB: ' . $awb->awb_number,
				'created_by'  => $sess['id']
			];
		}
		$this->db->insert_batch('shipment_tracking', $tracking_batch);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => false, 'message' => 'Gagal finalisasi karena error database.']);
		} else {
			echo json_encode(['status' => true, 'message' => 'Console AWB resmi dikunci dan masuk ke manifest aktif!']);
		}
	}

	// AJAX: Mengambil daftar karung untuk modal checklist
	public function ajax_get_koli_by_awb($awb_id)
	{
		$this->_check_access();
		$kolis = $this->db->get_where('awb_koli', ['awb_id' => $awb_id])->result();

		if ($kolis) {
			echo json_encode(['status' => true, 'data' => $kolis]);
		} else {
			echo json_encode(['status' => false, 'message' => 'AWB ini tidak memiliki koli/karung.']);
		}
	}

	// AJAX INTI: Eksekusi Split Keberangkatan Penerbangan
	public function ajax_confirm_split_departure()
	{
		$this->_check_access();
		$awb_id     = $this->input->post('awb_id', TRUE);
		$karung_fly = $this->input->post('karung_ids', TRUE) ?: []; // ID karung yang dicentang terbang
		$sess       = $this->session->userdata('user');

		$awb = $this->db->get_where('master_awb', ['id' => $awb_id])->row();
		if (!$awb) {
			echo json_encode(['status' => false, 'message' => 'Master AWB tidak valid bro.']);
			return;
		}

		// Ambil semua daftar ID karung asli yang terdaftar di AWB ini
		$all_koli = $this->db->get_where('awb_koli', ['awb_id' => $awb_id])->result_array();
		$all_koli_ids = array_column($all_koli, 'id');

		// Cari tahu karung mana saja yang ditinggal (Offloaded)
		$karung_left = array_diff($all_koli_ids, $karung_fly);

		$this->db->trans_start();

		// 1. UPDATE STATUS KOLI/KARUNG
		// Karung yang dicentang -> DEPARTED
		if (!empty($karung_fly)) {
			$this->db->where_in('id', $karung_fly)->update('awb_koli', ['status' => 'DEPARTED']);
		}
		// Karung yang ditinggal -> OFFLOADED
		if (!empty($karung_left)) {
			$this->db->where_in('id', $karung_left)->update('awb_koli', ['status' => 'OFFLOADED']);
		}

		// 2. UPDATE STATUS MASTER AWB INDUKNYA MENJADI DEPARTED
		$this->db->where('id', $awb_id)->update('master_awb', ['status' => 'DEPARTED']);

		// 3. LOGIKA EVALUASI STATUS RESI INDUK (PROPORSIONAL PARAREL)
		// Ambil semua resi unik yang terikat di dalam seluruh karung AWB ini
		$this->db->select('sd.shipment_id');
		$this->db->from('shipment_dimensions sd');
		$this->db->where_in('sd.awb_koli_id', $all_koli_ids);
		$this->db->group_by('sd.shipment_id');
		$shipments = $this->db->get()->result_array();

		$tracking_batch = [];

		foreach ($shipments as $s) {
			$s_id = $s['shipment_id'];

			// Hitung total koli fisik asli resi ini di DB
			$total_koli_resi = $this->db->where('shipment_id', $s_id)->count_all_results('shipment_dimensions');

			// Hitung berapa koli fisik resi ini yang ikut terbang (status karungnya DEPARTED)
			$this->db->from('shipment_dimensions sd');
			$this->db->join('awb_koli ak', 'ak.id = sd.awb_koli_id');
			$this->db->where('sd.shipment_id', $s_id);
			$this->db->where('ak.status', 'DEPARTED');
			$koli_terbang = $this->db->count_all_results();

			// Penentuan Status Akhir Resi Induk
			if ($koli_terbang === $total_koli_resi) {
				// Skenario A: Semua koli ikut terbang lengkap
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'DEPARTED']);

				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'DEPARTED',
					'location'    => $awb->origin,
					'note'        => 'Flight Out! Seluruh koli paket telah diberangkatkan dengan pesawat ' . $awb->flight_number,
					'created_by'  => $sess['id']
				];
			} elseif ($koli_terbang > 0 && $koli_terbang < $total_koli_resi) {
				// Skenario B: Hanya sebagian koli yang ikut terbang (SPLIT / PARTIAL)
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'PARTIAL_DEPARTED']);

				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'PARTIAL_DEPARTED',
					'location'    => $awb->origin,
					'note'        => 'Partial Flight Out! Baru sebanyak ' . $koli_terbang . '/' . $total_koli_resi . ' koli fisik yang berangkat di pesawat ' . $awb->flight_number . '. Sisa koli tertinggal akan disusulkan kloter berikutnya.',
					'created_by'  => $sess['id']
				];
			} else {
				// Skenario C: Kebetulan resi ini seluruh dusnya ada di dalam karung yang tertinggal
				// Status resi induk biarkan tetap MANIFESTED (menunggu re-route pesawat berikutnya)
			}
		}

		// Insert log tracking secara massal
		if (!empty($tracking_batch)) {
			$this->db->insert_batch('shipment_tracking', $tracking_batch);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => false, 'message' => 'Gagal konfirmasi terbang karena kendala database transaction.']);
		} else {
			echo json_encode(['status' => true, 'message' => 'Status keberangkatan berhasil diproses proporsional!']);
		}
	}

	// AJAX: Ambil daftar karung untuk modal check-list sebelum terbang
	public function ajax_get_awb_bags()
	{
		$this->_check_access();
		$awb_number = $this->input->post('smu_number', TRUE);

		// Cari master AWB
		$awb = $this->db->get_where('master_awb', ['awb_number' => $awb_number])->row();
		if (!$awb) {
			echo json_encode(['status' => false, 'message' => 'Master AWB tidak ditemukan!']);
			return;
		}

		// Ambil list karung di dalam AWB ini
		$this->db->select('ak.id, ak.koli_number, ak.actual_weight, COUNT(sd.id) as total_resi_koli');
		$this->db->from('awb_koli ak');
		$this->db->join('shipment_dimensions sd', 'sd.awb_koli_id = ak.id', 'left');
		$this->db->where('ak.awb_id', $awb->id);
		$this->db->group_by('ak.id, ak.koli_number, ak.actual_weight');
		$bags = $this->db->get()->result();

		echo json_encode([
			'status' => true,
			'awb'    => $awb,
			'bags'   => $bags
		]);
	}

	// AJAX: Eksekusi Keberangkatan Parsial (Split Flight)
	public function ajax_confirm_partial_departure()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$awb_id       = $this->input->post('awb_id', TRUE);
		$flown_bag_ids = $this->input->post('flown_bags', TRUE) ?: []; // Array ID karung yang dicentang (terbang)

		$awb = $this->db->get_where('master_awb', ['id' => $awb_id])->row();
		if (!$awb) {
			echo json_encode(['status' => false, 'message' => 'Data penerbangan tidak valid.']);
			return;
		}

		// 1. Ambil semua karung yang terdaftar di AWB ini
		$all_bags = $this->db->get_where('awb_koli', ['awb_id' => $awb_id])->result();
		if (empty($all_bags)) {
			echo json_encode(['status' => false, 'message' => 'AWB ini tidak memiliki koli karung untuk diterbangkan.']);
			return;
		}

		$this->db->trans_start();

		$allocated_flown_bags = [];
		$allocated_offload_bags = [];

		// 2. Update status masing-masing karung (DEPARTED vs OFLOADED)
		foreach ($all_bags as $bag) {
			if (in_array($bag->id, $flown_bag_ids)) {
				// Karung Ikut Terbang
				$this->db->where('id', $bag->id)->update('awb_koli', ['status' => 'DEPARTED']);
				$allocated_flown_bags[] = $bag->id;
			} else {
				// Karung Ketinggalan (Offload)
				$this->db->where('id', $bag->id)->update('awb_koli', ['status' => 'OFFLOADED']);
				$allocated_offload_bags[] = $bag->id;
			}
		}

		// 3. Update status Master AWB Induknya
		// Jika ada karung yang tertinggal, induknya kita set status 'PARTIAL_DEPARTED', jika lengkap 'DEPARTED'
		$final_awb_status = (count($allocated_offload_bags) > 0) ? 'DEPARTED' : 'DEPARTED';
		// Catatan: Di master_awb tetap set DEPARTED agar tidak merusak filter luar, tapi log resinya yang kita perketat
		$this->db->where('id', $awb_id)->update('master_awb', ['status' => 'DEPARTED']);

		// 4. Ambil semua resi unik yang terlibat di seluruh karung AWB ini
		$this->db->select('shipment_id')->from('shipment_dimensions')->where_in('awb_koli_id', array_column($all_bags, 'id'))->group_by('shipment_id');
		$involved_shipments = $this->db->get()->result();

		// 5. EVALUASI STATUS RESI INDUK SATU PER SATU (LOGIKA AKUMULASI PARALEL)
		$tracking_batch = [];
		foreach ($involved_shipments as $sh) {
			$s_id = $sh->shipment_id;

			// Hitung total koli fisik resi ini
			$total_koli_resi = $this->db->where('shipment_id', $s_id)->count_all_results('shipment_dimensions');

			// Hitung berapa koli fisik resi ini yang ikut di karung terbang
			$this->db->where('shipment_id', $s_id);
			$this->db->where_in('awb_koli_id', $flown_bag_ids);
			$flown_koli_resi = $this->db->count_all_results('shipment_dimensions');

			if ($flown_koli_resi === $total_koli_resi) {
				// Skenario A: Semua koli resi ini ikut terbang lancar
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'DEPARTED']);
				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'DEPARTED',
					'location'    => $awb->origin,
					'note'        => 'Lengkap! Seluruh koli barang telah terbang dengan pesawat ' . $awb->flight_number,
					'created_by'  => $sess['id']
				];
			} elseif ($flown_koli_resi > 0 && $flown_koli_resi < $total_koli_resi) {
				// Skenario B: Split Shipment (Hanya sebagian koli yang ikut terbang kloter ini)
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'PARTIAL_DEPARTED']);
				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'PARTIAL_DEPARTED',
					'location'    => $awb->origin,
					'note'        => "Split Flight! Baru {$flown_koli_resi} dari {$total_koli_resi} koli fisik yang berangkat dengan pesawat {$awb->flight_number}. Sisa koli tertinggal di gudang asal dan akan menyusul.",
					'created_by'  => $sess['id']
				];
			} else {
				// Skenario C: Kebetulan semua koli dari resi ini numpuk di karung yang tertinggal (offload total)
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'OFFLOADED']);
				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'OFFLOADED',
					'location'    => $awb->origin,
					'note'        => 'Kargo Offload! Seluruh koli untuk resi ini tertinggal di bandara asal karena kapasitas pesawat penuh.',
					'created_by'  => $sess['id']
				];
			}
		}

		if (!empty($tracking_batch)) {
			$this->db->insert_batch('shipment_tracking', $tracking_batch);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => false, 'message' => 'Gagal memproses keberangkatan karena error database.']);
		} else {
			$msg = "Keberangkatan berhasil diproses! " . count($allocated_flown_bags) . " karung terbang, " . count($allocated_offload_bags) . " karung offload.";
			echo json_encode(['status' => true, 'message' => $msg]);
		}
	}

	// AJAX: Mengambil daftar Master AWB yang masih DRAFT untuk target Re-Route
	public function ajax_get_draft_awb()
	{
		$this->_check_access();
		// Ambil penerbangan yang masih siap menampung koli konsolidasi
		$this->db->select('id, awb_number, flight_number, origin, destination');
		$this->db->where('status', 'DRAFT');
		$this->db->order_by('id', 'DESC');
		$data = $this->db->get('master_awb')->result();

		if ($data) {
			echo json_encode(['status' => true, 'data' => $data]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Kosong']);
		}
	}

	// AJAX INTI: Eksekusi Perpindahan Karung ke Pesawat Baru
	public function ajax_execute_reroute_koli()
	{
		$this->_check_access();
		$koli_id            = $this->input->post('koli_id', TRUE);
		$target_awb_id      = $this->input->post('target_awb_id', TRUE);
		$target_action_koli = $this->input->post('target_action_koli', TRUE); // NEW_KOLI atau ID Karung Eksis
		$sess               = $this->session->userdata('user');

		// 1. Ambil data Master AWB Target
		$new_awb = $this->db->get_where('master_awb', ['id' => $target_awb_id])->row();
		if (!$new_awb) {
			echo json_encode(['status' => false, 'message' => 'Penerbangan tujuan tidak valid bro!']);
			return;
		}

		// Ambil resi-resi di dalam karung asal
		$this->db->select('shipment_id');
		$this->db->from('shipment_dimensions');
		$this->db->where('awb_koli_id', $koli_id);
		$this->db->group_by('shipment_id');
		$shipments = $this->db->get()->result_array();

		if (empty($shipments)) {
			echo json_encode(['status' => false, 'message' => 'Karung asal kosong atau tidak punya isi resi bro.']);
			return;
		}

		$this->db->trans_start();

		$final_koli_id = NULL;
		$final_koli_name = '';

		if ($target_action_koli === 'NEW_KOLI') {
			// SKENARIO A: Bikin Wadah Karung Baru di Pesawat Baru
			$this->db->where('awb_id', $target_awb_id);
			$total_existing = $this->db->count_all_results('awb_koli');
			$next_number = str_pad($total_existing + 1, 3, '0', STR_PAD_LEFT);
			$final_koli_name = 'KOLI-' . $next_number;

			// Update wadah karung lamanya biar ga usah insert baru, status ikut DRAFT/mengikuti AWB baru
			$this->db->where('id', $koli_id)->update('awb_koli', [
				'awb_id'      => $target_awb_id,
				'koli_number' => $final_koli_name,
				'status'      => 'DRAFT'
			]);
			$final_koli_id = $koli_id;
		} else {
			// SKENARIO B: Dilebur / Dimasukkan ke Karung yang Sudah Eksis di Pesawat Baru
			$final_koli_id = $target_action_koli;
			$target_koli_row = $this->db->get_where('awb_koli', ['id' => $final_koli_id])->row();
			$final_koli_name = $target_koli_row->koli_number;

			// Pindahkan seluruh isi koli fisik di shipment_dimensions ke id karung target
			$this->db->where('awb_koli_id', $koli_id)->update('shipment_dimensions', [
				'awb_koli_id' => $final_koli_id
			]);

			// Karena isinya sudah ditumpahkan ke karung eksis, wadah karung lama yang kosong kita hapus
			$this->db->where('id', $koli_id)->delete('awb_koli');
		}

		// ====================================================================
		// 🔥 FIX LOGIC: PENENTUAN STATUS RESI INDUK MENGIKUTI STATUS AWB TARGET
		// ====================================================================
		// Jika AWB barunya masih DRAFT (belum dikunci), status resi diturunkan ke BOOKED
		// Jika AWB barunya sudah MANIFESTED (dikunci), baru status resi jadi MANIFESTED
		$new_shipment_status = ($new_awb->status === 'DRAFT') ? 'CONSOLIDATED' : 'MANIFESTED';
		// ====================================================================

		// 4. Update Informasi Penerbangan Baru & Status Dinamis di Tabel Utama Shipments
		$shipment_ids = array_column($shipments, 'shipment_id');
		$this->db->where_in('id', $shipment_ids)->update('shipments', [
			'status'         => $new_shipment_status, // <--- Sudah dinamis bro!
			'smu_number'     => $new_awb->awb_number,
			'flight_number'  => $new_awb->flight_number,
			'departure_date' => $new_awb->departure_date
		]);

		// 5. Hitung & Sinkronisasi Ulang Timbangan Total Berat Karung Penerima yang Baru
		$this->db->select('sd.shipment_id, s.chargeable_weight, s.koli');
		$this->db->from('shipment_dimensions sd');
		$this->db->join('shipments s', 's.id = sd.shipment_id');
		$this->db->where('sd.awb_koli_id', $final_koli_id);
		$packed_items = $this->db->get()->result();

		$total_berat_baru = 0;
		foreach ($packed_items as $item) {
			$total_berat_baru += ($item->chargeable_weight / $item->koli);
		}

		$this->db->where('id', $final_koli_id)->update('awb_koli', ['actual_weight' => $total_berat_baru]);

		// 6. Log Tracking History dengan pesan yang kontekstual
		$tracking_batch = [];
		foreach ($shipment_ids as $s_id) {
			$note_msg = 'Re-Route! Karung paket dialihkan ke penerbangan baru ' . $new_awb->flight_number . ' dimasukkan ke ' . $final_koli_name . '.';
			if ($new_shipment_status === 'CONSOLIDATED') {
				$note_msg .= ' Menunggu proses finalisasi manifest penerbangan baru.';
			}

			$tracking_batch[] = [
				'shipment_id' => $s_id,
				'status'      => $new_shipment_status,
				'location'    => $new_awb->origin,
				'note'        => $note_msg,
				'created_by'  => $sess['id']
			];
		}
		$this->db->insert_batch('shipment_tracking', $tracking_batch);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => false, 'message' => 'Gagal memproses alokasi karung koli.']);
		} else {
			echo json_encode(['status' => true, 'message' => 'Paket sukses dialihkan ke ' . $final_koli_name . ' pada flight baru!']);
		}
	}

	public function ajax_confirm_arrival()
	{
		$this->_check_access();
		$smu_number = $this->input->post('smu_number', TRUE);
		$sess       = $this->session->userdata('user');

		if (empty($smu_number)) {
			echo json_encode(['status' => false, 'message' => 'Nomor SMU tidak boleh kosong, bro!']);
			return;
		}

		// 1. Ambil data Master AWB berdasarkan nomor SMU yang berstatus terbang (DEPARTED)
		$awb = $this->db->get_where('master_awb', ['awb_number' => $smu_number, 'status' => 'DEPARTED'])->row();
		if (!$awb) {
			echo json_encode(['status' => false, 'message' => 'Data penerbangan aktif tidak ditemukan atau status penerbangan sudah mendarat.']);
			return;
		}

		// 2. Ambil seluruh karung di dalam AWB ini yang statusnya memang ikut terbang (DEPARTED)
		$this->db->where('awb_id', $awb->id);
		$this->db->where('status', 'DEPARTED');
		$kolis = $this->db->get('awb_koli')->result_array();

		if (empty($kolis)) {
			echo json_encode(['status' => false, 'message' => 'Gagal! Tidak ada karung koli berstatus DEPARTED di dalam penerbangan ini.']);
			return;
		}

		$koli_ids = array_column($kolis, 'id');

		// 3. Ambil daftar ID resi unik yang isi koli fisiknya terikat di dalam karung-karung terbang tersebut
		$this->db->select('shipment_id');
		$this->db->from('shipment_dimensions');
		$this->db->where_in('awb_koli_id', $koli_ids);
		$this->db->group_by('shipment_id');
		$shipments = $this->db->get()->result_array();

		// Jaga-jaga jika isinya kosong
		if (empty($shipments)) {
			echo json_encode(['status' => false, 'message' => 'Tidak ada muatan resi di dalam karung yang terbang.']);
			return;
		}

		$this->db->trans_start();

		// 4. Naikkan status dokumen induk Master AWB menjadi ARRIVED
		$this->db->where('id', $awb->id)->update('master_awb', ['status' => 'ARRIVED']);

		// 5. Ubah status karung-karung yang tadinya terbang (DEPARTED) menjadi ARRIVED
		$this->db->where_in('id', $koli_ids)->update('awb_koli', ['status' => 'ARRIVED']);

		// 6. 🔥 EVALUASI AKUMULASI KOLI UNTUK TIAP RESI INDUK (PROPORSIONAL SINKRON)
		$shipment_ids = array_column($shipments, 'shipment_id');
		$tracking_batch = [];

		foreach ($shipment_ids as $s_id) {
			// Hitung total koli fisik asli yang terdaftar dari resi ini
			$total_koli_resi = $this->db->where('shipment_id', $s_id)->count_all_results('shipment_dimensions');

			// Hitung berapa koli fisik resi ini yang karungnya SUDAH BERSTATUS ARRIVED di bandara tujuan
			$this->db->from('shipment_dimensions sd');
			$this->db->join('awb_koli ak', 'ak.id = sd.awb_koli_id');
			$this->db->where('sd.shipment_id', $s_id);
			$this->db->where('ak.status', 'ARRIVED');
			$koli_sampai = $this->db->count_all_results();

			if ($koli_sampai === $total_koli_resi) {
				// Skenario A: Semua dus fisik resi ini sudah lengkap tiba di tujuan
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'ARRIVED']);

				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'ARRIVED',
					'location'    => $awb->destination,
					'note'        => 'Landed! Seluruh koli paket telah mendarat di bandara tujuan ' . $awb->destination . ' dan sedang dalam proses pembongkaran.',
					'created_by'  => $sess['id']
				];
			} else {
				// Skenario B: Kasus Partial! Dus yang sampai baru sebagian (karena dus lainnya kena split di karung lain kemarin)
				$this->db->where('id', $s_id)->update('shipments', ['status' => 'PARTIAL_ARRIVED']);

				$tracking_batch[] = [
					'shipment_id' => $s_id,
					'status'      => 'PARTIAL_ARRIVED',
					'location'    => $awb->destination,
					'note'        => 'Partial Landed! Baru sebanyak ' . $koli_sampai . '/' . $total_koli_resi . ' koli fisik paket yang tiba di ' . $awb->destination . '. Sisa koli terpantau menyusul di kloter pesawat berbeda.',
					'created_by'  => $sess['id']
				];
			}
		}

		// Suntik data riwayat pelacakan secara massal (batch)
		if (!empty($tracking_batch)) {
			$this->db->insert_batch('shipment_tracking', $tracking_batch);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode(['status' => false, 'message' => 'Gagal mengonfirmasi mendarat karena kendala transaksi database.']);
		} else {
			echo json_encode(['status' => true, 'message' => "SMU $smu_number bersama koli muatannya resmi mendarat di " . $awb->destination]);
		}
	}

	public function inbound_scan()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		// 1. Cari Nama Kota Hub Agen ini (Hasil Mapping kita tadi)
		$agent = $this->db->select('c.name')
			->from('agents a')
			->join('cities c', 'c.id = a.city_id')
			->where('a.id', $sess['agent_id'])
			->get()->row();

		if (!$agent) {
			show_error("Agen Anda belum di-mapping ke Kota Hub Kargo. Hubungi Admin Pusat.");
		}

		// 2. Tarik daftar barang yang menuju ke kota ini
		$data = [
			'title'        => 'Inbound: Terima Barang',
			'my_city'      => $agent->name,
			'pending_list' => $this->M_Shipment->get_inbound_pending($agent->name)
		];

		$this->render('app/pages/shipment/inbound_mobile', $data);
	}

	public function ajax_process_inbound()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$input_scan = strtoupper(trim($this->input->post('no_resi', TRUE)));

		if (empty($input_scan)) {
			echo json_encode(['status' => false, 'message' => 'Barcode tidak boleh kosong!']);
			return;
		}

		// ─── 1. PROSES UPLOAD FOTO (PROOF OF CONDITION) DULU ───
		$photo_path = NULL;
		if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

			// KITA PERBAIKI FOLDERNYA DI SINI, BRO! DISERAGAMKAN KE FOLDER /shipments/pod/
			$upload_dir = FCPATH . 'uploads/shipments/pod/';
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0755, TRUE);
			}

			$ext       = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
			$file_name = 'INB_' . preg_replace('/[^A-Za-z0-9\-]/', '', $input_scan) . '_' . time() . '.' . $ext;
			$full_path = $upload_dir . $file_name;

			if (move_uploaded_file($_FILES['photo']['tmp_name'], $full_path)) {
				// Hanya simpan NAMA FILENYA saja (bukan path folder), agar klop dengan fungsi view tracking log
				$photo_path = $file_name;
			}
		}

		$is_complete    = false;
		$received_count = 0;
		$no_resi        = $input_scan;
		$shipment       = null;
		$is_koli_master = false;
		$pesan          = '';

		// GUNAKAN STRUKTUR TRANS_BEGIN AGAR LEBIH KOKOH
		$this->db->trans_begin();

		// ─── 2. LOGIC AUTO-DETECT: APAKAH INI KARUNG ATAU RESI FISIK? ───
		if (strpos($input_scan, 'KOLI-') === 0) {

			$karung = $this->db->get_where('awb_koli', ['koli_number' => $input_scan])->row();

			if (!$karung) {
				$this->db->trans_rollback(); // Amankan state DB sebelum return
				echo json_encode(['status' => false, 'message' => "Karung $input_scan tidak ditemukan di sistem!"]);
				return;
			}

			if ($karung->status === 'RECEIVED_AT_HUB') {
				$this->db->trans_rollback(); // Amankan state DB sebelum return
				echo json_encode(['status' => false, 'message' => "Karung $input_scan sudah pernah diterima sebelumnya!"]);
				return;
			}

			// Update status karung
			$this->db->where('id', $karung->id)->update('awb_koli', [
				'status'        => 'RECEIVED_AT_HUB',
				'photo_inbound' => $photo_path
			]);

			// Ambil semua resi di dalam karung ini
			$this->db->select('shipment_id');
			$this->db->from('shipment_dimensions');
			$this->db->where('awb_koli_id', $karung->id);
			$this->db->group_by('shipment_id');
			$shipments_in_bag = $this->db->get()->result_array();

			if (!empty($shipments_in_bag)) {
				$s_ids = array_column($shipments_in_bag, 'shipment_id');
				$this->db->where_in('id', $s_ids)->update('shipments', ['status' => 'RECEIVED_AT_HUB']);

				$tracking_batch = [];
				foreach ($s_ids as $s_id) {
					$tracking_batch[] = [
						'shipment_id' => $s_id,
						'status'      => 'RECEIVED_AT_HUB',
						'location'    => "Gudang Cabang Tujuan",
						'note'        => "Karung $input_scan berhasil mendarat di Gudang Cabang. Menunggu proses bongkar koli (Breakdown).",
						'photo_proof' => $photo_path, // Masuk ke photo_proof shipment_tracking
						'created_by'  => $sess['id']
					];
				}
				$this->db->insert_batch('shipment_tracking', $tracking_batch);
			}

			$pesan          = "Karung $input_scan beserta isinya berhasil diterima!";
			$is_koli_master = true;
		} else {

			$parts    = explode('-', $input_scan);
			$no_resi  = $parts[0];
			$piece_no = isset($parts[1]) ? $parts[1] : '01';

			$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();

			if (!$shipment) {
				$this->db->trans_rollback();
				echo json_encode(['status' => false, 'message' => "Resi $no_resi tidak terdaftar!"]);
				return;
			}

			// 1. Cek duplikasi scan koli fisik
			$check_piece = $this->db->get_where('shipment_tracking', [
				'shipment_id' => $shipment->id,
				'status'      => 'PIECE_RECEIVED_DESTINATION',
				'note LIKE'   => "%$input_scan%"
			])->num_rows();

			if ($check_piece > 0) {
				$this->db->trans_rollback();
				echo json_encode(['status' => false, 'message' => "Koli fisik $input_scan sudah masuk sistem!"]);
				return;
			}

			// 2. Simpan Log Tracking per Piece + FOTO BUKTI
			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment->id,
				'status'      => 'PIECE_RECEIVED_DESTINATION',
				'location'    => "Gudang {$shipment->destination}",
				'note'        => "Box fisik $input_scan diterima di Cabang Tujuan dan siap disortir.",
				'photo_proof' => $photo_path, // Disimpan ke log piece juga biar kurir bisa cek fisik per koli
				'created_by'  => $sess['id']
			]);

			// 3. Hitung apakah semua koli sudah lengkap?
			$received_count = $this->db->get_where('shipment_tracking', [
				'shipment_id' => $shipment->id,
				'status'      => 'PIECE_RECEIVED_DESTINATION'
			])->num_rows();

			if ($received_count >= $shipment->koli) {
				$this->db->where('id', $shipment->id)->update('shipments', ['status' => 'RECEIVED_DESTINATION']);

				$this->db->insert('shipment_tracking', [
					'shipment_id' => $shipment->id,
					'status'      => 'RECEIVED_DESTINATION',
					'location'    => "Kantor Cabang {$shipment->destination}",
					'note'        => 'Paket diterima lengkap seluruhnya di kantor tujuan. Siap dialokasikan ke kurir pengiriman (Delivery).',
					'photo_proof' => $photo_path, // Di-save ke log induk RECEIVED_DESTINATION juga, bro!
					'created_by'  => $sess['id']
				]);
				$is_complete = true;
			}

			$pesan          = "Koli $piece_no diterima ($received_count/{$shipment->koli})";
			$is_koli_master = false;
		}

		// CEK STATUS TRANSAKSI AKHIR DATABASE
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			// Cleanup file jika query database crash/gagal
			if ($photo_path && file_exists(FCPATH . 'uploads/shipments/pod/' . $photo_path)) {
				unlink(FCPATH . 'uploads/shipments/pod/' . $photo_path);
			}
			echo json_encode(['status' => false, 'message' => 'Gagal memproses data ke database.']);
		} else {
			$this->db->trans_commit(); // Kunci data secara permanen
			echo json_encode([
				'status'      => true,
				'is_koli'     => $is_koli_master,
				'is_complete' => $is_complete,
				'data'        => [
					'no_resi'  => $no_resi,
					'received' => $received_count,
					'total'    => $shipment->koli ?? 0
				],
				'message'     => $pesan,
			]);
		}
	}

	public function ajax_void()
	{
		$this->_check_access(['admin-mitra', 'superadmin', 'admin-kribo']);

		$id = $this->input->post('id');
		$reason = $this->input->post('reason') ?: 'Void by User';

		$shipment = $this->db->get_where('shipments', ['id' => $id])->row();

		if (!$shipment) {
			$this->_respond(['status' => false, 'message' => 'Data tidak ditemukan.'], 404);
		}

		// Pagar keamanan: Hanya bisa VOID jika belum masuk manifes/pesawat
		$locked_status = ['MANIFESTED', 'DEPARTED', 'ARRIVED', 'RECEIVED_DESTINATION', 'DELIVERED'];
		if (in_array($shipment->status, $locked_status)) {
			$this->_respond([
				'status' => false,
				'message' => 'Resi ini sudah masuk proses pengiriman (' . $shipment->status . '). Tidak bisa di-void!'
			], 400);
		}

		// Eksekusi VOID (Hanya ubah status)
		$update = $this->M_Shipment->update_status(
			$id,
			'CANCELLED',
			"VOID: " . $reason,
			'Sistem'
		);

		if ($update) {
			$this->_respond(['status' => true, 'message' => "Resi $shipment->no_resi telah di-void."]);
		}
	}

	public function test_wa()
	{
		$this->_send_finance_wa_notif($shipment, 'APPROVE', $next_status);
	}

	public function autocomplete_customer()
	{
		if ($this->input->server('REQUEST_METHOD') !== 'GET') return;

		$q = $this->input->get('term', TRUE); // term = input dari user

		$this->db->select('id, name, phone, nik, provinsi_id, provinsi_name, kota_id, kota_name, kecamatan_id, kecamatan_name, kelurahan_id, kelurahan_name, address_detail');
		$this->db->like('phone', $q, 'after'); // search by phone prefix
		$this->db->or_like('name', $q, 'both');
		$this->db->limit(8);
		$customers = $this->db->get('master_customers')->result();

		echo json_encode($customers);
	}

	public function manifest_list()
	{
		$this->_check_access();

		// 1. Ambil daftar manifest pickup
		$this->db->select('mp.*, a.name as agent_name');
		$this->db->from('manifest_pickups mp');
		$this->db->join('agents a', 'a.id = mp.agent_id', 'left');

		if (!empty($sess['agent_id'])) {
			$this->db->where('mp.agent_id', $sess['agent_id']);
		}

		$this->db->order_by('mp.created_at', 'DESC');
		$manifests = $this->db->get()->result();

		// 2. Hitung jumlah resi di dalam setiap manifest (biar informatif di tabel)
		foreach ($manifests as $m) {
			$m->total_resi = $this->db->where('manifest_pickup_id', $m->id)
				->count_all_results('manifest_pickup_items');
		}

		$data = [
			'title'     => 'Daftar Manifest Penjemputan',
			'manifests' => $manifests
		];

		$this->render('app/pages/shipment/manifest_index', $data);
	}

	public function preview_manifest()
	{
		$this->_check_access();

		// Ambil data yang SUDAH di-scan supir (PICKED_UP) 
		// DAN belum pernah masuk ke Surat Jalan manapun
		$this->db->select('id, no_resi, sender_name, receiver_name, destination, koli, chargeable_weight, commodity_detail, created_at');
		$this->db->from('shipments');
		$this->db->where('status', 'PICKED_UP');
		$this->db->where('NOT EXISTS (SELECT 1 FROM manifest_pickup_items WHERE manifest_pickup_items.shipment_id = shipments.id)', '', FALSE);

		if (!empty($sess['agent_id'])) {
			$this->db->where('shipments.agent_id', $sess['agent_id']);
		}
		
		$this->db->order_by('destination', 'ASC');

		$shipments = $this->db->get()->result();

		$data = [
			'title'     => 'Preview Manifest Penjemputan',
			'shipments' => $shipments
		];

		$this->render('app/pages/shipment/preview_manifest', $data);
	}

	public function save_manifest()
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		$shipment_ids = $this->input->post('shipment_ids');
		if (empty($shipment_ids)) {
			$this->session->set_flashdata('error', 'Pilih minimal satu resi bro!');
			redirect('shipment/preview_manifest');
		}

		// Generate Nomor Manifest (Contoh: SJP-2604-0001)
		$no_manifest = 'SJP-' . date('ym') . '-' . strtoupper(substr(md5(time()), 0, 4));

		$this->db->trans_start(); // Mulai Transaksi Database

		// 1. Simpan Parent (Header Surat Jalan)
		$data_manifest = [
			'no_manifest'      => $no_manifest,
			'tanggal'          => date('Y-m-d H:i:s'),
			'agent_id'         => $sess['agent_id'] ?? NULL,
			'forwarder_name'   => $this->input->post('forwarder_name', TRUE),
			'forwarder_phone'  => $this->input->post('forwarder_phone', TRUE),
			'receiver_name'    => $this->input->post('receiver_name', TRUE),
			'receiver_address' => $this->input->post('receiver_address', TRUE),
			'status'           => 'PRINTED',
			'created_by'       => $sess['id'],
			'created_at'       => date('Y-m-d H:i:s')
		];
		$this->db->insert('manifest_pickups', $data_manifest);
		$manifest_id = $this->db->insert_id();

		// 2. Simpan Child (Item Resi) & Update Status Shipment
		$items = [];
		foreach ($shipment_ids as $s_id) {
			$items[] = [
				'manifest_pickup_id' => $manifest_id,
				'shipment_id'        => $s_id
			];
		}
		$this->db->insert_batch('manifest_pickup_items', $items);

		$this->db->trans_complete(); // Selesai Transaksi

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal membuat manifest. Coba lagi.');
			redirect('shipment/preview_manifest');
		}

		// Redirect langsung ke halaman print
		redirect('shipment/print_manifest/' . $manifest_id);
	}

	public function print_manifest($manifest_id)
	{
		$this->_check_access();

		// 1. Ambil Data Header Surat Jalan
		$this->db->select('mp.*, a.name as agent_name');
		$this->db->from('manifest_pickups mp');
		$this->db->join('agents a', 'a.id = mp.agent_id', 'left');
		$this->db->where('mp.id', $manifest_id);
		$manifest = $this->db->get()->row();

		// Guard clause kalau ID diketik ngasal di URL
		if (!$manifest) {
			$this->session->set_flashdata('error', 'Data manifest tidak ditemukan!');
			redirect('shipment');
		}

		// 2. Ambil Data Rincian Resi yang Masuk ke Manifest Ini
		$this->db->select('s.no_resi, s.destination, s.sender_name, s.koli, s.chargeable_weight');
		$this->db->from('manifest_pickup_items mpi');
		$this->db->join('shipments s', 's.id = mpi.shipment_id');
		$this->db->where('mpi.manifest_pickup_id', $manifest_id);
		$this->db->order_by('s.destination', 'ASC');
		$items = $this->db->get()->result();

		$data = [
			'title'    => 'Print Surat Jalan',
			'manifest' => $manifest,
			'items'    => $items
		];

		// 3. Load View KHUSUS PRINT (Pakai $this->load->view, bukan render)
		// Biar sidebar dan navbar Tabler nggak ikut ke-print di kertas
		$this->load->view('app/pages/shipment/print_manifest', $data);
	}

	public function export_manifest()
	{
		$this->_check_access(); // Sesuaikan dengan fungsi guard lu

		// 1. Ambil data dari database (Hanya yang READY_TO_PICKUP)
		$this->db->select('no_resi, sender_name, receiver_name, destination, koli, chargeable_weight, commodity_detail');
		$this->db->where('status', 'READY_TO_PICKUP');
		$this->db->order_by('destination', 'ASC'); // Urutkan berdasarkan tujuan biar gampang
		$shipments = $this->db->get('shipments')->result();

		if (empty($shipments)) {
			$this->session->set_flashdata('error', 'Tidak ada barang yang siap dipickup (READY TO PICKUP).');
			redirect('shipment'); // Kembali ke halaman sebelumnya
		}

		require_once FCPATH . 'vendor/autoload.php';

		// 2. Inisiasi PhpSpreadsheet
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('Manifest Penjemputan');

		// 3. Styling Variables (Biar rapi)
		$styleHeader = [
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => ['borderStyle' => Border::BORDER_THIN],
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => '0052CC'] // Warna biru khas Smesco
			],
		];

		$styleBorderAll = [
			'borders' => [
				'allBorders' => ['borderStyle' => Border::BORDER_THIN],
			],
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER,
			],
		];

		// 4. Judul Dokumen
		$sheet->setCellValue('A1', 'SURAT JALAN / MANIFEST PENJEMPUTAN');
		$sheet->mergeCells('A1:I1');
		$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		$sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d M Y H:i'));
		$sheet->mergeCells('A2:I2');
		$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		// 5. Header Tabel
		$sheet->setCellValue('A4', 'NO');
		$sheet->setCellValue('B4', 'CEK'); // Kolom Checklist
		$sheet->setCellValue('C4', 'NO. RESI (AWB)');
		$sheet->setCellValue('D4', 'KOTA TUJUAN');
		$sheet->setCellValue('E4', 'PENGIRIM');
		$sheet->setCellValue('F4', 'KOLI');
		$sheet->setCellValue('G4', 'BERAT (Kg)');
		$sheet->setCellValue('H4', 'ISI BARANG');
		$sheet->setCellValue('I4', 'KETERANGAN');

		$sheet->getStyle('A4:I4')->applyFromArray($styleHeader);

		// 6. Isi Data
		$row = 5;
		$no = 1;
		$total_koli = 0;
		$total_berat = 0;

		foreach ($shipments as $s) {
			$sheet->setCellValue('A' . $row, $no);
			// Simbol Checklist kotak kosong. Kalau di-print sangat jelas.
			$sheet->setCellValue('B' . $row, '[   ]');
			$sheet->setCellValue('C' . $row, $s->no_resi);
			$sheet->setCellValue('D' . $row, $s->destination);
			$sheet->setCellValue('E' . $row, $s->sender_name);
			$sheet->setCellValue('F' . $row, $s->koli);
			$sheet->setCellValue('G' . $row, $s->chargeable_weight);
			$sheet->setCellValue('H' . $row, $s->commodity_detail);
			$sheet->setCellValue('I' . $row, ''); // Kosong untuk notes tulisan tangan

			// Center align untuk beberapa kolom
			$sheet->getStyle("A$row:D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle("F$row:G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

			$total_koli += $s->koli;
			$total_berat += $s->chargeable_weight;

			$row++;
			$no++;
		}

		// 7. Beri Border ke Seluruh Data
		$sheet->getStyle('A4:I' . ($row - 1))->applyFromArray($styleBorderAll);

		// 8. Baris Total (Footer Tabel)
		$sheet->setCellValue('A' . $row, 'TOTAL');
		$sheet->mergeCells("A$row:E$row");
		$sheet->setCellValue('F' . $row, $total_koli);
		$sheet->setCellValue('G' . $row, $total_berat);

		$sheet->getStyle("A$row:I$row")->applyFromArray($styleHeader); // Pakai style header biar tebal
		$sheet->getStyle("A$row:E$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

		// 9. Area Tanda Tangan Serah Terima
		$row += 3;
		$sheet->setCellValue('B' . $row, 'Diserahkan Oleh (Smesco),');
		$sheet->setCellValue('G' . $row, 'Diterima Oleh (Kurir Bandara),');

		$sheet->getStyle('B' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('B' . $row . ':G' . $row)->getFont()->setBold(true);

		$row += 4; // Kasih jarak buat ttd
		$sheet->setCellValue('B' . $row, '(.......................................)');
		$sheet->setCellValue('G' . $row, '(.......................................)');
		$sheet->getStyle('B' . $row . ':G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

		// 10. Auto-Size Lebar Kolom
		foreach (range('A', 'I') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		// 11. Eksekusi Output ke Excel (Download)
		$filename = 'Manifest_Pickup_' . date('Ymd_Hi') . '.xlsx';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit;
	}

	public function update_to_delivered()
	{
		// Validasi request AJAX
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$shipment_id = $this->input->post('shipment_id');
		$sess = $this->session->userdata('user'); // Mengambil ID dari session pemicu (sesuaikan key session kamu, misal $sess['id'])

		if (empty($shipment_id)) {
			echo json_encode(['status' => 'error', 'message' => 'ID Transaksi tidak valid.']);
			exit;
		}

		// Ambil rute destination resi ini untuk dijadikan 'location' di tracking log
		$shipment = $this->db->get_where('shipments', ['id' => $shipment_id])->row();
		$location = $shipment ? $shipment->destination : 'DESTINATION';

		// Tentukan path folder tujuan
		$upload_path = './uploads/pod/';

		// JIKA FOLDER BELUM ADA, CREATE OTOMATIS
		if (!is_dir($upload_path)) {
			if (!mkdir($upload_path, 0755, true)) {
				echo json_encode(['status' => 'error', 'message' => 'Gagal membuat folder penyimpanan di server. Hubungi tim IT.']);
				exit;
			}
		}

		// Konfigurasi Upload Gambar ke Folder server
		$config['upload_path']   = $upload_path;
		$config['allowed_types'] = 'jpg|jpeg|png';
		$config['max_size']      = 3072; // Maksimal ukuran 3MB
		$config['file_name']     = 'POD_' . $shipment_id . '_' . time();

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('pod_image')) {
			// Jika gagal upload gambar
			$error = $this->upload->display_errors('', '');
			echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah foto: ' . $error]);
			exit;
		} else {
			// Jika sukses upload, dapatkan nama filenya
			$upload_data = $this->upload->data();
			$file_name   = $upload_data['file_name'];

			// Mulai database transaction agar jika salah satu query gagal, data aman tidak corupt
			$this->db->trans_begin();

			// 1. Update tabel utama 'shipments' (hanya status dan waktu diperbarui)
			$update_data = [
				'status'     => 'DELIVERED',
				'updated_at' => date('Y-m-d H:i:s')
			];
			$this->db->where('id', $shipment_id);
			$this->db->update('shipments', $update_data);

			// 2. Insert riwayat tracking log ke tabel 'shipment_tracking' beserta foto bukti (photo_proof)
			$tracking_data = [
				'shipment_id' => $shipment_id,
				'status'      => 'DELIVERED',
				'location'    => $location,
				'note'        => 'Paket telah sukses diterima oleh yang bersangkutan. (DELIVERED)',
				'photo_proof' => $file_name, // Foto disimpan di tabel tracking sekarang, bro!
				'created_by'  => $sess['id'],
				'created_at'  => date('Y-m-d H:i:s')
			];
			$this->db->insert('shipment_tracking', $tracking_data);

			// Cek status transaksi database
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();

				// Hapus file foto yang terupload jika query transaksi database gagal
				if (file_exists($upload_path . $file_name)) {
					unlink($upload_path . $file_name);
				}

				echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui riwayat status di database.']);
			} else {
				$this->db->trans_commit();
				echo json_encode(['status' => 'success', 'message' => 'Status resi berhasil diubah menjadi DELIVERED dan tracking log telah diperbarui.']);
			}
			exit;
		}
	}
}
