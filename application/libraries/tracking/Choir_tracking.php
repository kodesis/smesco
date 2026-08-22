<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Choir_tracking implements Tracking_contract
{
	private $api_url     = 'https://office.choirexpress.co.id/v2/api/get_tracking';
	private $client_name = 'CA75731';
	private $username    = 'CA75731';
	private $token       = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IkNBNzU3MzEiLCJjbGllbnRfbmFtZSI6IkNBNzU3MzEiLCJyYW5kb20iOiJDWFg0OTUwIn0.zCivGfCrHpU74RQUd64KymJNcVXNeSc4iN8aji0lQBU';

	// Mapping keyword dari Choir Express ke Status Internal
	private $status_map = [
		'order created'                      => 'BOOKED',
		'shipment has been processed'        => 'RECEIVED_ORIGIN',
		'departed to destination'            => 'MANIFESTED',
		'arrived at destination'             => 'ARRIVED',
		'presented to import customs'        => 'IN_TRANSIT',
		'released from import customs'       => 'IN_TRANSIT',
		'departed from destination import'   => 'IN_TRANSIT',
		'arrived at sorting center'          => 'IN_TRANSIT',
		'arrived at post office'             => 'IN_TRANSIT',
		'out for delivery'                   => 'IN_TRANSIT',
		'delivered'                          => 'DELIVERED',
	];

	public function fetch(string $connote): array
	{
		$payload = json_encode([
			'awb'         => trim($connote),
			'client_name' => $this->client_name,
			'username'    => $this->username,
		]);

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL            => $this->api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_HTTPHEADER     => [
				'Accept: application/json',
				'Authorization: ' . $this->token,
				'Content-Type: application/json',
			],
		]);

		$response  = curl_exec($curl);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($curl_error || !$response) return [];

		$decoded = json_decode($response, true);
		if (empty($decoded['status']) || $decoded['status'] != 200 || empty($decoded['trackings'])) {
			return [];
		}

		$result = [];
		foreach ($decoded['trackings'] as $item) {
			$raw_date = trim($item['date'] ?? '');
			$raw_time = trim($item['time'] ?? '');

			// Bypass / Filter tanggal bug 1970-01-01
			if (empty($raw_date) || $raw_date === '1970-01-01') {
				continue;
			}

			// Convert Date 12-hour AM/PM ke Format MySQL datetime (Y-m-d H:i:s)
			$dt_string = $raw_date . ' ' . $raw_time;
			$dt_object = DateTime::createFromFormat('Y-m-d h:i:s A', $dt_string);

			if (!$dt_object) {
				// Fallback jika format detik tidak dikirim
				$dt_object = DateTime::createFromFormat('Y-m-d h:i A', $dt_string);
			}

			$created_at = $dt_object ? $dt_object->format('Y-m-d H:i:s') : null;
			if (!$created_at) continue;

			// Map status
			$raw_status  = trim($item['status'] ?? '');
			$mapped_code = $this->resolve_status($raw_status);

			if ($mapped_code === 'BOOKED') {
				continue;
			}

			// Gabungkan note
			$note = $raw_status;
			if (!empty($item['notes'])) {
				$note .= ($note ? ' — ' : '') . $item['notes'];
			}

			$result[] = [
				'status'     => $mapped_code,
				'note'       => $note,
				'location'   => $decoded['destination'] ?? '',
				'created_at' => $created_at,
			];
		}

		return $result;
	}

	private function resolve_status(string $raw_status): string
	{
		$clean_status = strtr(strtolower($raw_status), ['.' => '']);

		foreach ($this->status_map as $keyword => $internal_status) {
			if (strpos($clean_status, $keyword) !== false) {
				return $internal_status;
			}
		}

		return 'IN_TRANSIT'; // Default status jika keyword tidak cocok
	}
}
