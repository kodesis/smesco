<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Shipment extends CI_Model
{
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
		$this->db->select('shipments.*, service_types.name as service_name, service_types.code as service_code, users.name as created_by_name');
		$this->db->from('shipments');
		$this->db->join('service_types', 'service_types.id = shipments.service_type_id', 'left');
		$this->db->join('users', 'users.id = shipments.created_by', 'left'); // Join ke tabel users buat nampilin nama admin yang input resi

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
			->where_in('vendor', ['TLX', 'CHOIR'])
			->get()->row_array();

		if (!$shipment || empty($shipment['vendor_connote'])) return false;
		if ($shipment['status'] === 'DELIVERED') return false;

		// Resolve vendor class
		$vendor_map = [
			'TLX'            => 'Tlx_tracking',
			'CHOIR'          => 'Choir_tracking',
		];

		$class_name = $vendor_map[$shipment['vendor']] ?? null;
		if (!$class_name) return false;

		require_once APPPATH . 'libraries/tracking/Tracking_contract.php';
		require_once APPPATH . 'libraries/tracking/' . $class_name . '.php';

		$tracker = new $class_name();
		$items   = $tracker->fetch($shipment['vendor_connote']);

		if (empty($items)) return false;

		// Ambil existing timestamps untuk dedup
		$existing = $this->db->select('created_at')
			->from('shipment_tracking')
			->where('shipment_id', $shipment_id)
			->get()->result_array();

		$existing_timestamps = array_column($existing, 'created_at');
		$inserted_count = 0;

		// Urutkan $items ASC berdasarkan created_at agar pemrosesan berurutan dari lama ke baru
		usort($items, function ($a, $b) {
			return strtotime($a['created_at']) <=> strtotime($b['created_at']);
		});

		foreach ($items as $item) {
			if (in_array($item['created_at'], $existing_timestamps)) continue;

			$this->db->insert('shipment_tracking', [
				'shipment_id' => $shipment_id,
				'status'      => $item['status'],
				'note'        => $item['note'],
				'location'    => $item['location'],
				'created_by'  => 0,
				'created_at'  => $item['created_at'],
			]);

			$inserted_count++;
		}

		// ── UPDATE STATUS TERBARU KE TABEL SHIPMENTS ──
		// Ambil record tracking paling akhir dari array items yang sudah diurutkan
		$latest_item = end($items);

		if ($latest_item && !empty($latest_item['status'])) {
			// Jika ada data baru atau status di shipments berbeda dengan status paling akhir dari vendor
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
}
