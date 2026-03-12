<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library(['session', 'form_validation']);
		$this->load->helper(['string', 'date']);
		$this->load->model(['M_Auth', 'M_Booking']);
	}
	public function index()
	{
		$data = [
			'title' => 'Home',
			'segment' => 'home',
			'pages' => 'pages/front/home/v_home'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function about()
	{
		$data = [
			'title' => 'About',
			'segment' => 'about',
			'pages' => 'pages/front/home/v_about'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function service()
	{
		$data = [
			'title' => 'Service',
			'segment' => 'service',
			'pages' => 'pages/front/home/v_service'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function track($resi = NULL)
	{
		$nomor_resi = ($resi) ? $resi : $this->input->post('nomor_resi');

		if (!empty($nomor_resi)) {
			if (!$resi) {
				// Validasi hanya saat form POST
				$this->form_validation->set_rules('g-recaptcha-response', 'reCAPTCHA', 'required');

				if ($this->form_validation->run() == FALSE) {
					$this->session->set_flashdata('message', 'reCAPTCHA is required.');
					redirect('home/track');
				}

				$recaptchaResponse = $this->input->post('g-recaptcha-response');
				$secretKey = '6LcrQFQqAAAAAKO2oNf5Gx-8MOR1vExhoD0oiDVY'; // Hosting
				$userIP = $this->input->ip_address();

				$url = 'https://www.google.com/recaptcha/api/siteverify';
				$data = [
					'secret' => $secretKey,
					'response' => $recaptchaResponse,
					'remoteip' => $userIP
				];

				$options = [
					'http' => [
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'method'  => 'POST',
						'content' => http_build_query($data)
					]
				];

				$context = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				$resultData = json_decode($result);

				if (!$resultData->success) {
					$this->session->set_flashdata('message', 'Unable to verify reCAPTCHA. Please try again later.');
					redirect('home/track');
				}
			}

			$cek_resi = $this->M_Booking->cekResi($nomor_resi);
			if ($cek_resi) {
				$resi = $this->M_Booking->getResi($nomor_resi);
				$this->session->set_flashdata('message', "Nomor resi $nomor_resi ditemukan!");
			} else {
				$this->session->set_flashdata('message', "Nomor resi $nomor_resi tidak ditemukan!");
			}

			$data = [
				'title' => 'Track',
				'segment' => 'track',
				'pages' => 'pages/front/home/v_track',
				'resi' => ($resi) ? $resi : '',
			];
		} else {
			$data = [
				'title' => 'Track',
				'segment' => 'track',
				'pages' => 'pages/front/home/v_track',
				'resi' => '',
			];
		}

		$this->load->view('pages/front/index', $data);
	}

	public function agent()
	{
		$data = [
			'title' => 'Kemitraan',
			'segment' => 'agent',
			'pages' => 'pages/front/home/v_agent'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function cek_ongkir()
	{
		$data = [
			'title' => 'Cek Ongkir',
			'segment' => 'cek_ongkir',
			'pages' => 'pages/front/home/v_cek_ongkir'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function autocompleteOrigin()
	{
		$term = $this->input->get('term');

		$this->db->like('city_origin', $term);
		$this->db->group_by('city_origin');
		$query = $this->db->get('mt_pricelist');

		$result = $query->result_array();
		$items = [];
		foreach ($result as $row) {
			$items[] = [
				'label' => $row['city_origin'],
				'value' => $row['city_origin'],
			];
		}
		echo json_encode($items);
	}

	public function autocompleteDestination()
	{
		$term = $this->input->get('term');

		$this->db->like('destination_name', $term);
		$this->db->order_by('destination_name', 'ASC');

		$query = $this->db->get('mt_destination');

		$result = $query->result_array();
		$items = [];

		foreach ($result as $row) {
			$items[] = [
				'label' => $row['destination_name'],
				'value' => $row['destination_name'],
			];
		}

		echo json_encode($items);
	}

	public function getPrice()
	{
		$origin = $this->input->post('origin');
		$destination = $this->input->post('destination');
		$jenis = $this->input->post('jenis_pengiriman');
		$chargeable = (float) $this->input->post('chargeable');

		// cek dulu di tabel mt_destination
		$query_zone = $this->db->where('destination_name', $destination)->get('mt_destination')->row_array();

		$zone = $query_zone['zone'];

		if ($zone) {
			$destination = $zone;
		}

		$jenis = $this->db->select('jenis')->where('city', $destination)->get('mt_pricelist')->row_array()['jenis'];
		// print_r($jenis);


		$this->db->where('jenis', $jenis);
		$this->db->where('city_origin', $origin);
		$this->db->where('city', $destination);
		$this->db->where('is_active', '1');

		if ($jenis === 'IE') {
			if ($chargeable >= 21) {
				$this->db->where('min_chargeable', '21');
			} else {
				$this->db->where('min_chargeable <=', $chargeable);
				$this->db->where('max_chargeable >=', $chargeable);
			}
		} else {
			// Untuk jenis lain, ambil sesuai range chargeable
			if ($jenis == 'IR' || $jenis == "IP") {
				$this->db->where('min_chargeable <=', $chargeable);
				$this->db->where('max_chargeable >=', $chargeable);
			}
		}

		$price = $this->db->get('mt_pricelist')->row_array();

		$harga_up = 0;
		$harga_jual = 0;
		$per_kg = 0;

		if ($price) {
			if ($jenis === 'IE') {
				if ($chargeable >= 21) {
					$harga_up = (float) $price['total'] * $chargeable;
					$harga_jual = (float) $price['all_in_smu'] * $chargeable;
				} else {
					$harga_up = (float) $price['total'];
					$harga_jual = (float) $price['all_in_smu'];
				}
			} else if ($jenis === 'IP') {
				if ($chargeable >= 31 && $chargeable <= 300) {
					$harga_up = (float) $price['total'] * $chargeable;
					$harga_jual = (float) $price['all_in_smu'] * $chargeable;
				} else {
					$harga_up = (float) $price['total'];
					$harga_jual = (float) $price['all_in_smu'];
				}
			} else {
				// Selain IE 21UP, harga tetap dikali chargeable
				$harga_up = (float) $price['total'] * $chargeable;
				$harga_jual = (float) $price['all_in_smu'] * $chargeable;
			}

			$per_kg = $price['total'];
		}

		$data = [
			'chargeable' => $chargeable,
			'per_kg' => $per_kg,
			'harga_up' => $harga_up,
			'harga_jual' => round($harga_jual),
			'jenis' => $jenis
		];

		echo json_encode($data);
	}

	public function pricelist()
	{
		$data = [
			'title' => 'Pricelist',
			'segment' => 'pricelist',
			'pages' => 'pages/front/home/v_pricelist'
		];

		$this->load->view('pages/front/index', $data);
	}

	public function outlet()
	{
		$data = [
			'title' => 'Outlet',
			'segment' => 'outlet',
			'pages' => 'pages/front/home/v_outlet',
			'outlets' => $this->db->where(['is_active' => '1'])->order_by('nama_pendaftar', 'ASC')->get('partner')->result()
		];

		$this->load->view('pages/front/index', $data);
	}
}
