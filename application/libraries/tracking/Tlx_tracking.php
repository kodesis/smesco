<?php
class Tlx_tracking implements Tracking_contract
{
	private $code_map = [
		'SPU' => 'PICKED_UP',
		'STP' => 'RECEIVED_ORIGIN',
		'SOC' => 'RECEIVED_ORIGIN',
		'SDO' => 'MANIFESTED',
		'SAD' => 'ARRIVED',
		'DCC' => 'RECEIVED_DESTINATION',
		'DCP' => 'RECEIVED_DESTINATION',
		'ITR' => 'IN_TRANSIT',
		'WDC' => 'IN_TRANSIT',
		'SFD' => 'DELIVERED',
	];

	// tambahin di atas fetch(), di class
	private $details_map = [
		'ARRIVED AT DESTINATION'   => 'ARRIVED',
		'DEPARTED TO DESTINATION'  => 'IN_TRANSIT',
		'has been processed'       => 'MANIFESTED',
		'has been arrived'         => 'RECEIVED_ORIGIN',
		'Delivery failed'          => 'DELIVERY_FAILED',
		'not in touch'             => 'DELIVERY_FAILED',
	];

	public function fetch(string $connote): array
	{
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL            => 'https://admin.tlx.co.id/api/shipments/tracking-status?connote=' . urlencode($connote),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 5,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'GET',
		]);

		$response  = curl_exec($curl);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($curl_error || !$response) return [];

		$decoded = json_decode($response, true);
		if (empty($decoded['data'][0]['tracking_status'])) return [];

		$result = [];
		foreach ($decoded['data'][0]['tracking_status'] as $item) {
			$status = $this->resolve_status($item);
			if ($status === null) {
				log_message('error', 'TLX unmapped: ' . json_encode($item));
				continue;
			}

			$note = $item['status'] ?? '';
			if (!empty($item['details'])) {
				$note .= ($note ? ' — ' : '') . $item['details'];
			}

			$result[] = [
				'status'     => $status,
				'note'       => $note,
				'location'   => '',
				'created_at' => date('Y-m-d H:i:s', $item['date_time']),
			];
		}

		return $result;
	}

	private function resolve_status(array $item): ?string
	{
		if (!empty($item['code']) && isset($this->code_map[$item['code']])) {
			return $this->code_map[$item['code']];
		}
		$details = $item['details'] ?? '';
		foreach ($this->details_map as $needle => $status) {
			if (stripos($details, $needle) !== false) return $status;
		}
		return null;
	}
}
