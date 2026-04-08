<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Visitor_log_hook
{
	private $CI;

	// Ekstensi statis yang tidak perlu dicatat
	private $skip_extensions = [
		'css',
		'js',
		'png',
		'jpg',
		'jpeg',
		'gif',
		'ico',
		'svg',
		'woff',
		'woff2',
		'ttf',
		'eot',
		'map',
		'webp'
	];

	// Known bots (sebagian dari user-agent string)
	private $known_bots = [
		'bot',
		'crawler',
		'spider',
		'slurp',
		'facebookexternalhit',
		'curl',
		'wget',
		'python-requests',
		'go-http-client',
		'axios',
		'httpie',
		'java/',
		'libwww',
		'okhttp',
		'scrapy',
		'postman',
		'insomnia',
		'ahrefsbot',
		'semrushbot',
		'mj12bot',
		'dotbot',
		'bingbot',
		'googlebot',
		'yandexbot',
		'duckduckbot',
	];

	// Path mencurigakan
	private $suspicious_paths = [
		'.env',
		'wp-admin',
		'wp-login',
		'phpmyadmin',
		'pma',
		'adminer',
		'shell',
		'eval',
		'base64',
		'passwd',
		'etc/shadow',
		'proc/self',
		'../',
		'.git',
		'xmlrpc',
		'config.php',
		'setup.php',
		'install.php',
		'upgrade.php',
	];

	public function log_request()
	{
		$this->CI = &get_instance();

		$uri = $this->CI->uri->uri_string();

		// Skip asset statis
		$ext = strtolower(pathinfo(parse_url($uri, PHP_URL_PATH), PATHINFO_EXTENSION));
		if (in_array($ext, $this->skip_extensions)) return;

		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$ua_lower   = strtolower($user_agent);
		$uri_lower  = strtolower($uri);

		// Deteksi bot
		$is_bot = 0;
		if (empty($user_agent)) {
			$is_bot = 1;
		} else {
			foreach ($this->known_bots as $bot) {
				if (strpos($ua_lower, $bot) !== false) {
					$is_bot = 1;
					break;
				}
			}
		}

		// Deteksi suspicious
		$is_suspicious = 0;
		foreach ($this->suspicious_paths as $path) {
			if (strpos($uri_lower, $path) !== false) {
				$is_suspicious = 1;
				break;
			}
		}

		// Ambil user_id dari session kalau ada
		$user_id = null;
		if ($this->CI->session->userdata('user')) {
			$user_id = $this->CI->session->userdata('user')['id'] ?? null;
		}

		// Ambil IP (support proxy)
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']
			?? $_SERVER['HTTP_X_REAL_IP']
			?? $_SERVER['REMOTE_ADDR']
			?? '0.0.0.0';
		// Ambil IP pertama kalau ada chain
		$ip = trim(explode(',', $ip)[0]);

		$this->CI->db->insert('visitor_logs', [
			'ip_address'    => $ip,
			'user_agent'    => $user_agent ?: null,
			'method'        => $_SERVER['REQUEST_METHOD'] ?? 'GET',
			'uri'           => '/' . ltrim($uri, '/'),
			'referer'       => $_SERVER['HTTP_REFERER'] ?? null,
			'user_id'       => $user_id,
			'is_bot'        => $is_bot,
			'is_suspicious' => $is_suspicious,
			'created_at'    => date('Y-m-d H:i:s'),
		]);
	}
}
