<?php
defined('BASEPATH') or exit('No direct script access allowed');

abstract class Public_Api_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->_parse_json_input();
	}

	// Support JSON body & form-urlencoded sekaligus
	private function _parse_json_input()
	{
		$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
		if (stripos($content_type, 'application/json') !== false) {
			$json = json_decode($this->input->raw_input_stream, TRUE);
			if ($json) $_POST = $json;
		}
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
