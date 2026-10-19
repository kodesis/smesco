<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Shipment extends CI_Model
{
	const KEC_PRICING_ORIGIN = 'JAKARTA';
	const KEC_VOLUME_DIVISOR = 5000;
	const KEC_TESS_MIN       = 100000;

	/**
	 * Fungsi untuk generate Nomor AWB (Resi)
	 * Format: SMC + YYMMDD + 0001 (Reset tiap hari)
	 */
	public function generate_no_resi()
	{
		// 1. Dapatkan format tanggal hari ini: YYMMDD (contoh: 260331)
		$date_prefix = date('ymd');
		$prefix = 'SMC' . $date_prefix; // Output: SMC260331

		// 2. Cari resi terakhir di database yang dibuat hari ini
		$this->db->select('no_resi');
		$this->db->like('no_resi', $prefix, 'after'); // Cek awalan SMC260331...
		$this->db->order_by('no_resi', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get('shipments');

		if ($query->num_rows() > 0) {
			// Kalau sudah ada transaksi hari ini, ambil 4 digit terakhir
			$last_awb = $query->row()->no_resi;
			$last_sequence = intval(substr($last_awb, -4));
			$new_sequence = $last_sequence + 1;
		} else {
			// Kalau belum ada transaksi sama sekali hari ini, mulai dari 1
			$new_sequence = 1;
		}

		// 3. Gabungkan Prefix dengan Urutan yang dipadding 4 digit angka 0
		// Hasil akhir: SMC2603310001
		return $prefix . str_pad($new_sequence, 4, '0', STR_PAD_LEFT);
	}

	/**
	 * Ambil daftar resi sesuai Hak Akses
	 */
	public function get_shipments($agent_id = NULL, $filters = [])
	{
		$this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code');
		$this->db->from('shipments');
		$this->db->join('service_types', 'service_types.id = shipments.service_type_id', 'left');

		// 1. Filter Scope Agen (Security)
		if ($agent_id !== NULL) {
			$this->db->where('shipments.agent_id', $agent_id);
		}

		// 2. Filter Kata Kunci (AWB / Nama Pengirim / Nama Penerima)
		if (!empty($filters['q'])) {
			$q = $filters['q'];
			$this->db->group_start(); // Pakai group_start biar querynya: WHERE ... AND (awb LIKE ... OR name LIKE ...)
			$this->db->like('shipments.no_resi', $q);
			$this->db->or_like('shipments.sender_name', $q);
			$this->db->or_like('shipments.receiver_name', $q);
			$this->db->or_like('shipments.destination', $q);
			$this->db->group_end();
		}

		// 3. Filter Status
		if (!empty($filters['status'])) {
			$this->db->where('shipments.status', $filters['status']);
		}

		// 4. Filter Rentang Tanggal
		if (!empty($filters['start'])) {
			$this->db->where('DATE(shipments.created_at) >=', $filters['start']);
		}
		if (!empty($filters['end'])) {
			$this->db->where('DATE(shipments.created_at) <=', $filters['end']);
		}

		$this->db->order_by('shipments.id', 'DESC');
		return $this->db->get()->result();
	}

	// Fungsi buat ngisi angka-angka di kartu dashboard index
	public function get_stats($agent_id = NULL)
	{
		if ($agent_id !== NULL) $this->db->where('agent_id', $agent_id);

		return $this->db->select("
        COUNT(*) as total_all,
        SUM(CASE WHEN status = 'BOOKED' THEN 1 ELSE 0 END) as total_pending,
        SUM(CASE WHEN status IN ('MANIFESTED','DEPARTED','ARRIVED','RECEIVED_DESTINATION') THEN 1 ELSE 0 END) as total_transit,
        SUM(total_amount) as total_tagihan -- Ini adalah Harga Smesco yang harus mereka bayar
    ")->get('shipments')->row();
	}

	// 1. Menghitung total data (untuk pagination)
	public function count_filtered($agent_id = NULL, $filters = [])
	{
		$this->_apply_filters($agent_id, $filters);
		return $this->db->count_all_results('shipments');
	}

	// 2. Menarik data per halaman (Limit & Offset)
	public function get_paged($limit, $offset, $agent_id = NULL, $filters = [])
	{
		$this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code, users.name as created_by_name, agents.is_kecamatan');
		$this->db->from('shipments');
		$this->db->join('service_types', 'service_types.id = shipments.service_type_id', 'left');
		$this->db->join('users', 'users.id = shipments.created_by', 'left');
		$this->db->join('agents', 'agents.id = shipments.agent_id', 'left');
		
		$this->_apply_filters($agent_id, $filters);

		$this->db->order_by('shipments.id', 'DESC');
		$this->db->limit($limit, $offset);
		return $this->db->get()->result();
	}

	// Fungsi pembantu biar nggak nulis filter dua kali (DRY - Don't Repeat Yourself)
	private function _apply_filters($agent_id, $filters)
	{
		if ($agent_id !== NULL) {
			$this->db->where('shipments.agent_id', $agent_id);
		}

		if (!empty($filters['q'])) {
			$q = $filters['q'];
			$this->db->group_start();
			$this->db->like('shipments.no_resi', $q);
			$this->db->or_like('shipments.sender_name', $q);
			$this->db->or_like('shipments.receiver_name', $q);
			$this->db->or_like('shipments.destination', $q);
			$this->db->group_end();
		}

		if (!empty($filters['status'])) {
			$this->db->where('shipments.status', $filters['status']);
		}

		if (!empty($filters['start'])) {
			$this->db->where('DATE(shipments.created_at) >=', $filters['start']);
		}
		if (!empty($filters['end'])) {
			$this->db->where('DATE(shipments.created_at) <=', $filters['end']);
		}

		// Di dalam method pencarian M_Shipment
		if (!empty($filters['payment_type'])) {
			$this->db->where('payment_type', $filters['payment_type']);
		}
		if (!empty($filters['payment_status'])) {
			$this->db->where('payment_status', $filters['payment_status']);
		}
	}

	// Di M_Shipment.php atau M_Manifest.php
	public function process_manifest($shipment_ids, $data_pesawat)
	{
		$this->db->trans_start();

		// Data yang diinput Admin Kribo
		$update_data = [
			'status'           => 'MANIFESTED',
			'smu_number'       => $data_pesawat['smu_number'],
			'flight_number'    => $data_pesawat['flight_number'],
			'departure_date'   => $data_pesawat['departure_date'],
			'origin_warehouse' => $data_pesawat['origin_warehouse'],
		];

		// Update semua resi yang dicentang
		$this->db->where_in('id', $shipment_ids);
		$this->db->update('shipments', $update_data);

		// Catat ke Log buat tiap resi
		foreach ($shipment_ids as $id) {
			$this->insert_log($id, 'MANIFESTED', "Barang masuk Manifest SMU: " . $data_pesawat['smu_number']);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function bulk_manifest($ids, $data)
	{
		$this->db->trans_start();

		// 1. Update data resi
		$this->db->where_in('id', $ids);
		$this->db->update('shipments', [
			'status'           => 'MANIFESTED',
			'smu_number'       => $data['smu_number'],
			'flight_number'    => $data['flight_number'],
			'origin_warehouse' => $data['origin_warehouse'],
			'departure_date'   => $data['departure_date']
		]);

		// 2. Insert Log buat masing-masing resi
		foreach ($ids as $id) {
			$this->db->insert('shipment_tracking', [
				'shipment_id' => $id,
				'status'      => 'MANIFESTED',
				'note'        => "Manifested: SMU {$data['smu_number']} via {$data['flight_number']}",
				'location'    => $data['origin_warehouse'],
				'created_by'  => $this->session->userdata('user')['id']
			]);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function getResi($no_resi)
	{
		return $this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code')
			->from('shipments')
			->join('service_types', 'service_types.id = shipments.service_type_id', 'left')
			->where('shipments.no_resi', $no_resi)
			->get()
			->row_array();
	}

	public function update_status($id, $new_status, $note = '', $location = '')
	{
		// Ambil ID user dari session buat pencatatan log
		$sess = $this->session->userdata('user');
		$user_id = $sess['id'] ?? NULL;

		$this->db->trans_start();

		// 1. Update status di tabel shipments
		$this->db->where('id', $id);
		$this->db->update('shipments', [
			'status'     => $new_status,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// 2. Insert riwayat ke tabel shipment_tracking
		// Catatan: Pastikan nama tabelnya 'shipment_tracking' sesuai dengan yang lu pakai di function create()
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $id,
			'status'      => $new_status,
			'note'        => $note,
			'location'    => $location,
			'created_by'  => $user_id,
			'created_at'  => date('Y-m-d H:i:s')
		]);

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	public function get_tracking_history($shipment_id)
	{
		return $this->db->where('shipment_id', $shipment_id)
			->order_by('id', 'DESC')
			->get('shipment_tracking')
			->result_array();
	}

	// public function get_tracking_public($shipment_id)
	// {
	// 	// Ambil data tracking sekaligus category dari tabel shipments
	// 	$this->db->select('st.*, s.category as shipment_category');
	// 	$this->db->from('shipment_tracking st');
	// 	$this->db->join('shipments s', 's.id = st.shipment_id');
	// 	$this->db->where('st.shipment_id', $shipment_id);
	// 	$this->db->where_not_in('st.status', ['PIECE_RECEIVED', 'PIECE_RECEIVED_DESTINATION']);
	// 	$this->db->order_by('st.created_at', 'DESC');

	// 	$query = $this->db->get();
	// 	$results = $query->result_array();

	// 	foreach ($results as &$row) {
	// 		// Cek jika kategori shipment adalah INTERNATIONAL
	// 		if (strtoupper($row['shipment_category']) === 'INTERNATIONAL') {

	// 			switch ($row['status']) {
	// 				case 'BOOKED':
	// 					$row['note'] = 'Your shipment has been booked / registered.';
	// 					break;
	// 				case 'PICKED_UP':
	// 					$row['note'] = 'Your shipment has been picked up by courier.';
	// 					break;
	// 				case 'RECEIVED_ORIGIN':
	// 					$row['note'] = 'On origin custom clearance process';
	// 					break;
	// 				case 'MANIFESTED':
	// 					$row['note'] = 'Your shipment has departed from origin airport';
	// 					break;
	// 				case 'ARRIVED':
	// 					$row['note'] = 'Your shipment has arrived at destination airport';
	// 					break;
	// 				case 'RECEIVED_DESTINATION':
	// 					// Bisa digunakan untuk menandakan proses masuk bea cukai tujuan
	// 					$row['note'] = 'On destination custom clearance process';
	// 					break;
	// 				case 'DELIVERED':
	// 					$row['note'] = 'Shipment delivered successfully.';
	// 					break;
	// 				default:
	// 					// Jika status internal lainnya, biarkan menggunakan note bawaan atau custom default
	// 					if (empty($row['note'])) {
	// 						$row['note'] = 'In transit / processing.';
	// 					}
	// 					break;
	// 			}
	// 		} else {
	// 			// ── LOGIC MASKING DOMESTIC (Bawaan Lama) ──
	// 			if ($row['status'] === 'MANIFESTED') {
	// 				$row['note'] = 'Paket telah masuk ke dalam daftar muatan kargo udara.';
	// 			}
	// 			if ($row['status'] === 'DEPARTED') {
	// 				$row['note'] = 'Pesawat telah berangkat menuju bandara tujuan.';
	// 			}
	// 			if ($row['status'] === 'ARRIVED') {
	// 				$row['note'] = 'Pesawat telah tiba di bandara tujuan.';
	// 			}
	// 		}

	// 		// Hapus temp variabel category agar response API publik tetap bersih
	// 		unset($row['shipment_category']);
	// 	}

	// 	return $results;
	// }

	public function get_tracking_public($shipment_id)
	{
		$this->db->select('st.*, s.category as shipment_category, s.vendor as shipment_vendor');
		$this->db->from('shipment_tracking st');
		$this->db->join('shipments s', 's.id = st.shipment_id');
		$this->db->where('st.shipment_id', $shipment_id);
		$this->db->where_not_in('st.status', ['PIECE_RECEIVED', 'PIECE_RECEIVED_DESTINATION']);
		$this->db->order_by('st.created_at', 'DESC');

		$query   = $this->db->get();
		$results = $query->result_array();

		foreach ($results as &$row) {
			$is_international = strtoupper($row['shipment_category']) === 'INTERNATIONAL';
			$has_vendor       = !empty($row['shipment_vendor']);

			if ($is_international) {
				if ($has_vendor) {
					// Note dari vendor sudah tersimpan saat sync — tidak di-overwrite
					// Hanya fallback kalau note kosong
					if (empty($row['note'])) {
						$row['note'] = 'In transit / processing.';
					}
				} else {
					// Manual input — pakai teks generik per status
					$generic_notes = [
						'BOOKED'               => 'Your shipment has been booked / registered.',
						'PICKED_UP'            => 'Your shipment has been picked up by courier.',
						'RECEIVED_ORIGIN'      => 'On origin custom clearance process.',
						'MANIFESTED'           => 'Your shipment has departed from origin airport.',
						'ARRIVED'              => 'Your shipment has arrived at destination airport.',
						'RECEIVED_DESTINATION' => 'On destination custom clearance process.',
						'IN_TRANSIT'           => 'In transit / processing.',
						'DELIVERED'            => 'Shipment delivered successfully.',
					];

					$row['note'] = $generic_notes[$row['status']] ?? (empty($row['note']) ? 'In transit / processing.' : $row['note']);
				}
			} else {
				// Domestic — logic lama
				$domestic_overrides = [
					'MANIFESTED' => 'Paket telah masuk ke dalam daftar muatan kargo udara.',
					'DEPARTED'   => 'Pesawat telah berangkat menuju bandara tujuan.',
					'ARRIVED'    => 'Pesawat telah tiba di bandara tujuan.',
				];

				if (isset($domestic_overrides[$row['status']])) {
					$row['note'] = $domestic_overrides[$row['status']];
				}
			}

			unset($row['shipment_category'], $row['shipment_vendor']);
		}

		return $results;
	}

	public function get_by_id($id)
	{
		return $this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code, master_commodities.name as commodity_name')
			->from('shipments')
			->join('service_types', 'service_types.id = shipments.service_type_id', 'left')
			->join('master_commodities', 'master_commodities.id = shipments.commodity_id', 'left')
			->where('shipments.id', $id)
			->get()
			->row_array();
	}

	public function get_by_no_resi($no_resi)
	{
		return $this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code, master_commodities.name as commodity_name')
			->from('shipments')
			->join('service_types', 'service_types.id = shipments.service_type_id', 'left')
			->join('master_commodities', 'master_commodities.id = shipments.commodity_id', 'left')
			->where('shipments.no_resi', $no_resi)
			->get()
			->row();
	}

	public function get_dimensions($shipment_id)
	{
		return $this->db->get_where('shipment_dimensions', ['shipment_id' => $shipment_id])->result_array();
	}

	public function insert_tracking_log($shipment_id, $status, $note = '', $location = '')
	{
		$sess = $this->session->userdata('user');
		$user_id = $sess['id'] ?? NULL;

		$data = [
			'shipment_id' => $shipment_id,
			'status'      => $status,
			'note'        => $note,
			'location'    => $location,
			'created_by'  => $user_id,
			'created_at'  => date('Y-m-d H:i:s')
		];

		return $this->db->insert('shipment_tracking', $data);
	}

	// Tambahkan di M_Shipment.php

	public function get_manifest_list($filters = [])
	{
		$statuses = isset($filters['statuses']) ? $filters['statuses'] : ['MANIFESTED', 'DEPARTED'];

		$this->db->select("
				smu_number,
				flight_number,
				departure_date,
				origin,
				origin_warehouse,
				destination,
				status,
				COUNT(id)               as total_resi,
				SUM(koli)               as total_koli,
				SUM(chargeable_weight)  as total_weight
			")
			->from('shipments')
			->where_in('status', $statuses)
			->group_by('smu_number, flight_number, departure_date, origin, origin_warehouse, destination, status')
			->order_by('departure_date', 'ASC');

		// Filter status dari UI
		if (!empty($filters['status'])) {
			$this->db->where('status', $filters['status']);
		}

		return $this->db->get()->result();
	}

	// public function get_master_awb_list($filters = [])
	// {
	// 	// Status default jika tidak di-filter dari UI
	// 	$statuses = !empty($filters['status']) ? [$filters['status']] : ['DRAFT', 'MANIFESTED', 'DEPARTED'];

	// 	$this->db->select("
	//          ma.id as awb_id,
	//          ma.awb_number,
	//          ma.flight_number,
	//          ma.departure_date,
	//          ma.origin,
	//          ma.destination,
	//          ma.status,
	//          al.name as airline_name,
	//          COUNT(DISTINCT ak.id) as total_karung,
	//          COUNT(s.id) as total_resi,
	//          SUM(s.koli) as total_koli,
	//          SUM(s.chargeable_weight) as total_weight
	//      ");
	// 	$this->db->from('master_awb ma');
	// 	$this->db->join('airlines al', 'al.id = ma.airline_id', 'left');
	// 	// Relasi ke tabel koli (karung konsolidasi)
	// 	$this->db->join('awb_koli ak', 'ak.awb_id = ma.id', 'left');
	// 	// Relasi ke shipments melalui awb_koli_id yang baru
	// 	$this->db->join('shipments s', 's.awb_koli_id = ak.id', 'left');

	// 	$this->db->where_in('ma.status', $statuses);

	// 	// Jika user yang login terikat ke agen/hub tertentu
	// 	if (!empty($filters['agent_id'])) {
	// 		// Asumsi AWB dicatat oleh agen pembuat atau menyaring rute asal bandara agen
	// 		$this->db->where('ma.created_by_agent', $filters['agent_id']);
	// 	}

	// 	$this->db->group_by('ma.id, ma.awb_number, ma.flight_number, ma.departure_date, ma.origin, ma.destination, ma.status, al.name');
	// 	$this->db->order_by('ma.departure_date', 'ASC');

	// 	return $this->db->get()->result();
	// }

	public function get_master_awb_list($filters = [])
	{
		$statuses = !empty($filters['status']) ? [$filters['status']] : ['ARRIVED'];

		$this->db->select("
        ma.id as awb_id,
        ma.awb_number as smu_number,
        ma.flight_number,
        ma.departure_date,
        ma.origin,
        ma.destination,
        ma.status,
        al.name as airline_name,
        COUNT(DISTINCT ak.id) as total_karung,
        COUNT(DISTINCT CASE WHEN ak.scanned_inbound_at IS NOT NULL THEN ak.id END) as received_karung,
        COUNT(DISTINCT s.id) as total_resi,
        COUNT(DISTINCT sd.barcode_koli) as total_koli,
        SUM(ak.actual_weight) as total_weight
    ");
		$this->db->from('master_awb ma');
		$this->db->join('airlines al', 'al.id = ma.airline_id', 'left');
		$this->db->join('awb_koli ak', 'ak.awb_id = ma.id', 'left');
		$this->db->join('shipment_dimensions sd', 'sd.awb_koli_id = ak.id', 'left');
		$this->db->join('shipments s', 's.id = sd.shipment_id', 'left');

		$this->db->where_in('ma.status', $statuses);

		if (!empty($filters['destination'])) {
			$this->db->where('ma.destination', $filters['destination']);
		}

		if (!empty($filters['agent_id'])) {
			$this->db->where('ma.created_by', $filters['agent_id']);
		}

		$this->db->group_by('ma.id, ma.awb_number, ma.flight_number, ma.departure_date, ma.origin, ma.destination, ma.status, al.name');
		$this->db->order_by('ma.departure_date', 'DESC');

		return $this->db->get()->result();
	}

	public function depart_by_smu($smu_number)
	{
		$shipments = $this->db->get_where('shipments', ['smu_number' => $smu_number])->result();
		if (empty($shipments)) return false;

		$this->db->trans_start();

		// 1. Update Status Master
		$this->db->where('smu_number', $smu_number);
		$this->db->update('shipments', ['status' => 'DEPARTED', 'updated_at' => date('Y-m-d H:i:s')]);

		// 2. Insert Log per Resi
		foreach ($shipments as $s) {
			$this->insert_tracking_log(
				$s->id,
				'DEPARTED',
				"Pesawat {$s->flight_number} telah berangkat membawa SMU {$smu_number}.",
				'Warehouse CGK (Airside)'
			);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function arrive_by_smu($smu_number)
	{
		// 1. Ambil data resi untuk mendapatkan info tujuan & flight
		$shipments = $this->db->get_where('shipments', ['smu_number' => $smu_number])->result();
		if (empty($shipments)) return false;

		$this->db->trans_start();

		// 2. Update status Master ke ARRIVED
		$this->db->where('smu_number', $smu_number);
		$this->db->update('shipments', [
			'status' => 'ARRIVED',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// 3. Catat Log Tracking
		foreach ($shipments as $s) {
			$this->insert_tracking_log(
				$s->id,
				'ARRIVED',
				"Pesawat {$s->flight_number} telah mendarat. Paket berada di Gudang Bandara tujuan.",
				"Bandara {$s->destination}" // Lokasi otomatis ambil dari kolom destination
			);
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_inbound_pending($city_name)
	{
		return $this->db->select("
        ma.id,
        ma.awb_number,
        ma.origin as origin_agent,
        COUNT(ak.id) as koli,
        SUM(CASE WHEN ak.scanned_inbound_at IS NOT NULL THEN 1 ELSE 0 END) as received_qty
    ", FALSE)
			->from('master_awb ma')
			->join('awb_koli ak', 'ak.awb_id = ma.id', 'left')
			->where('ma.destination', $city_name)
			->where('ma.status', 'ARRIVED')
			->group_by('ma.id')
			->having('received_qty < koli')
			->order_by('ma.created_at', 'ASC')
			->get()->result();
	}

	public function get_superadmin_stats()
	{
		return $this->db->select("
        COUNT(*) as total_shipment,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as shipment_today,
        SUM(CASE WHEN status NOT IN ('DELIVERED','CANCELLED') THEN 1 ELSE 0 END) as shipment_active,
        
        -- Total Omzet (Harga Jual)
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE()) 
            THEN total_amount ELSE 0 END) as omzet_bulan_ini,
            
        -- TOTAL PROFIT (Selisih Harga Smesco - Kribo)
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE()) 
            THEN margin_amount ELSE 0 END) as profit_smesco_bulan_ini
		")->get('shipments')->row();
	}

	public function get_kribo_stats()
	{
		return $this->db->select("
        COUNT(*) as total_shipment,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as shipment_today,

        SUM(CASE WHEN status = 'BOOKED' THEN 1 ELSE 0 END) as pending_payment,

        SUM(CASE WHEN status IN ('READY_TO_PICKUP','PICKED_UP') THEN 1 ELSE 0 END) as proses_pickup,

        SUM(CASE WHEN status = 'RECEIVED_ORIGIN' THEN 1 ELSE 0 END) as ready_manifest,

        SUM(CASE WHEN status IN ('MANIFESTED','DEPARTED','ARRIVED','RECEIVED_DESTINATION') THEN 1 ELSE 0 END) as in_transit,

        SUM(CASE WHEN status = 'DELIVERED' THEN 1 ELSE 0 END) as delivered,

        SUM(CASE WHEN status = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled
    ")->get('shipments')->row();
	}

	public function get_recent_shipments($limit = 8)
	{
		return $this->db
			->select('shipments.*, agents.name as agent_name, service_types.code as service_code')
			->from('shipments')
			->join('agents', 'agents.id = shipments.agent_id', 'left')
			->join('service_types', 'service_types.id = shipments.service_type_id', 'left')
			->order_by('shipments.id', 'DESC')
			->limit($limit)
			->get()->result();
	}

	public function get_agent_stats($agent_id, $city_name = NULL)
	{
		$city_name_escaped = $this->db->escape($city_name);

		return $this->db->select("
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as shipment_today,
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as shipment_bulan_ini,
        SUM(CASE WHEN status NOT IN ('DELIVERED','CANCELLED') THEN 1 ELSE 0 END) as shipment_aktif,
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE()) THEN total_amount ELSE 0 END) as omzet_bulan_ini,
        SUM(CASE WHEN status IN ('ARRIVED','DEPARTED') 
            AND destination = {$city_name_escaped} THEN 1 ELSE 0 END) as inbound_pending
    ")->where('agent_id', $agent_id)->get('shipments')->row();
	}

	public function sync_vendor_tracking($shipment_id)
	{
		$shipment = $this->db->select('id, vendor, vendor_connote, status')
			->from('shipments')
			->where('id', $shipment_id)
			->where_in('vendor', ['TLX', 'CHOIR EXPRESS'])
			->get()->row_array();

		if (!$shipment || empty($shipment['vendor_connote'])) return false;

		// 🔴 REMOVED: 
		// if ($shipment['status'] === 'DELIVERED') return false;
		// Baris di atas dihapus agar jika data tracking dihapus manual/di-reset, sync tetap bisa berjalan.

		// Resolve vendor class
		$vendor_map = [
			'TLX'   => 'Tlx_tracking',
			'CHOIR EXPRESS' => 'Choir_tracking',
		];

		$class_name = $vendor_map[$shipment['vendor']] ?? null;
		if (!$class_name) return false;

		require_once APPPATH . 'libraries/tracking/Tracking_contract.php';
		require_once APPPATH . 'libraries/tracking/' . $class_name . '.php';

		$tracker = new $class_name();
		$items   = $tracker->fetch($shipment['vendor_connote']);

		if (empty($items)) return false;

		// Ambil existing tracking untuk dedup presisi (kombinasi created_at + note)
		$existing = $this->db->select('created_at, note')
			->from('shipment_tracking')
			->where('shipment_id', $shipment_id)
			->get()->result_array();

		$existing_signatures = array_map(function ($row) {
			return $row['created_at'] . '|' . trim($row['note']);
		}, $existing);

		// Urutkan $items ASC berdasarkan created_at agar data ter-insert dari terlama ke terbaru
		usort($items, function ($a, $b) {
			return strtotime($a['created_at']) <=> strtotime($b['created_at']);
		});

		$inserted_count = 0;

		foreach ($items as $item) {
			$signature = $item['created_at'] . '|' . trim($item['note']);

			if (in_array($signature, $existing_signatures)) continue;

			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => $item['status'],
				'note'        => $item['note'],
				'location'    => $item['location'],
				'created_by'  => 0,
				'created_at'  => $item['created_at'],
			]);
			$existing_signatures[] = $signature;

			$inserted_count++;
		}

		// ── FIX UPDATE STATUS UTAMA: Selalu ambil status dengan timestamp paling baru dari array ──
		$latest_item = end($items);

		if ($latest_item && !empty($latest_item['status'])) {
			// Update shipments status jika ada data baru di-insert ATAU status shipments saat ini beda dengan status terbaru
			if ($inserted_count > 0 || $shipment['status'] !== $latest_item['status']) {
				$this->db->where('id', $shipment_id)
					->update('shipments', [
						'status'     => $latest_item['status'],
						'updated_at' => date('Y-m-d H:i:s'),
					]);
			}
		}

		return true;
	}

	/**
	 * Konfirmasi pembayaran Duitku via callback. Idempotent — kalau udah PAID,
	 * gak nulis tracking log lagi (Duitku retry callback sampai 5x).
	 * PENTING: created_by pakai SYSTEM_USER_ID punya konstanta, karena ini
	 * dipanggil server Duitku, bukan user yang login — kolom shipment_tracking.created_by
	 * NOT NULL jadi wajib diisi ID user "system" yang valid.
	 */
	public function confirmDuitkuPayment($no_resi, $reference)
	{
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();
		if (!$shipment) {
			return ['outcome' => 'not_found'];
		}

		if ($shipment->payment_status === 'PAID') {
			return ['outcome' => 'already_processed', 'shipment_id' => $shipment->id];
		}

		$this->db->trans_start();

		$this->db->where('id', $shipment->id)->update('shipments', [
			'payment_status' => 'PAID',
			'updated_at'      => date('Y-m-d H:i:s'),
		]);

		// Status operasional (BOOKED, MANIFESTED, dst) TIDAK diubah di sini —
		// itu tetap ngikutin flow fisik shipment yang udah ada.
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => $shipment->status,
			'location'    => $shipment->origin,
			'note'        => "Pembayaran Duitku dikonfirmasi. Ref: {$reference}",
			'created_by'  => '0', // TODO: ganti dari konstanta dummy, isi ID user system yang beneran
		]);

		$this->db->trans_complete();

		return [
			'outcome'     => $this->db->trans_status() ? 'paid' : 'error',
			'shipment_id' => $shipment->id,
		];
	}

	/**
	 * Tandai pembayaran gagal/expired. Idempotent — sekali FAILED atau udah PAID,
	 * gak diproses ulang (misal retry callback Duitku setelah manual override admin).
	 */
	public function markDuitkuFailed($no_resi, $reference)
	{
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();
		if (!$shipment) {
			return ['outcome' => 'not_found'];
		}

		if (in_array($shipment->payment_status, ['PAID', 'FAILED'], true)) {
			return ['outcome' => 'already_processed', 'shipment_id' => $shipment->id];
		}

		$this->db->where('id', $shipment->id)->update('shipments', [
			'payment_status' => 'FAILED',
			'updated_at'      => date('Y-m-d H:i:s'),
		]);

		return ['outcome' => 'failed', 'shipment_id' => $shipment->id];
	}

	public function get_agent_kecamatan_stats($agent_id)
	{
		$start = date('Y-m-d 00:00:00');
		$end   = date('Y-m-d 00:00:00', strtotime('+1 day'));

		$row = $this->db->query("
		SELECT
		  COUNT(*) AS total,
		  SUM(created_at >= ? AND created_at < ?) AS today,
		  SUM(payment_status = 'PAID') AS paid,
		  SUM((payment_status IS NULL OR payment_status <> 'PAID') AND status <> 'CANCELLED') AS unpaid,
		  SUM(status = 'READY_FOR_HANDOVER')      AS ready_for_handover,
		  SUM(status = 'READY_FOR_FORWARDING')    AS ready_for_forwarding,
		  SUM(status = 'IN_TRANSIT_TO_JAKARTA')   AS in_transit_jakarta,
		  SUM(status = 'RECEIVED_JAKARTA')        AS received_jakarta,
		  SUM(status = 'HANDOVER_TO_COURIER')     AS handover_to_courier
		FROM shipments
		WHERE agent_id = ? AND category = 'INTERNATIONAL'
	", [$start, $end, (int) $agent_id])->row();

		$keys = [
			'total',
			'today',
			'paid',
			'unpaid',
			'ready_for_handover',
			'ready_for_forwarding',
			'in_transit_jakarta',
			'received_jakarta',
			'handover_to_courier'
		];

		$out = [];
		foreach ($keys as $k) {
			$out[$k] = (int) ($row->$k ?? 0);
		}
		return $out;
	}

	public function get_recent_agent_kecamatan_shipments($agent_id, $limit = 5)
	{
		return $this->db
			->select('
				s.id,
				s.no_resi,
				s.sender_name,
				s.receiver_name,
				s.destination,
				s.chargeable_weight,
				s.total_amount,
				s.payment_status,
				s.status,
				s.created_at
			')
			->from('shipments s')
			->where('s.agent_id', $agent_id)
			->where('s.category', 'INTERNATIONAL')
			->order_by('s.created_at', 'DESC')
			->limit($limit)
			->get()
			->result();
	}

	/**
	 * Create shipment international khusus Agen Kecamatan.
	 *
	 * Perbedaan dengan save_intl():
	 * - Harga menggunakan price_kecamatan
	 * - Tidak menggunakan payment gateway
	 * - Tidak ada payment proof upload
	 * - Shipment dibuat BOOKED / UNPAID
	 * - Pembayaran dikonfirmasi melalui confirm_payment()
	 *
	 * @param array $data
	 * @return array
	 */
	/*
	 * Taruh di dalam class M_Shipment (bagian atas class):
	 *
	 * const KEC_VOLUME_DIVISOR = 6000;     // HARUS sama dengan pembagi_volume di view
	 * const KEC_TESS_MIN       = 100000;   // minimal TESS (Rp)
	 * const KEC_PHOTO_MAX      = 2097152;  // 2MB
	 */

	/**
	 * Buat shipment Agen Kecamatan.
	 * Semua harga dihitung ulang di sini; nilai dari browser tidak dipercaya.
	 */
	public function create_shipment_kecamatan($data = [])
	{
		$agent_id    = (int) ($data['agent_id'] ?? 0);
		$user_id     = (int) ($data['user_id'] ?? 0);
		$origin      = trim($data['origin'] ?? '');
		$destination = trim($data['destination'] ?? '');
		$agent_origin = trim($data['agent_origin'] ?? '');
		$service_id  = (int) ($data['service_id'] ?? 0);
		$post        = is_array($data['post'] ?? NULL) ? $data['post'] : [];
		$files       = is_array($data['files'] ?? NULL) ? $data['files'] : [];

		/* ---------------------------------------------------------
		 * 1. VALIDASI
		 * --------------------------------------------------------- */
		if (!$agent_id || !$user_id) {
			return $this->_kec_fail('Data agen atau user tidak valid.');
		}

		if ($origin === '' || $destination === '' || !$service_id) {
			return $this->_kec_fail('Asal, tujuan, dan layanan wajib diisi.');
		}

		$missing = $this->_kec_validate_required($post);
		if ($missing) {
			return $this->_kec_fail($missing);
		}

		if (empty($post['is_lartas_agreed'])) {
			return $this->_kec_fail('Verifikasi barang terlarang (LARTAS) wajib dikonfirmasi.');
		}

		/*
		 * Kecamatan hanya CASH / TRANSFER.
		 * Status tetap UNPAID sampai pembayaran dikonfirmasi.
		 */
		$payment_type = strtoupper(trim($post['payment_type'] ?? ''));
		if (!in_array($payment_type, ['CASH', 'TRANSFER'], TRUE)) {
			return $this->_kec_fail('Metode pembayaran tidak valid.');
		}

		$actual_weight = $this->_kec_parse_number($post['actual_weight'] ?? 0);
		if ($actual_weight <= 0) {
			return $this->_kec_fail('Berat aktual wajib lebih dari 0.');
		}

		$dims = $this->_kec_parse_koli($post);
		if (!$dims['ok']) {
			return $this->_kec_fail($dims['message']);
		}

		$koli         = $dims['items'];              // satu elemen = satu koli
		$total_koli   = max(1, count($koli));        // tanpa dimensi tetap 1 koli
		$volume_weight = $dims['volume'];

		$photo = $this->_kec_validate_photo($files);
		if (!$photo['ok']) {
			return $this->_kec_fail($photo['message']);
		}

		/* ---------------------------------------------------------
		 * 2. PRICELIST & HARGA
		 * --------------------------------------------------------- */
		$pricelist = $this->db
			->where('origin', $origin)
			->where('destination', $destination)
			->where('service_type_id', $service_id)
			->where('category', 'INTERNATIONAL')
			->where('is_active', 1)
			->get('pricelist')
			->row();

		if (!$pricelist) {
			return $this->_kec_fail('Tarif internasional untuk tujuan tersebut belum tersedia.');
		}

		// Urutan: max(aktual, volume) -> minimum tarif -> bulatkan ke atas.
		$chargeable = max($actual_weight, $volume_weight);
		$min_weight = (float) ($pricelist->min_weight_kg ?? 0);

		if ($min_weight > 0 && $chargeable < $min_weight) {
			$chargeable = $min_weight;
		}

		$chargeable = ceil($chargeable);

		$price = $this->_kec_resolve_price($pricelist, $chargeable);
		if (!$price) {
			return $this->_kec_fail('Harga Kecamatan untuk tarif tersebut belum tersedia.');
		}

		$shipping_total  = $chargeable * $price['price_kecamatan'];
		$shipping_margin_smesco = ($price['price_smesco'] - $price['price_kribo']) * $chargeable; // tanpa TESS & addon
		$shipping_margin_kecamatan = ($price['price_kecamatan'] - $price['price_smesco']) * $chargeable; // tanpa TESS & addon

		$tagihan_kecamatan = $shipping_total - $shipping_margin_kecamatan;

		$addon_codes = is_array($post['addons'] ?? NULL) ? $post['addons'] : [];
		$addons      = $this->_kec_calc_addons($addon_codes, $koli, $total_koli);
		$tess_fee    = $this->_kec_calc_tess($pricelist, $chargeable);

		$total_amount = $shipping_total + $addons['total'] + $tess_fee;

		/* ---------------------------------------------------------
		 * 3. DATA PENGIRIM & PENERIMA
		 * --------------------------------------------------------- */
		$val = function ($key) use ($post) {
			$v = isset($post[$key]) ? trim((string) $post[$key]) : '';
			return $v === '' ? NULL : $v;
		};

		$prov_id = $val('sender_provinsi');
		$kota_id = $val('sender_kota');
		$kec_id  = $val('sender_kecamatan');
		$kel_id  = $val('sender_kelurahan');

		$prov_name = $this->_kec_region_name('mt_provinsi', $prov_id, 'nama_provinsi');
		$kota_name = $this->_kec_region_name('mt_kota', $kota_id, 'nama_kota');
		$kec_name  = $this->_kec_region_name('mt_kecamatan', $kec_id, 'nama_kecamatan');
		$kel_name  = $this->_kec_region_name('mt_kelurahan', $kel_id, 'nama_kelurahan');

		$sender_address = implode(', ', array_filter([
			$val('sender_address_detail'),
			$kel_name,
			$kec_name ? 'Kec. ' . $kec_name : '',
			$kota_name ? 'Kab/Kota ' . $kota_name : '',
			$prov_name,
		]));

		$receiver_address = implode(', ', array_filter([
			$val('receiver_address_detail'),
			$val('receiver_city'),
			$val('receiver_zipcode'),
			$destination,
		]));

		$is_valuable = !empty($post['is_valuable']) ? 1 : 0;
		$goods_value = $is_valuable ? $this->_kec_parse_number($post['goods_value'] ?? 0) : 0;
		$now         = date('Y-m-d H:i:s');

		/* ---------------------------------------------------------
		 * 4. NOMOR RESI & FOTO
		 * Dibuat setelah semua validasi lolos supaya nomor resi
		 * tidak terbuang kalau validasi gagal.
		 * --------------------------------------------------------- */
		$no_resi = $this->generate_no_resi();
		if (!$no_resi) {
			return $this->_kec_fail('Gagal membuat nomor resi.');
		}

		$photo_name = $this->_kec_store_photo($files['shipment_photo'], $no_resi, $photo['ext']);
		if (!$photo_name) {
			return $this->_kec_fail('Gagal mengupload foto kargo.');
		}

		/* ---------------------------------------------------------
		 * 5. SIMPAN (TRANSAKSI)
		 * --------------------------------------------------------- */
		$data_insert = [
			'no_resi'                 => $no_resi,
			'agent_id'                => $agent_id,
			'category'                => 'INTERNATIONAL',
			'origin'                  => $origin,
			'destination'             => $destination,
			'agent_origin' => $agent_origin !== '' ? $agent_origin : NULL,
			'service_type_id'         => $service_id,

			'sender_name'             => $val('sender_name'),
			'sender_phone'            => $val('sender_phone'),
			'sender_nik'              => $val('sender_nik'),
			'sender_provinsi'         => $prov_name,
			'sender_kota'             => $kota_name,
			'sender_kecamatan'        => $kec_name,
			'sender_kelurahan'        => $kel_name,
			'sender_address_detail'   => $val('sender_address_detail'),
			'sender_address'          => $sender_address,

			'receiver_name'           => $val('receiver_name'),
			'receiver_phone'          => $val('receiver_phone'),
			'receiver_kota'           => $val('receiver_city'),
			'receiver_zipcode'        => $val('receiver_zipcode'),
			'receiver_address_detail' => $val('receiver_address_detail'),
			'receiver_address'        => $receiver_address,

			'commodity_id'            => $val('commodity_id'),
			'commodity_detail'        => $val('commodity_detail'),
			'commodity_detail_en'     => $val('commodity_detail_en'),

			'customs_value_usd'       => $this->_kec_parse_number($post['customs_value_usd'] ?? 0),
			'is_lartas_agreed'        => 1,
			'is_valuable'             => $is_valuable,
			'goods_value'             => $goods_value,

			// Belum PAID; dikonfirmasi lewat confirm_payment().
			'payment_type'            => $payment_type,
			'payment_status'          => 'UNPAID',
			'payment_confirmed_by'    => NULL,
			'payment_confirmed_at'    => NULL,
			'payment_expired_at'      => NULL,

			'koli'                    => $total_koli,
			'actual_weight'           => $actual_weight,
			'volume_weight'           => $volume_weight,
			'chargeable_weight'       => $chargeable,

			'cost_price'              => $price['price_smesco'],
			'sell_price'              => $price['price_kecamatan'],
			'price_per_kg'            => $price['price_kecamatan'],

			// Kecamatan tidak memakai pickup Jakarta.
			'pickup_rate_id'          => NULL,
			'pickup_fee'              => 0,

			'total_addon_fee'         => $addons['total'],
			'tess'                    => $tess_fee,
			'total_amount'            => $total_amount,
			'margin_amount'           => $shipping_margin_smesco,

			'status'                  => 'BOOKED',
			'created_by'              => $user_id,
			'created_at'              => $now,

			'shipment_photo' => 'uploads/shipments/booking/' . $photo_name,

			'status_tagihan_smesco' => 'UNPAID',
			'nominal_tagihan_smesco' => $tagihan_kecamatan,
			'margin_kecamatan' => $shipping_margin_kecamatan,
		];

		// echo '<pre>';
		// print_r($data_insert);
		// echo '</pre>';
		// exit;


		$this->db->trans_start();

		$this->db->insert('shipments', $data_insert);

		$shipment_id = (int) $this->db->insert_id();

		if ($shipment_id) {

			if ($koli) {
				$dim_rows = [];
				foreach ($koli as $i => $k) {
					$dim_rows[] = [
						'shipment_id'  => $shipment_id,
						'barcode_koli' => $no_resi . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
						'qty'          => 1,
						'length'       => $k['length'],
						'width'        => $k['width'],
						'height'       => $k['height'],
					];
				}
				$this->db->insert_batch('shipment_dimensions', $dim_rows);
			}

			if ($addons['rows']) {
				foreach ($addons['rows'] as $i => $row) {
					$addons['rows'][$i]['shipment_id'] = $shipment_id;
				}
				$this->db->insert_batch('shipment_addons', $addons['rows']);
			}

			// Menunggu konfirmasi pembayaran: BOOKED -> READY_FOR_HANDOVER.
			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => 'BOOKED',
				'location' => $agent_origin !== '' ? $agent_origin : $origin,
				'note'        => 'Shipment Kecamatan berhasil dibuat. Menunggu konfirmasi pembayaran.',
				'photo_proof' => $photo_name,
				'created_by'  => $user_id,
				'created_at'  => $now,
			]);

			if ($val('sender_phone')) {
				$this->_kec_upsert_customer([
					'name'           => $val('sender_name'),
					'phone'          => $val('sender_phone'),
					'nik'            => $val('sender_nik'),
					'provinsi_id'    => $prov_id,
					'provinsi_name'  => $prov_name,
					'kota_id'        => $kota_id,
					'kota_name'      => $kota_name,
					'kecamatan_id'   => $kec_id,
					'kecamatan_name' => $kec_name,
					'kelurahan_id'   => $kel_id,
					'kelurahan_name' => $kel_name,
					'address_detail' => $val('sender_address_detail'),
				], $user_id, $now);
			}
		}

		$this->db->trans_complete();

		// Satu jalur gagal: hapus foto supaya tidak jadi file yatim.
		if (!$shipment_id || $this->db->trans_status() === FALSE) {
			@unlink($this->_kec_photo_dir() . $photo_name);
			return $this->_kec_fail('Gagal menyimpan shipment Kecamatan ke database.');
		}

		return [
			'status'         => TRUE,
			'shipment_id'    => $shipment_id,
			'no_resi'        => $no_resi,
			'payment_status' => 'UNPAID',
			'status_code'    => 'BOOKED',
			'total_amount'   => $total_amount,
		];
	}

	/* =============================================================
	 * HELPER (private) - khusus alur Kecamatan
	 * ============================================================= */

	private function _kec_fail($message)
	{
		return ['status' => FALSE, 'message' => $message];
	}

	/**
	 * Form selalu mengirim format Indonesia lewat mask JS:
	 * titik = ribuan, koma = desimal ("1.250,5" = 1250.5).
	 * Harus sama dengan parseIndoNumber() di view.
	 */
	private function _kec_parse_number($value)
	{
		if ($value === NULL || $value === '') {
			return 0.0;
		}

		$value = str_replace('.', '', trim((string) $value));

		return (float) str_replace(',', '.', $value);
	}

	/**
	 * Cek field wajib di server (atribut `required` di HTML mudah dilewati).
	 * Return pesan error, atau NULL kalau lengkap.
	 */
	private function _kec_validate_required(array $post)
	{
		$required = [
			'sender_name'             => 'Nama pengirim',
			'sender_phone'            => 'No. WhatsApp pengirim',
			'sender_provinsi'         => 'Provinsi pengirim',
			'sender_kota'             => 'Kota pengirim',
			'sender_kecamatan'        => 'Kecamatan pengirim',
			'sender_kelurahan'        => 'Kelurahan pengirim',
			'sender_address_detail'   => 'Alamat pengirim',
			'receiver_name'           => 'Nama penerima',
			'receiver_phone'          => 'Telepon penerima',
			'receiver_city'           => 'Kota penerima',
			'receiver_zipcode'        => 'Kode pos penerima',
			'receiver_address_detail' => 'Alamat penerima',
			'commodity_id'            => 'Kategori komoditi',
			'commodity_detail'        => 'Isi barang',
			'commodity_detail_en'     => 'English description',
			'customs_value_usd'       => 'Nilai barang (customs value)',
		];

		foreach ($required as $key => $label) {
			if (trim((string) ($post[$key] ?? '')) === '') {
				return $label . ' wajib diisi.';
			}
		}

		return NULL;
	}

	/**
	 * Parse dimensi. Hasil: satu elemen per koli (qty di-expand).
	 */
	private function _kec_parse_koli(array $post)
	{
		$lengths = (array) ($post['dim_length'] ?? []);
		$widths  = (array) ($post['dim_width'] ?? []);
		$heights = (array) ($post['dim_height'] ?? []);


		$items  = [];
		$volume = 0.0;

		foreach ((array) ($post['dim_qty'] ?? []) as $key => $qty_raw) {

			$qty = (int) $this->_kec_parse_number($qty_raw);
			if ($qty <= 0) {
				continue;
			}

			$p = $this->_kec_parse_number($lengths[$key] ?? 0);
			$l = $this->_kec_parse_number($widths[$key] ?? 0);
			$t = $this->_kec_parse_number($heights[$key] ?? 0);

			$p = round($this->_kec_parse_number($lengths[$key] ?? 0), 2);
			$l = round($this->_kec_parse_number($widths[$key] ?? 0), 2);
			$t = round($this->_kec_parse_number($heights[$key] ?? 0), 2);

			if ($p <= 0 || $l <= 0 || $t <= 0) {
				return [
					'ok'      => FALSE,
					'message' => 'Panjang, lebar, dan tinggi setiap koli wajib diisi dengan benar.',
				];
			}

			$volume += (($p * $l * $t) / self::KEC_VOLUME_DIVISOR) * $qty;

			for ($i = 0; $i < $qty; $i++) {
				$items[] = ['length' => $p, 'width' => $l, 'height' => $t];
			}
		}

		return ['ok' => TRUE, 'items' => $items, 'volume' => round($volume, 2)];
	}

	/**
	 * Harga per kg. Non-tier: dari pricelist. Tier: dari pricelist_tiers.
	 * Berat di atas tier terakhir memakai tier terakhir.
	 * Return ['cost' => , 'sell' => ] atau NULL kalau tidak ada harga.
	 */
	private function _kec_resolve_price($pricelist, $weight)
	{
		$price_kribo = (float) ($pricelist->price_kribo ?? 0);
		$price_smesco = (float) ($pricelist->price_smesco ?? 0);
		$price_kecamatan = (float) ($pricelist->price_kecamatan ?? 0);

		if ((int) $pricelist->is_tiered === 1) {

			$tiers = $this->db
				->where('pricelist_id', $pricelist->id)
				->order_by('min_weight', 'ASC')
				->get('pricelist_tiers')
				->result();

			if (!$tiers) {
				return NULL;
			}

			$picked = NULL;

			foreach ($tiers as $tier) {
				if ($weight >= (float) $tier->min_weight && $weight <= (float) $tier->max_weight) {
					$picked = $tier;
					break;
				}
			}

			$last = end($tiers);

			if (!$picked && $weight > (float) $last->max_weight) {
				$picked = $last;
			}

			if (!$picked) {
				return NULL; // celah antar tier / di bawah tier pertama
			}

			$price_kribo = isset($picked->price_kribo) ? (float) $picked->price_kribo : $price_kribo;
			$price_smesco = isset($picked->price_smesco) ? (float) $picked->price_smesco : $price_smesco;
			$price_kecamatan = (float) ($picked->price_kecamatan ?? 0);
		}

		if ($price_kecamatan <= 0) {
			return NULL;
		}

		return ['price_kribo' => $price_kribo, 'price_smesco' => $price_smesco, 'price_kecamatan' => $price_kecamatan];
	}

	/**
	 * Hitung biaya addon per koli. min_charge berlaku per koli.
	 */
	private function _kec_calc_addons(array $codes, array $koli, $total_koli)
	{
		$result = ['total' => 0.0, 'rows' => []];

		if (!$codes) {
			return $result;
		}

		$addons = $this->db
			->where_in('code', $codes)
			->where('is_active', 1)
			->get('master_addons')
			->result();

		foreach ($addons as $addon) {

			$factor = (float) ($addon->base_factor ?? 0);
			$min    = (float) ($addon->min_charge ?? 0);
			$fee    = 0.0;

			if ($koli) {
				foreach ($koli as $k) {
					switch ($addon->calc_method) {
						case 'VOLUME':
							$per_koli = $k['length'] * $k['width'] * $k['height'] * $factor;
							break;
						case 'VOLUME_PLUS':
							$per_koli = ($k['length'] + 10) * ($k['width'] + 10) * ($k['height'] + 10) * $factor;
							break;
						case 'PER_KOLI':
							$per_koli = $factor;
							break;
						default:
							continue 2;
					}

					$fee += max($per_koli, $min);
				}
			} elseif ($addon->calc_method === 'PER_KOLI') {
				$fee = $factor * $total_koli;
			}

			if ($fee > 0) {
				$result['total'] += $fee;
				$result['rows'][] = [
					'shipment_id'  => NULL,
					'addon_id'     => $addon->id,
					'addon_amount' => $fee,
				];
			}
		}

		return $result;
	}

	/**
	 * TESS (surcharge vendor sementara), minimal KEC_TESS_MIN.
	 * Tidak masuk margin, mengikuti save_intl().
	 */
	private function _kec_calc_tess($pricelist, $chargeable)
	{
		if (empty($pricelist->vendor_id)) {
			return 0.0;
		}

		$vendor = $this->db
			->where('id', $pricelist->vendor_id)
			->where('is_active', '1')
			->get('vendors')
			->row();

		$rate = $vendor ? (float) ($vendor->temporary_surcharge ?? 0) : 0;

		return $rate > 0 ? max($chargeable * $rate, self::KEC_TESS_MIN) : 0.0;
	}

	/**
	 * Nama wilayah dari ID. $table/$field hanya dari konstanta di kode.
	 */
	private function _kec_region_name($table, $id, $field)
	{
		if (empty($id)) {
			return NULL;
		}

		$row = $this->db->select($field)->where('id', $id)->get($table)->row();

		return $row ? $row->$field : NULL;
	}

	/* -------------------------------------------------------------
	 * FOTO
	 * ------------------------------------------------------------- */

	private function _kec_photo_dir()
	{
		return FCPATH . 'uploads/shipments/booking/';
	}

	/**
	 * Validasi foto berdasarkan ISI file (getimagesize), bukan
	 * $_FILES['type'] atau nama file yang dikirim browser.
	 * Ekstensi ditentukan dari hasil deteksi, jadi tidak bisa .php.
	 */
	private function _kec_validate_photo(array $files)
	{
		$file = $files['shipment_photo'] ?? NULL;

		if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			return ['ok' => FALSE, 'message' => 'Foto kargo wajib diupload.'];
		}

		if ($file['size'] > 2097152) { // 2MB
			return ['ok' => FALSE, 'message' => 'Ukuran foto maksimal 2MB.'];
		}

		$info = @getimagesize($file['tmp_name']);
		$ext_map = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png'];

		if (!$info || !isset($ext_map[$info[2]])) {
			return ['ok' => FALSE, 'message' => 'Format foto tidak valid. Gunakan JPG, JPEG, atau PNG.'];
		}

		return ['ok' => TRUE, 'ext' => $ext_map[$info[2]]];
	}

	/**
	 * Pindahkan foto. Return nama file, atau FALSE kalau gagal.
	 */
	private function _kec_store_photo(array $file, $no_resi, $ext)
	{
		$dir = $this->_kec_photo_dir();

		if (!is_dir($dir) && !mkdir($dir, 0755, TRUE) && !is_dir($dir)) {
			return FALSE;
		}

		$name = 'BOOKING_' . $no_resi . '_' . time() . '.' . $ext;

		return move_uploaded_file($file['tmp_name'], $dir . $name) ? $name : FALSE;
	}

	/* -------------------------------------------------------------
	 * CUSTOMER
	 * ------------------------------------------------------------- */

	/**
	 * Upsert master_customers berdasarkan phone.
	 * created_by hanya diisi saat insert (tidak menimpa pembuat awal).
	 */
	private function _kec_upsert_customer(array $customer, $user_id, $now)
	{
		$customer['updated_at'] = $now;

		$exists = $this->db
			->where('phone', $customer['phone'])
			->count_all_results('master_customers') > 0;

		if ($exists) {
			$this->db
				->where('phone', $customer['phone'])
				->update('master_customers', $customer);
			return;
		}

		$customer['created_by'] = $user_id;
		$this->db->insert('master_customers', $customer);
	}

	public function create_kec_handover($agent_id, $user_id, array $ids, array $fwd = [])
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

		if (!$ids) {
			return $this->_kec_fail('Pilih minimal satu shipment.');
		}

		// if (empty($fwd['awb'])) {
		// 	return $this->_kec_fail('AWB Indah Kargo wajib diisi.');
		// }

		$now = date('Y-m-d H:i:s');

		$this->db->trans_start();

		$rows = $this->db
			->select('id, agent_origin')
			->where_in('id', $ids)
			->where('agent_id', $agent_id)
			->where('category', 'INTERNATIONAL')
			->where('status', 'READY_FOR_HANDOVER')
			->get('shipments')
			->result_array();

		if (count($rows) !== count($ids)) {
			$this->db->trans_rollback();
			return $this->_kec_fail('Ada shipment yang tidak ditemukan atau belum siap diserahkan.');
		}

		$manifest_number = $this->_kec_manifest_number();

		$this->db->insert('agent_manifests', [
			'manifest_number' => $manifest_number,
			'agent_id'        => $agent_id,
			'destination_hub' => $fwd['hub'] ?? 'INDAH KARGO',
			'handover_date'   => $now,
			'forwarder_name'  => $fwd['name'] ?? NULL,
			'forwarder_phone' => $fwd['phone'] ?? NULL,
			'status'          => 'HANDED_OVER',
			'created_by'      => $user_id,
			'created_at'      => $now,
		]);
		$manifest_id = (int) $this->db->insert_id();

		$items = $tracking = [];
		foreach ($rows as $r) {
			$items[] = [
				'agent_manifest_id' => $manifest_id,
				'shipment_id'       => $r['id'],
				'created_at'        => $now,
			];
			$tracking[] = [
				'shipment_id' => $r['id'],
				'status'      => 'HANDOVER_TO_COURIER',
				'location'    => $r['agent_origin'] ?: 'AGEN KECAMATAN',
				'note'        => 'Shipment diserahkan ke Indah Kargo. AWB: ' . $fwd['awb'] . '. Manifest: ' . $manifest_number,
				'created_by'  => $user_id,
				'created_at'  => $now,
			];
		}

		$this->db->insert_batch('agent_manifest_items', $items);

		// Guard atomic: hanya yang masih READY_FOR_HANDOVER
		$this->db
			->where_in('id', $ids)
			->where('agent_id', $agent_id)
			->where('status', 'READY_FOR_HANDOVER')
			->update('shipments', [
				'origin_courier' => 'INDAH_KARGO',
				'origin_courier_awb' => $fwd['awb'],
				'status'         => 'HANDOVER_TO_COURIER',
				'updated_by'     => $user_id,
				'updated_at'     => $now,
			]);

		if ($this->db->affected_rows() !== count($ids)) {
			$this->db->trans_rollback();
			return $this->_kec_fail('Shipment sudah diproses sebelumnya.');
		}

		$this->db->insert_batch('shipment_tracking', $tracking);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return $this->_kec_fail('Gagal menyimpan proses handover.');
		}

		return [
			'status'          => TRUE,
			'manifest_id'     => $manifest_id,
			'manifest_number' => $manifest_number,
			'total'           => count($ids),
		];
	}

	private function _kec_manifest_number()
	{
		$prefix = 'KM' . date('ymd');

		$row = $this->db
			->select('manifest_number')
			->like('manifest_number', $prefix, 'after')
			->order_by('manifest_number', 'DESC')
			->limit(1)
			->get('agent_manifests')
			->row();

		$seq = $row ? ((int) substr($row->manifest_number, -4)) + 1 : 1;

		return $prefix;
	}
}
