<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Api extends MY_Model
{
	public function validate_access($key, $incoming_ip)
	{
		$this->db->select('api_clients.*, agents.name as agent_name');
		$this->db->from('api_clients');
		$this->db->join('agents', 'agents.id = api_clients.agent_id');
		$this->db->where('api_key', $key);
		$this->db->where('api_clients.is_active', 1);
		$client = $this->db->get()->row();

		if (!$client) return false;

		// Cek Whitelist IP
		if (!empty($client->ip_whitelist)) {
			$allowed_ips = array_map('trim', explode(',', $client->ip_whitelist));
			if (!in_array($incoming_ip, $allowed_ips)) {
				return 'IP_BLOCKED'; // IP tidak terdaftar
			}
		}
		// Cek hit limit (hari ini)
		$today_hits = $this->db->where('api_client_id', $client->id)
			->where('DATE(created_at)', date('Y-m-d'))
			->count_all_results('api_logs');

		if ($client->hit_limit && $today_hits >= $client->hit_limit) {
			return 'LIMIT_EXCEEDED';
		}

		return $client;
	}

	// application/models/M_Api.php
	public function get_all_clients()
	{
		return $this->db->select('api_clients.*, agents.name as agent_name')
			->from('api_clients')
			->join('agents', 'agents.id = api_clients.agent_id')
			->order_by('created_at', 'DESC')
			->get()->result();
	}

	public function generate_unique_key()
	{
		// Bikin string random 32 karakter
		return bin2hex(random_bytes(16));
	}

	public function insert_client($data)
	{
		return $this->db->insert('api_clients', $data);
	}

	public function update_client($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('api_clients', $data);
	}

	public function delete_client($id)
	{
		return $this->db->delete('api_clients', ['id' => $id]);
	}

	public function log_request($client_id, $endpoint, $method, $params, $response_code, $ip, $ua)
	{
		$this->db->insert('api_logs', [
			'api_client_id'   => $client_id,
			'endpoint'        => $endpoint,
			'method'          => $method,
			'request_params'  => json_encode($params),
			'response_code'   => $response_code,
			'ip_address'      => $ip,
			'user_agent'      => $ua
		]);
	}

	// Cek berapa kali client hit API hari ini
	public function count_hits_today($client_id)
	{
		return $this->db
			->where('api_client_id', $client_id)
			->where('DATE(created_at)', date('Y-m-d'))
			->count_all_results('api_logs');
	}

	// Catat log request
	public function write_log($data)
	{
		return $this->db->insert('api_logs', $data);
	}

	// Update validate_access: IP whitelist opsional
	// public function validate_access($key, $ip)
	// {
	// 	$client = $this->db
	// 		->where('api_key', $key)
	// 		->where('is_active', 1)
	// 		->get('api_clients')
	// 		->row();

	// 	if (!$client) return false;

	// 	// Skip IP check kalau whitelist kosong
	// 	if (!empty($client->ip_whitelist)) {
	// 		$allowed_ips = array_map('trim', explode(',', $client->ip_whitelist));
	// 		if (!in_array($ip, $allowed_ips)) return 'IP_BLOCKED';
	// 	}

	// 	return $client;
	// }
}
