<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();
		// Jika ingin load model, library, helper yang dipakai di banyak method, bisa dilakukan di sini
		$this->load->helper(['url', 'tabler_icon']);
		$this->load->model(['M_Shipment']);
		$this->load->library(['api_whatsapp']);
	}

	public function index()
	{
		$data = [
			'title' => 'Home',
			'pages' => 'landing-page/pages/v_home'
		];
		$this->load->view('landing-page/index', $data);
	}

	public function tracking()
	{
		$awb = $this->input->get('awb', TRUE);
		$this->load->model('M_Shipment');

		$data = [
			'title'    => 'Tracking Resi - Smesco Express',
			'awb'      => $awb,
			'pages'    => 'landing-page/pages/tracking',
			'shipment' => NULL,
			'history'  => []
		];

		if ($awb) {
			// 1. Ambil data master shipment
			$shipment = $this->M_Shipment->getResi($awb);

			if ($shipment) {
				$data['shipment'] = $shipment;
				// 2. Ambil riwayat tracking berdasarkan ID shipment
				$data['history']  = $this->M_Shipment->get_tracking_public($shipment['id']);
			}
		}

		$this->load->view('landing-page/index', $data);
	}

	// Tambahkan di application/controllers/Home.php

	public function ajax_cek_ongkir_public()
	{
		// Karena ini diakses publik, kita tetap butuh model pricelist
		$this->load->model('M_Pricelist');

		$origin      = $this->input->post('origin', TRUE);
		$destination = $this->input->post('destination', TRUE);
		$weight      = floatval($this->input->post('weight') ?? 1);

		// Cari pricelist yang aktif
		$pricelist = $this->db->get_where('pricelist', [
			'origin'      => $origin,
			'destination' => $destination,
			'is_active'   => 1
		])->row();

		if ($pricelist) {
			$price = $pricelist->price_smesco; // Default harga jual

			// Logic Tiering jika rute Internasional
			if ($pricelist->is_tiered == 1) {
				$tier = $this->db->where('pricelist_id', $pricelist->id)
					->where('min_weight <=', $weight)
					->where('max_weight >=', $weight)
					->get('pricelist_tiers')->row();
				if ($tier) {
					$price = $tier->price_smesco;
				} else {
					// Ambil tier tertinggi kalau berat lewat batas
					$last_tier = $this->db->where('pricelist_id', $pricelist->id)
						->order_by('max_weight', 'DESC')->get('pricelist_tiers', 1)->row();
					if ($last_tier) $price = $last_tier->price_smesco;
				}
			}

			$total = $price * ceil($weight);

			echo json_encode([
				'status' => true,
				'data'   => [
					'service' => $pricelist->category,
					'price'   => $price,
					'total'   => $total,
					'weight'  => ceil($weight)
				]
			]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Rute tidak ditemukan.']);
		}
	}

	// Tambahkan di application/controllers/Home.php

	public function ajax_autocomplete_route()
	{
		$term = $this->input->get('term', TRUE);
		if (!$term) return;

		// Kita ambil dari tabel pricelist supaya user hanya memilih rute yang TERSEDIA harganya
		// Menggunakan UNION agar dapet list unik dari origin dan destination
		$query = $this->db->query("
        SELECT DISTINCT city FROM (
            SELECT origin as city FROM pricelist WHERE is_active = 1
            UNION
            SELECT destination as city FROM pricelist WHERE is_active = 1
        ) as routes 
        WHERE city LIKE ? 
        LIMIT 10
    ", ["%$term%"]);

		$result = $query->result();

		$data = [];
		foreach ($result as $row) {
			$data[] = $row->city;
		}

		echo json_encode($data);
	}

	// Nampilin Halaman Cek Ongkir Detail
	public function cek_ongkir()
	{
		$data = [
			'title' => 'Kalkulator Ongkir Detail & Pickup',
			'pages' => 'landing-page/pages/cek_ongkir_detail',
			// Ambil data area pickup yang aktif
			'pickup_rates' => $this->db->get_where('master_pickup_rates', ['is_active' => 1])->result()
		];
		$this->load->view('landing-page/index', $data);
	}

	// AJAX Endpoint buat ngambil rate per kg secara publik
	public function ajax_get_rate_public()
	{
		$origin      = $this->input->post('origin', TRUE);
		$destination = $this->input->post('destination', TRUE);
		$weight      = floatval($this->input->post('weight') ?? 1);

		// Ambil rute pertama yang aktif (asumsi layanan standar/default)
		$pricelist = $this->db->get_where('pricelist', [
			'origin'      => $origin,
			'destination' => $destination,
			'is_active'   => 1
		])->row();

		if ($pricelist) {
			$price = $pricelist->price_smesco;

			// Cek Tiering Internasional
			if ($pricelist->is_tiered == 1) {
				$tier = $this->db->where('pricelist_id', $pricelist->id)
					->where('min_weight <=', $weight)
					->where('max_weight >=', $weight)
					->get('pricelist_tiers')->row();
				if ($tier) {
					$price = $tier->price_smesco;
				} else {
					$last_tier = $this->db->where('pricelist_id', $pricelist->id)
						->order_by('max_weight', 'DESC')->get('pricelist_tiers', 1)->row();
					if ($last_tier) $price = $last_tier->price_smesco;
				}
			}

			echo json_encode([
				'status' => true,
				'data'   => [
					'price_per_kg'  => $price,
					'min_weight_kg' => $pricelist->min_weight_kg,
					'category'      => $pricelist->category,
					'is_tiered'     => $pricelist->is_tiered
				]
			]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Rute tidak ditemukan.']);
		}
	}

	public function confirm_payment($no_resi)
	{
		$shipment = $this->M_Shipment->get_by_no_resi($no_resi);

		if (!$shipment) {
			show_404();
		}

		// Cek expired
		if ($shipment->payment_expired_at && strtotime($shipment->payment_expired_at) < time()) {
			$data['expired'] = TRUE;
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			// Upload bukti transfer
			if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK) {
				$this->session->set_flashdata('error', 'File tidak valid.');
				redirect('home/confirm_payment/' . $no_resi);
			}

			$file    = $_FILES['payment_proof'];
			$allowed = ['image/jpeg', 'image/jpg', 'image/png'];

			if (!in_array($file['type'], $allowed) || $file['size'] > 2 * 1024 * 1024) {
				$this->session->set_flashdata('error', 'Format/ukuran tidak valid.');
				redirect('home/confirm_payment/' . $no_resi);
			}

			$upload_dir = FCPATH . 'uploads/payment_proofs/' . date('Y/m/');
			if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, TRUE);

			$ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
			$file_name = $no_resi . '_proof_' . time() . '.' . $ext;

			if (!move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
				$this->session->set_flashdata('error', 'Gagal upload file.');
				redirect('home/confirm_payment/' . $no_resi);
			}

			$proof_path = 'uploads/payment_proofs/' . date('Y/m/') . $file_name;

			$this->db->update('shipments', [
				'payment_proof' => $proof_path,
				'payment_status' => 'UPLOADED'
			], ['no_resi' => $no_resi]);

			// Notifikasi WA ke admin
			$finance = $this->db->select('phone')->where('confirm_payment', '1')->get('users')->row();
			$admin_phone = $finance->phone;

			$url = base_url('auth');

			$pesan_admin = "*SMESCO EXPRESS — Bukti Transfer Masuk*\n\n" .
				"No. Resi: *$no_resi*\n" .
				"Pengirim: *{$shipment->sender_name}*\n" .
				"Total: *Rp " . number_format($shipment->total_amount, 0, ',', '.') . "*\n\n" .
				"Silakan verifikasi di panel admin." .
				"$url\n\n" .;

			try {
				$this->api_whatsapp->wa_notif_v2($admin_phone, $pesan_admin);
			} catch (Exception $e) {
				log_message('error', 'WA Admin Notif Error: ' . $e->getMessage());
			}

			$data['success'] = TRUE;
		}

		$data['title'] = 'Konfirmasi Pembayaran - ' . $shipment->no_resi;
		$data['shipment'] = $shipment;
		$data['pages'] = 'landing-page/pages/payment_confirm';

		$this->load->view('landing-page/index', $data);
	}
}
