<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_management extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Api');
		$this->load->library('form_validation');
	}

	// application/controllers/Api_management.php
	public function index()
	{
		$data = [
			'title' => 'API Management',
			'clients' => $this->M_Api->get_all_clients()
		];
		$this->render('app/pages/api_management/index', $data);
	}

	public function create()
	{
		$this->form_validation->set_rules('agent_id', 'Agen', 'required');
		$this->form_validation->set_rules('client_name', 'Nama Aplikasi', 'required|trim');

		if ($this->form_validation->run()) {
			$data = [
				'agent_id'     => $this->input->post('agent_id'),
				'client_name'  => $this->input->post('client_name'),
				'api_key'      => $this->M_Api->generate_unique_key(),
				'ip_whitelist' => $this->input->post('ip_whitelist'),
				'hit_limit'    => $this->input->post('hit_limit') ?: 1000,
				'is_active'    => 1
			];
			$this->M_Api->insert_client($data);
			$this->session->set_flashdata('success', 'API Key berhasil dibuat!');
			redirect('api_management');
		}

		$data = [
			'title'  => 'Register API Client',
			'agents' => $this->db->get('agents')->result()
		];
		$this->render('app/pages/api_management/form', $data);
	}

	public function regenerate($id)
	{
		if ($this->input->method() !== 'post') show_404();
		$new_key = $this->M_Api->generate_unique_key();
		$this->M_Api->update_client($id, ['api_key' => $new_key]);
		$this->session->set_flashdata('success', 'API Key baru telah di-generate!');
		redirect('api_management');
	}

	public function delete($id)
	{
		if ($this->input->method() !== 'post') show_404();
		$this->M_Api->delete_client($id);
		$this->session->set_flashdata('success', 'API Client berhasil dihapus.');
		redirect('api_management');
	}
}
