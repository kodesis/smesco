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
}
