<?php 
defined('BASEPATH') or exit('No direct script access allowed');

abstract class API_Controller extends CI_Controller
{

	protected $client_data;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Api');

		$key = $this->input->get_request_header('X-API-KEY', TRUE);
		$ip  = $this->input->ip_address();

		$auth = $this->M_Api->validate_access($key, $ip);

		if (!$auth) {
			$this->_respond(['message' => 'Invalid API Key'], 401);
		}

		if ($auth === 'IP_BLOCKED') {
			$this->_respond(['message' => 'Your IP (' . $ip . ') is not whitelisted'], 403);
		}

		$this->client_data = $auth;
	}

	protected function _respond($data, $code = 200)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($code)
			->set_output(json_encode($data))
			->_display();
		exit;
	}
}
