<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shipment extends Authenticated_Controller
{
	protected $allowed_roles = ['admin-kribo', 'finance-kribo', 'admin-mitra', 'staff-mitra', 'checker', 'driver'];

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['pdfgenerator']);
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
			$pickup_fee    = 0;
			$pickup_margin = 0;
			$pickup_id     = NULL;

			if ($this->input->post('use_pickup') == 1) {
				$p_id = $this->input->post('pickup_rate_id');
				$rate_db = $this->db->get_where('master_pickup_rates', ['id' => $p_id])->row();

				if ($rate_db && $chargeable >= $rate_db->min_weight) {
					$pickup_id     = $p_id;
					$pickup_fee    = $rate_db->price_smesco;
					$pickup_margin = ($rate_db->price_smesco - $rate_db->price_kribo);
				}
			}

			// 7. Kalkulasi Final & Persiapan Data
			$no_resi      = $this->M_Shipment->generate_no_resi();
			$sess         = $this->session->userdata('user');
			$is_valuable  = $this->input->post('is_valuable') ? 1 : 0;
			$total_margin = $shipping_margin + $pickup_margin;

			$insert_shipment = [
				'no_resi'           => $no_resi,
				'agent_id'          => $sess['agent_id'] ?? NULL,
				'origin'            => $origin,
				'destination'       => $destination,
				'service_type_id'   => $service_id,
				'category'          => $pricelist->category,
				'sender_name'       => $this->input->post('sender_name', TRUE),
				'sender_phone'      => $this->input->post('sender_phone', TRUE),
				'sender_address'    => $this->input->post('sender_address', TRUE),
				'receiver_name'     => $this->input->post('receiver_name', TRUE),
				'receiver_phone'    => $this->input->post('receiver_phone', TRUE),
				'receiver_address'  => $this->input->post('receiver_address', TRUE),
				'commodity_id'      => $this->input->post('commodity_id', TRUE),
				'commodity_detail'  => $this->input->post('commodity_detail', TRUE),
				'is_valuable'       => $is_valuable,
				'goods_value'       => $is_valuable ? $this->_parse_indo_number($this->input->post('goods_value')) : 0,
				'payment_type'      => $this->input->post('payment_type', TRUE),
				'koli'              => $total_koli ?: 1,
				'actual_weight'     => $actual_weight,
				'volume_weight'     => $total_volume_weight,
				'chargeable_weight' => $chargeable,
				'cost_price'        => $cost_per_kg,
				'sell_price'        => $sell_per_kg,
				'pickup_rate_id'    => $pickup_id,
				'pickup_fee'        => $pickup_fee,
				'total_amount'      => $shipping_total + $pickup_fee,
				'margin_amount'     => $total_margin,
				'status'            => 'BOOKED',
				'created_by'        => $sess['id']
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

			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => 'BOOKED',
				'location'    => $origin,
				'note'        => 'Shipment berhasil dibuat.',
				'created_by'  => $sess['id']
			]);
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$this->session->set_flashdata('error', 'Gagal menyimpan data.');
				redirect('shipment/create');
			} else {
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
			'commodities' => $this->db->get_where('master_commodities', ['is_active' => 1])->result()
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

		$this->load->library('ciqrcode');
		$this->load->library('cibarcode');
		$tempDir = sys_get_temp_dir();

		// 1. Logo Base64 (Cukup sekali saja)
		$logoPath = FCPATH . 'assets/logo/icon-smesco.png';
		$logoBase64 = (file_exists($logoPath)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

		// 2. QR Tracking (Sama untuk semua koli karena merujuk ke halaman tracking yang sama)
		$linkTracking = base_url("home/track/$no_resi");
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
}
