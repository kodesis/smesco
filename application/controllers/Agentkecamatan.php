<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AgentKecamatan extends Agent_Controller
{

	protected $allowed_roles = ['admin-kribo', 'finance-kribo', 'admin-mitra', 'staff-mitra', 'checker', 'driver', 'admin-mitra-kecamatan'];
	public function __construct()
	{
		parent::__construct();

		$this->load->model('M_Shipment');
		$this->load->model('M_Agent');
		$this->load->model('M_Pricelist');
		$this->load->model('M_AgentSettlement');
	}

	/**
	 * ============================================================
	 * DAFTAR PENGIRIMAN AGEN KECAMATAN
	 * ============================================================
	 */
	public function index()
	{
		$this->load->model('M_AgentSettlement');
		$agent_id = $this->agent_id;

		$status = $this->input->get('status', TRUE);
		$q      = trim($this->input->get('q', TRUE) ?? '');
		$quick  = $this->input->get('quick', TRUE);
		$quick  = in_array($quick, ['today', 'unpaid'], TRUE) ? $quick : '';

		$this->db
			->select('s.*, st.name AS service_name, mc.name AS commodity_name')
			->from('shipments s')
			->join('service_types st', 'st.id = s.service_type_id', 'left')
			->join('master_commodities mc', 'mc.id = s.commodity_id', 'left')
			->where('s.agent_id', $agent_id)
			->where('s.category', 'INTERNATIONAL');

		if (!empty($status)) {
			$this->db->where('s.status', $status);
		}

		if ($q !== '') {
			$this->db->group_start()
				->like('s.no_resi', $q)
				->or_like('s.sender_name', $q)
				->or_like('s.receiver_name', $q)
				->or_like('s.destination', $q)
				->group_end();
		}

		if ($quick === 'today') {
			$this->db->where('s.created_at >=', date('Y-m-d 00:00:00'))
				->where('s.created_at <', date('Y-m-d 00:00:00', strtotime('+1 day')));
		} elseif ($quick === 'unpaid') {
			$this->db->group_start()
				->where('s.payment_status IS NULL', NULL, FALSE)
				->or_where('s.payment_status !=', 'PAID')
				->group_end()
				->where('s.status !=', 'CANCELLED');
		}

		$total    = $this->db->count_all_results('', FALSE);   // SETELAH semua filter
		$paginate = $this->paginate($total, 15, ['q' => $q, 'status' => $status, 'quick' => $quick]);

		$shipments = $this->db
			->order_by('s.created_at', 'DESC')
			->limit($paginate['per_page'], $paginate['offset'])
			->get()->result();

		$data = array_merge([
			'title'       => 'Pengiriman Internasional',
			'segment'     => 'shipment-kecamatan',
			'shipments'   => $shipments,
			'total'       => $total,
			'filters'     => ['q' => $q, 'status' => $status, 'quick' => $quick],
			'stats'       => $this->M_Shipment->get_agent_kecamatan_stats($agent_id),
			'outstanding' => $this->M_AgentSettlement->get_summary($agent_id),
			'can_settle'    => $this->_can_settle(),
		], $paginate);

		$this->render('app/pages/agent_kecamatan/index', $data);
	}


	/**
	 * ============================================================
	 * FORM BUAT PENGIRIMAN
	 * ============================================================
	 */
	public function create()
	{
		$agent_id = $this->agent_id;

		$agent = $this->M_Agent->get_detail($agent_id);

		if (!$agent) {
			show_error('Data agen tidak ditemukan.', 404);
		}

		$kec = $this->_get_agent_kecamatan($agent);

		$data = [
			'title'       => 'Kirim Barang Internasional',
			'agent'       => $agent,
			'pricing_origin'    => M_Shipment::KEC_PRICING_ORIGIN,
			'agent_origin'      => $kec->nama_kecamatan ?? NULL,
			'agent_origin_name' => $kec->nama_kecamatan ?? NULL,

			'countries'   => $this->db
				->select('destination')
				->where('category', 'INTERNATIONAL')
				->where('is_active', 1)
				->group_by('destination')
				->order_by('destination', 'ASC')
				->get('pricelist')
				->result(),

			'services'    => $this->M_Pricelist->get_services('INTERNATIONAL'),

			'commodities' => $this->db
				->where('is_active', 1)
				->order_by('name', 'ASC')
				->get('master_commodities')
				->result(),

			'addons'      => $this->db
				->where('is_active', 1)
				->order_by('name', 'ASC')
				->get('master_addons')
				->result(),

			'sess'        => $this->current_user,
		];

		$this->render(
			'app/pages/agent_kecamatan/create',
			$data
		);
	}


	/**
	 * ============================================================
	 * SIMPAN PENGIRIMAN
	 * ============================================================
	 *
	 * Catatan:
	 * - International ONLY
	 * - agent_id selalu dari session
	 * - tidak ada upload bukti pembayaran
	 * - payment_status awal = UNPAID
	 * - status awal = BOOKED
	 * - setelah confirm_payment -> READY_FOR_HANDOVER
	 */
	/**
	 * Simpan pengiriman internasional dari Agen Kecamatan.
	 *
	 * Pricelist TIDAK lagi di-query di sini: semua perhitungan harga
	 * dilakukan sekali di M_Shipment::create_shipment_kecamatan().
	 */
	public function save()
	{
		$destination = trim((string) $this->input->post('destination_country', TRUE));
		$service_id  = (int) $this->input->post('service_type_id', TRUE);

		if ($destination === '' || $service_id <= 0) {
			$this->_save_back('Tujuan dan layanan pengiriman wajib diisi.');
		}

		$agent = $this->M_Agent->get_detail($this->agent_id);

		if (!$agent) {
			show_error('Data agen tidak ditemukan.', 404);
		}

		/*
		 * Agen wajib punya kota asal yang aktif.
		 * Sumber kebenaran adalah DB, bukan value dari browser.
		 */
		$kec = $this->_get_agent_kecamatan($agent);

		if (!$kec || trim((string) $kec->nama_kecamatan) === '') {
			$this->_save_back('Kecamatan asal agen belum terdaftar.');
		}

		$result = $this->M_Shipment->create_shipment_kecamatan([
			'agent_id'    => $this->agent_id,
			'user_id'     => (int) $this->current_user['id'],
			'origin'      => "JAKARTA",
			'agent_origin' => $kec->nama_kecamatan,
			'destination' => $destination,
			'service_id'  => $service_id,
			'post'        => $this->input->post(NULL, TRUE),
			'files'       => $_FILES,
		]);

		if (empty($result['status'])) {
			$this->_save_back($result['message']);
		}

		$this->session->set_flashdata(
			'success',
			'Pengiriman berhasil dibuat. Resi: <b>' . html_escape($result['no_resi']) . '</b>'
		);

		redirect('agentkecamatan/detail/' . $result['shipment_id']);
	}

	/**
	 * Flash error + kembali ke form.
	 * redirect() memanggil exit, jadi tidak perlu `return` setelahnya.
	 * Prefix "_" membuat method ini tidak bisa diakses lewat URL di CI3.
	 */
	private function _save_back($message)
	{
		$this->session->set_flashdata('error', $message);
		redirect('agentkecamatan/create');
	}


	/**
	 * ============================================================
	 * DETAIL PENGIRIMAN
	 * ============================================================
	 */
	public function detail($id)
	{
		$agent_id = $this->agent_id;

		$shipment = $this->M_Shipment->get_by_id($id);

		if (!$shipment) {
			$this->session->set_flashdata(
				'error',
				'Data shipment tidak ditemukan.'
			);

			redirect('agentkecamatan');
			return;
		}

		/*
     * Pastikan shipment memang milik Agen Kecamatan
     */
		if ((int) $shipment['agent_id'] !== (int) $agent_id) {
			$this->session->set_flashdata(
				'error',
				'Anda tidak memiliki akses ke shipment tersebut.'
			);

			redirect('agentkecamatan');
			return;
		}

		/*
     * Hanya shipment INTERNATIONAL
     */
		if ($shipment['category'] !== 'INTERNATIONAL') {
			$this->session->set_flashdata(
				'error',
				'Shipment bukan merupakan shipment internasional.'
			);

			redirect('agentkecamatan');
			return;
		}


		if (!empty($shipment['vendor']) && !empty($shipment['vendor_connote'])) {
			$this->M_Shipment->sync_vendor_tracking($id);
		}

		$data = [
			'title'      => 'Detail Shipment Kecamatan - ' . $shipment['no_resi'],
			'shipment'   => $shipment,
			'dimensions' => $this->M_Shipment->get_dimensions($id),
			'history'    => $this->M_Shipment->get_tracking_public($id),
		];

		$this->render(
			'app/pages/agent_kecamatan/detail',
			$data
		);
	}


	/**
	 * ============================================================
	 * KONFIRMASI PEMBAYARAN
	 * ============================================================
	 *
	 * Tidak ada upload bukti transfer.
	 * Agen langsung menyatakan pembayaran sudah diterima.
	 */
	public function confirm_payment()
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_response(false, 'Method tidak diizinkan.');
		}

		$id = (int) $this->input->post('id');
		if ($id <= 0) {
			return $this->_json_response(false, 'ID shipment tidak valid.');
		}

		$agent_id = (int) $this->agent_id;
		$user_id  = (int) $this->current_user['id'];

		$shipment = $this->db
			->select('id, no_resi, status, payment_status, agent_origin')
			->where('id', $id)
			->where('agent_id', $agent_id)
			->where('category', 'INTERNATIONAL')
			->get('shipments')
			->row_array();

		if (!$shipment) {
			return $this->_json_response(false, 'Shipment tidak ditemukan.');
		}
		if ($shipment['payment_status'] === 'PAID') {
			return $this->_json_response(false, 'Pembayaran sudah dikonfirmasi.');
		}
		if ($shipment['status'] !== 'BOOKED') {
			return $this->_json_response(false, 'Status saat ini: ' . $shipment['status']);
		}

		$now = date('Y-m-d H:i:s');
		$this->db->trans_start();

		$this->db
			->where('id', $id)
			->where('agent_id', $agent_id)
			->where('category', 'INTERNATIONAL')
			->where('status', 'BOOKED')
			->where('payment_status !=', 'PAID')
			->update('shipments', [
				'payment_status'       => 'PAID',
				'payment_confirmed_by' => $user_id,
				'payment_confirmed_at' => $now,
				'status'               => 'READY_FOR_HANDOVER',
				'updated_by'           => $user_id,
				'updated_at'           => $now,
			]);

		if ($this->db->affected_rows() !== 1) {
			$this->db->trans_rollback();
			return $this->_json_response(false, 'Shipment sudah diproses sebelumnya.');
		}

		$this->db->insert('shipment_tracking', [
			'shipment_id' => $id,
			'status'      => 'READY_FOR_HANDOVER',
			'location'    => $shipment['agent_origin'] ?: 'AGEN KECAMATAN',
			'note'        => 'Pembayaran shipment dikonfirmasi oleh Agen Kecamatan.',
			'created_by'  => $user_id,
			'created_at'  => $now,
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			return $this->_json_response(false, 'Konfirmasi pembayaran gagal disimpan.');
		}

		return $this->_json_response(true, 'Pembayaran berhasil dikonfirmasi.');
	}


	/**
	 * ============================================================
	 * CETAK LABEL
	 * ============================================================
	 *
	 * Tidak membuat ulang template A6.
	 * Kita validasi ownership dulu, lalu gunakan print_label existing.
	 */
	public function print_label($id = NULL)
	{
		if ($id === NULL || !ctype_digit((string) $id)) {
			show_404();
			return;
		}

		$this->load->model('M_AgentKecamatan');

		// Scoped di SQL: shipment agen lain tidak akan pernah ketemu.
		$resi = $this->M_AgentKecamatan->get_by_id((int) $id, (int) $this->agent_id);

		if (!$resi || strtoupper((string) $resi['category']) !== 'INTERNATIONAL') {
			show_404();
			return;
		}

		if ($resi['payment_status'] !== 'PAID') {
			$this->session->set_flashdata(
				'error',
				'Label hanya dapat dicetak setelah pembayaran dikonfirmasi.'
			);
			redirect('agentkecamatan/detail/' . (int) $id);
		}

		if ($resi['status'] === 'CANCELLED') {
			$this->session->set_flashdata('error', 'Shipment dengan status CANCELLED tidak bisa dicetak.');
			redirect('agentkecamatan/detail/' . (int) $id);
		}

		$this->load->library(['ciqrcode', 'cibarcode', 'pdfgenerator']);

		$no_resi = $resi['no_resi'];
		$tempDir = sys_get_temp_dir();
		$uniq    = uniqid('', TRUE);

		// Logo
		$logoPath   = FCPATH . 'assets/logo/logo-smesco-hera-2-small.png';
		$logoBase64 = file_exists($logoPath)
			? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
			: '';

		// QR tracking (sama untuk semua koli)
		$pathTracking = $tempDir . DIRECTORY_SEPARATOR . 'qr_trc_' . $uniq . '.png';
		$this->ciqrcode->generate([
			'data'     => base_url('home/tracking?awb=' . $no_resi),
			'level'    => 'M',
			'size'     => 4,
			'savename' => $pathTracking,
		]);
		$qrTracking = 'data:image/png;base64,' . base64_encode(file_get_contents($pathTracking));
		unlink($pathTracking);

		// Satu set piece per koli; dipakai di RESI 1 dan RESI 2.
		$total_koli = max(1, (int) $resi['koli']);
		$labels     = [];

		for ($i = 1; $i <= $total_koli; $i++) {
			$piece_id     = $no_resi . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
			$pathInternal = $tempDir . DIRECTORY_SEPARATOR . 'qr_int_' . $uniq . '_' . $i . '.png';

			$this->ciqrcode->generate([
				'data'     => $piece_id,
				'level'    => 'M',
				'size'     => 5,
				'savename' => $pathInternal,
			]);

			$labels[] = [
				'piece_id'    => $piece_id,
				'koli_ke'     => $i,
				'qr_internal' => 'data:image/png;base64,' . base64_encode(file_get_contents($pathInternal)),
				'barcode'     => $this->cibarcode->generate($piece_id),
			];
			unlink($pathInternal);
		}

		// Nama kota asal agen dari cities.code (code tidak UNIQUE -> ambil yang aktif pertama).
		$agent_origin_code = trim((string) ($resi['agent_origin'] ?? ''));
		$agent_origin_name = $agent_origin_code;

		if ($agent_origin_code !== '') {
			$city = $this->db
				->select('name')
				->where('code', $agent_origin_code)
				->where('is_active', 1)
				->limit(1)
				->get('cities')
				->row();

			if ($city && trim((string) $city->name) !== '') {
				$agent_origin_name = $city->name;
			}
		}

		// $this->load->view('app/pages/agent_kecamatan/print', [
		// 	'resi'              => $resi,
		// 	'labels'            => $labels,
		// 	'total_koli'        => $total_koli,
		// 	'qr_tracking'       => $qrTracking,
		// 	'logo_base64'       => $logoBase64,
		// 	'agent_origin_code' => $agent_origin_code,
		// 	'agent_origin_name' => $agent_origin_name !== '' ? $agent_origin_name : '-',
		// 	'hub_name'          => 'Jakarta',
		// 	'hub_address'       => 'Pondok Labu',
		// ]);

		$html = $this->load->view('app/pages/agent_kecamatan/print', [
			'resi'              => $resi,
			'labels'            => $labels,
			'total_koli'        => $total_koli,
			'qr_tracking'       => $qrTracking,
			'logo_base64'       => $logoBase64,
			'agent_origin_code' => $agent_origin_code,
			'agent_origin_name' => $agent_origin_name !== '' ? $agent_origin_name : '-',
			'hub_name'          => 'Jakarta',
			'hub_address'       => 'Pondok Labu',
		], TRUE);

		$this->pdfgenerator->generate($html, 'Resi-' . $no_resi, 'A6', 'portrait');
	}


	/**
	 * ============================================================
	 * MANIFEST
	 * ============================================================
	 *
	 * Kita kerjakan setelah flow transaksi + pembayaran stabil.
	 */
	public function manifest()
	{
		$agent_id = $this->agent_id;

		$data = [
			'title'    => 'Daftar Barang yang Diserahkan',
			'agent_id' => $agent_id,
		];

		$this->render(
			'app/pages/agent_kecamatan/manifest',
			$data
		);
	}


	public function create_manifest()
	{
		// Akan kita kerjakan setelah index/detail stabil.
	}


	public function add_to_manifest()
	{
		// Akan kita kerjakan setelah index/detail stabil.
	}


	public function handover($id)
	{
		// Akan kita kerjakan setelah manifest stabil.
	}

	public function ajax_cek_harga()
	{
		$this->_check_access();

		$sess = $this->session->userdata('user');

		// Pastikan hanya role Agen Kecamatan
		if (($sess['role_slug'] ?? '') !== 'admin-mitra-kecamatan') {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'status'  => false,
					'message' => 'Akses tidak diizinkan.'
				]));
		}

		$origin         = $this->input->post('origin', TRUE);
		$destination    = $this->input->post('destination', TRUE);
		$service_id     = $this->input->post('service_type_id', TRUE);
		$weight         = (float) $this->input->post('weight');

		if (!$origin || !$destination || !$service_id) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'status'  => false,
					'message' => 'Data rute dan layanan belum lengkap.'
				]));
		}

		/*
	|--------------------------------------------------------------------------
	| Cari pricelist
	|--------------------------------------------------------------------------
	*/
		$pricelist = $this->db
			->where('origin', $origin)
			->where('destination', $destination)
			->where('service_type_id', $service_id)
			->where('category', 'INTERNATIONAL')
			->where('is_active', 1)
			->get('pricelist')
			->row();

		if (!$pricelist) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'status'  => false,
					'message' => 'Tarif Kecamatan tidak ditemukan untuk rute ini.'
				]));
		}

		/*
	|--------------------------------------------------------------------------
	| Default price
	|--------------------------------------------------------------------------
	*/
		$price_per_kg = (float) $pricelist->price_kecamatan;

		$min_weight_kg = (float) $pricelist->min_weight_kg;

		/*
	|--------------------------------------------------------------------------
	| Tiered Pricing
	|--------------------------------------------------------------------------
	*/
		if ((int) $pricelist->is_tiered === 1) {

			$tier = $this->db
				->where('pricelist_id', $pricelist->id)
				->where('min_weight <=', $weight)
				->where('max_weight >=', $weight)
				->get('pricelist_tiers')
				->row();

			if ($tier) {

				$price_per_kg = (float) $tier->price_kecamatan;
			} else {

				/*
			 * Kalau berat melebihi tier terakhir,
			 * gunakan tier dengan max_weight paling besar.
			 */
				$last_tier = $this->db
					->where('pricelist_id', $pricelist->id)
					->order_by('max_weight', 'DESC')
					->limit(1)
					->get('pricelist_tiers')
					->row();

				if ($last_tier) {
					$price_per_kg = (float) $last_tier->price_kecamatan;
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Response
		|--------------------------------------------------------------------------
		*/
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => true,
				'data'   => [
					'price_per_kg'              => $price_per_kg,
					'min_weight_kg'             => $min_weight_kg,
					'is_tiered'                 => (int) $pricelist->is_tiered,
					'category'                  => $pricelist->category,
					'vendor_surcharge_per_kg'   => 0
				]
			]));
	}

	public function handover_courier()
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_response(false, 'Method tidak diizinkan.');
		}

		$ids = (array) $this->input->post('ids');
		if (!$ids && $this->input->post('id')) {
			$ids = [$this->input->post('id')];
		}

		$result = $this->M_Shipment->create_kec_handover(
			(int) $this->agent_id,
			(int) $this->current_user['id'],
			$ids,
			[
				'name'  => trim((string) $this->input->post('forwarder_name', TRUE)) ?: NULL,
				'phone' => trim((string) $this->input->post('forwarder_phone', TRUE)) ?: NULL,
				'hub' => trim((string) $this->input->post('destination_hub', TRUE)) ?: NULL,
				'awb'  => trim((string) $this->input->post('origin_courier_awb', TRUE)),
			]
		);

		if (empty($result['status'])) {
			return $this->_json_response(false, $result['message']);
		}

		return $this->_json_response(true, 'Manifest ' . $result['manifest_number'] . ' berhasil dibuat.', [
			'manifest_id' => $result['manifest_id'],
		]);
	}

	private function _kec_json($status, $message, $extra = [])
	{
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode(array_merge([
				'status'  => (bool) $status,
				'message' => $message,
			], $extra)));
	}

	private function _get_agent_kecamatan($agent)
	{
		if (empty($agent->subdistrict_id)) {
			return NULL;
		}

		$kecamatan_id = $agent->regency_id . $agent->subdistrict_id;

		return $this->db
			->select('id, nama_kecamatan')
			->where('id', $kecamatan_id)
			->get('mt_kecamatan')
			->row();
	}

	public function settlement()
	{
		$this->_require_settler();
		$this->load->model('M_AgentSettlement');

		$this->render('app/pages/agent_kecamatan/settlement', [
			'title'   => 'Setoran ke Smesco',
			'unpaid'  => $this->M_AgentSettlement->get_unpaid_shipments($this->agent_id),
			'summary' => $this->M_AgentSettlement->get_summary($this->agent_id),
			'history' => $this->M_AgentSettlement->get_history($this->agent_id),
		]);
	}

	public function settlement_submit()
	{
		$this->_require_settler();
		if ($this->input->method() !== 'post') {
			show_404();
		}
		$this->load->model('M_AgentSettlement');

		$ids    = (array) $this->input->post('ids');
		$amount = (float) $this->input->post('transfer_amount');
		$date   = (string) $this->input->post('transfer_date', TRUE);

		$d = DateTime::createFromFormat('Y-m-d', $date);
		if (!$ids)                                    $this->_settle_back('Pilih minimal satu resi.');
		if ($amount <= 0)                             $this->_settle_back('Nominal transfer wajib diisi.');
		if (!$d || $d->format('Y-m-d') !== $date)     $this->_settle_back('Tanggal transfer tidak valid.');
		if ($date > date('Y-m-d'))                    $this->_settle_back('Tanggal transfer tidak boleh di masa depan.');

		// Bukti disimpan DI LUAR webroot. Sesuaikan jika struktur cPanel-mu beda.
		$dir = dirname(FCPATH) . '/uploads/settlement_proofs/';
		if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
			$this->_settle_back('Folder bukti tidak bisa dibuat.');
		}

		$this->load->library('upload', [
			'upload_path'   => $dir,
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'max_size'      => 2048,
			'encrypt_name'  => TRUE,
		]);

		if (!$this->upload->do_upload('proof')) {
			$this->_settle_back(strip_tags($this->upload->display_errors('', '')));
		}
		$proof = $this->upload->data('file_name');

		$res = $this->M_AgentSettlement->submit(
			(int) $this->agent_id,
			(int) $this->current_user['id'],
			$ids,
			$amount,
			$date,
			$proof
		);

		if (empty($res['status'])) {
			@unlink($dir . $proof);   // jangan tinggalkan file yatim
			$this->_settle_back($res['message']);
		}

		$this->session->set_flashdata('success', 'Setoran <b>' . html_escape($res['settlement_no']) . '</b> terkirim, menunggu verifikasi finance.');
		redirect('agentkecamatan/settlement');
	}

	private function _settle_back($msg)
	{
		$this->session->set_flashdata('error', $msg);
		redirect('agentkecamatan/settlement');
	}

	private function _can_settle()
	{
		$sess = $this->session->userdata('user');

		
		
		if (($sess['role_slug'] ?? '') !== 'admin-mitra-kecamatan') {
			return FALSE;
		}

		$agent = $this->M_Agent->get_detail($this->agent_id);

		
		if (!$agent || ($agent->is_kecamatan ?? '0') !== '1') {
			return FALSE;
		}

		// echo '<pre>';
		// print_r($agent);
		// echo '</pre>';
		// exit;

		// Flag dibaca dari DB, bukan dari session, supaya pencabutan hak langsung berlaku.
		$u = $this->db->select('confirm_payment')
			->where('id', (int) $this->current_user['id'])
			->where('is_active', 1)
			->where('deleted_at IS NULL', NULL, FALSE)
			->get('users')->row();

		return $u && $u->confirm_payment === '1';
	}

	private function _require_settler()
	{
		// if (!$this->_can_settle()) {
		// 	show_error('Akses tidak diizinkan.', 403);
		// }
	}
}
