<?php
defined('BASEPATH') or exit('No direct script access allowed');

abstract class API_Controller extends CI_Controller
{
	protected $client_data;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Api');
		$this->_parse_json_input();
		$this->_authenticate();
	}

	private function _parse_json_input()
	{
		$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
		if (stripos($content_type, 'application/json') !== false) {
			$json = json_decode($this->input->raw_input_stream, TRUE);
			if ($json) $_POST = $json;
		}
	}

	private function _authenticate()
	{
		$key = $this->input->get_request_header('X-API-KEY', TRUE);
		$ip  = $this->input->ip_address();

		if (!$key) {
			$this->_respond(['status' => false, 'message' => 'API Key tidak ditemukan.'], 401);
		}

		$client = $this->M_Api->validate_access($key, $ip);

		if (!$client) {
			$this->_respond(['status' => false, 'message' => 'API Key tidak valid atau tidak aktif.'], 401);
		}

		if ($client === 'IP_BLOCKED') {
			$this->_respond(['status' => false, 'message' => 'IP ' . $ip . ' tidak diizinkan.'], 403);
		}

		if ($auth === 'LIMIT_EXCEEDED') {
			$this->_respond(['status' => false, 'message' => 'Batas request harian tercapai.'], 429);
		}

		// ── Hit limit check
		if ($client->hit_limit !== NULL) {
			$hits_today = $this->M_Api->count_hits_today($client->id);
			if ($hits_today >= $client->hit_limit) {
				$this->_respond([
					'status'  => false,
					'message' => 'Batas request harian tercapai (' . $client->hit_limit . ' requests/hari).',
				], 429);
			}
		}

		$this->client_data = $client;
	}

	// Dipanggil manual di tiap endpoint setelah proses selesai
	protected function _log($endpoint, $response_code, $request_params = [])
	{
		$this->M_Api->write_log([
			'api_client_id'   => $this->client_data->id,
			'endpoint'        => $endpoint,
			'method'          => strtoupper($this->input->method()),
			'request_params'  => json_encode($request_params, JSON_UNESCAPED_UNICODE),
			'response_code'   => $response_code,
			'ip_address'      => $this->input->ip_address(),
			'user_agent'      => $this->input->user_agent() ?? '',
		]);
	}

	protected function _respond($data, $code = 200)
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header($code)
			->set_output(json_encode($data, JSON_UNESCAPED_UNICODE))
			->_display();
		exit;
	}
}
