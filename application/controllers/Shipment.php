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

			echo '<pre>';
			print_r($_POST);
			echo '</pre>';
			exit;
			// 1. Dapatkan Input Dasar
			$origin      = $this->input->post('origin', TRUE);
			$destination = $this->input->post('destination', TRUE);
			$service_id  = $this->input->post('service_type_id', TRUE);
			$sender_name  = $this->input->post('sender_name', TRUE);  // <-- TAMBAH INI
			$sender_phone = $this->input->post('sender_phone', TRUE);  // <-- TAMBAH INI
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

			// 3. Hitung Berat (Actual vs Volume)
			$actual_weight = $this->_parse_indo_number($this->input->post('actual_weight'));
			$dim_qtys      = $this->input->post('dim_qty');
			$dim_lengths   = $this->input->post('dim_length');
			$dim_widths    = $this->input->post('dim_width');
			$dim_heights   = $this->input->post('dim_height');

			$total_volume_weight = 0;
			$total_koli = 0;
			$insert_dimensions = [];

			if (!empty($dim_qtys)) {
				foreach ($dim_qtys as $i => $qty_val) {
					$q = intval($this->_parse_indo_number($qty_val));
					$p = $this->_parse_indo_number($dim_lengths[$i]);
					$l = $this->_parse_indo_number($dim_widths[$i]);
					$t = $this->_parse_indo_number($dim_heights[$i]);

					if ($q > 0) {
						$total_koli += $q;
						$row_vol = (($p * $l * $t) / 5000) * $q;
						$total_volume_weight += $row_vol;

						if ($p > 0 || $l > 0 || $t > 0) {
							$insert_dimensions[] = [
								'qty'    => $q,
								'length' => $p,
								'width'  => $l,
								'height' => $t,
							];
						}
					}
				}
			}

			// 4. Tentukan Chargeable Weight (Pembulatan ke Atas)
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

			// 7. Kalkulasi Final & Persiapan Data
			$sess         = $this->session->userdata('user');
			$is_valuable  = $this->input->post('is_valuable') ? 1 : 0;
			$addon_codes      = $this->input->post('addons') ?: [];
			$total_addon_fee  = 0;
			$insert_addons    = [];

			if (!empty($addon_codes)) {
				// Ambil semua addon yang dipilih sekaligus (1 query)
				$this->db->where_in('code', $addon_codes)->where('is_active', 1);
				$selected_addons = $this->db->get('master_addons')->result();

				foreach ($selected_addons as $addon) {
					$fee = 0;

					// Hitung fee per addon berdasarkan calc_method — mirror logic JS
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
						// Fallback: tidak ada dimensi, pakai total_koli
						if ($addon->calc_method === 'PER_KOLI') {
							$fee = $addon->base_factor * ($total_koli ?: 1);
						}
					}

					if ($fee > 0) {
						$total_addon_fee += $fee;
						$insert_addons[] = [
							'addon_id'     => $addon->id,
							'addon_amount' => $fee,
							// shipment_id akan di-inject setelah insert
						];
					}
				}
			}

			$total_margin = $shipping_margin; // margin mitra hanya dari biaya pengiriman

			// Helper: ambil nama wilayah dari ID
			$sender_prov_name  = $this->db->get_where('mt_provinsi',   ['id' => $this->input->post('sender_provinsi')])->row();
			$sender_kota_name  = $this->db->get_where('mt_kota',       ['id' => $this->input->post('sender_kota')])->row();
			$sender_kec_name   = $this->db->get_where('mt_kecamatan',  ['id' => $this->input->post('sender_kecamatan')])->row();
			$sender_kel_name   = $this->db->get_where('mt_kelurahan',  ['id' => $this->input->post('sender_kelurahan')])->row();

			$receiver_prov_name = $this->db->get_where('mt_provinsi',  ['id' => $this->input->post('receiver_provinsi')])->row();
			$receiver_kota_name = $this->db->get_where('mt_kota',      ['id' => $this->input->post('receiver_kota')])->row();
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

			// ── Upload Foto Barang ──
			$photo_path = NULL;
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

				// Simpan dulu tmp_name-nya, upload setelah no_resi di-generate
				$pending_upload = [
					'tmp_name' => $file['tmp_name'],
					'ext'      => strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)),
				];
			}

			// ← Generate no_resi di SINI, setelah semua validasi lewat
			$no_resi = $this->M_Shipment->generate_no_resi();

			// ← Baru eksekusi move file-nya kalau ada pending upload
			if (!empty($pending_upload)) {
				$upload_dir = FCPATH . 'uploads/shipments/' . date('Y') . '/' . date('m') . '/';

				if (!is_dir($upload_dir)) {
					mkdir($upload_dir, 0755, TRUE);
				}

				$file_name = $no_resi . '_photo.' . $pending_upload['ext'];
				$full_path = $upload_dir . $file_name;

				if (!move_uploaded_file($pending_upload['tmp_name'], $full_path)) {
					$this->session->set_flashdata('error', 'Gagal mengupload foto. Coba lagi bro.');
					redirect('shipment/create');
				}

				$photo_path = 'uploads/shipments/' . date('Y') . '/' . date('m') . '/' . $file_name;
			}

			// Tentukan Expired Date (Hanya jika tipe pembayaran TRANSFER)
			$payment_expired_at = NULL;
			if ($payment_type === 'TRANSFER') {
				// Set batas waktu 10 menit dari sekarang
				$payment_expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
			}

			$insert_shipment = [
				'no_resi'           		  => $no_resi,
				'agent_id'          		  => $sess['agent_id'] ?? NULL,
				'origin'            		  => $origin,
				'destination'       		  => $destination,
				'service_type_id'   		  => $service_id,
				'category'          		  => $pricelist->category,
				'sender_name'             => $this->input->post('sender_name', TRUE),
				'sender_phone'            => $this->input->post('sender_phone', TRUE),
				'sender_provinsi'   		  => $sender_prov_name  ? $sender_prov_name->nama_provinsi  : NULL,
				'sender_kota'       		  => $sender_kota_name  ? $sender_kota_name->nama_kota      : NULL,
				'sender_kecamatan'  		  => $sender_kec_name   ? $sender_kec_name->nama_kecamatan  : NULL,
				'sender_kelurahan'  		  => $sender_kel_name   ? $sender_kel_name->nama_kelurahan  : NULL,
				'sender_address_detail'   => $this->input->post('sender_address_detail', TRUE),
				'sender_address'          => $full_sender_address,
				'receiver_name'           => $this->input->post('receiver_name', TRUE),
				'receiver_phone'          => $this->input->post('receiver_phone', TRUE),
				'receiver_provinsi'  	  => $receiver_prov_name  ? $receiver_prov_name->nama_provinsi  : NULL,
				'receiver_kota'      	  => $receiver_kota_name  ? $receiver_kota_name->nama_kota      : NULL,
				'receiver_kecamatan' 	  => $receiver_kec_name   ? $receiver_kec_name->nama_kecamatan  : NULL,
				'receiver_kelurahan' 	  => $receiver_kel_name   ? $receiver_kel_name->nama_kelurahan  : NULL,
				'receiver_address_detail' => $this->input->post('receiver_address_detail', TRUE),
				'receiver_address'        => $full_receiver_address,
				'commodity_id'      		  => $this->input->post('commodity_id', TRUE),
				'commodity_detail'  		  => $this->input->post('commodity_detail', TRUE),
				'is_valuable'       		  => $is_valuable,
				'goods_value'       		  => $is_valuable ? $this->_parse_indo_number($this->input->post('goods_value')) : 0,
				'payment_type'      		  => $payment_type,
				'koli'              		  => $total_koli ?: 1,
				'actual_weight'     		  => $actual_weight,
				'volume_weight'     		  => $total_volume_weight,
				'chargeable_weight' 		  => $chargeable,
				'cost_price'        		  => $cost_per_kg,
				'sell_price'        		  => $sell_per_kg,
				'pickup_rate_id'    		  => $pickup_id,
				'pickup_fee'        		  => $pickup_fee,
				'total_addon_fee'   		  => $total_addon_fee,
				'total_amount'      		  => $shipping_total + $pickup_fee + $total_addon_fee,
				'margin_amount'     		  => $total_margin,
				'shipment_photo' 			  => $photo_path,
				'payment_expired_at'  	  => $payment_expired_at,
				'status'            		  => 'BOOKED',
				'created_by'        		  => $sess['id']
			];

			// 8. Database Transaction
			$this->db->trans_start();
			$this->db->insert('shipments', $insert_shipment);
			$shipment_id = $this->db->insert_id();

			if (!empty($insert_dimensions)) {
				foreach ($insert_dimensions as &$dim) {
					$dim['shipment_id'] = $shipment_id;
				}
				$this->db->insert_batch('shipment_dimensions', $insert_dimensions);
			}

			if (!empty($insert_addons)) {
				foreach ($insert_addons as &$addon_row) {
					$addon_row['shipment_id'] = $shipment_id;
				}
				$this->db->insert_batch('shipment_addons', $insert_addons);
			}

			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => 'BOOKED',
				'location'    => $origin,
				'note'        => 'Shipment berhasil dibuat.',
				'created_by'  => $sess['id']
			]);
			$this->db->trans_complete();

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
				$this->session->set_flashdata('error', 'Gagal menyimpan data.');
				redirect('shipment/create');
			} else {
				if ($payment_type === 'TRANSFER') {
					$url = base_url('home/confirm_payment/' . $no_resi);
					$total_rp = number_format($shipping_total + $pickup_fee + $total_addon_fee, 0, ',', '.');

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

		// Tampilan Form
		$data = [
			'title'       => 'Buat Booking',
			'cities'      => $this->M_Pricelist->get_cities(),
			'services'    => $this->M_Pricelist->get_services(),
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result(),
			'addons'      => $this->db->get_where('master_addons', ['is_active' => 1])->result()
		];
		$this->render('app/pages/shipment/create', $data);
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

	public function manifest()
	{
		$this->_check_access();

		$filters = [
			'status' => $this->input->get('status', TRUE),
		];

		$data = [
			'title'     => 'Manajemen Manifest & Flight',
			'manifests' => $this->M_Shipment->get_manifest_list($filters),
			'filters'   => $filters,
		];

		$this->render('app/pages/shipment/manifest', $data);
	}

	public function ajax_confirm_arrival()
	{
		$this->_check_access();
		$smu_number = $this->input->post('smu_number', TRUE);

		if (empty($smu_number)) {
			echo json_encode(['status' => false, 'message' => 'Nomor SMU tidak valid!']);
			return;
		}

		$res = $this->M_Shipment->arrive_by_smu($smu_number);

		if ($res) {
			echo json_encode(['status' => true, 'message' => "SMU $smu_number berhasil dikonfirmasi mendarat."]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Gagal memproses data kedatangan.']);
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
		$input_scan = $this->input->post('no_resi', TRUE); // Contoh: SMC...-01
		$parts = explode('-', $input_scan);
		$no_resi = $parts;

		$no_resi  = $parts[0];                          // ✅ "SMC2604050001"
		$piece_no = isset($parts[1]) ? $parts[1] : '01';

		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();

		if (!$shipment) {
			echo json_encode(['status' => false, 'message' => 'Resi tidak terdaftar!']);
			return;
		}

		// 1. Cek duplikasi scan koli
		$check_piece = $this->db->get_where('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => 'PIECE_RECEIVED_DESTINATION',
			'note LIKE'   => "%$input_scan%"
		])->num_rows();

		if ($check_piece > 0) {
			echo json_encode(['status' => false, 'message' => "Koli $input_scan sudah masuk sistem!"]);
			return;
		}

		// 2. Simpan Log Koli
		$this->M_Shipment->insert_tracking_log($shipment->id, 'PIECE_RECEIVED_DESTINATION', "Box $input_scan diterima di Cabang Tujuan.", "Gudang {$shipment->destination}");

		// 3. Hitung apakah sudah lengkap?
		$received_count = $this->db->get_where('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => 'PIECE_RECEIVED_DESTINATION'
		])->num_rows();

		$is_complete = false;
		if ($received_count >= $shipment->koli) {
			// UPDATE STATUS MASTER
			$this->M_Shipment->update_status($shipment->id, 'RECEIVED_DESTINATION', 'Paket diterima lengkap di kantor tujuan. Siap dikirim.', "Kantor Cabang {$shipment->destination}");
			$is_complete = true;
		}

		echo json_encode([
			'status'      => true,
			'is_complete' => $is_complete,
			'data'        => ['no_resi' => $shipment->no_resi, 'received' => $received_count, 'total' => $shipment->koli],
			'message' => "Koli $piece_no diterima ($received_count/{$shipment->koli})",
		]);
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
}
