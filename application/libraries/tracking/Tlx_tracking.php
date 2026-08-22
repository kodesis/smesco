<?php
// application/libraries/tracking/Tlx_tracking.php
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
			if (empty($item['code']) || !isset($this->code_map[$item['code']])) continue;

			$note = $item['status'] ?? '';
			if (!empty($item['details'])) {
				$note .= ($note ? ' — ' : '') . $item['details'];
			}

			$result[] = [
				'status'     => $this->code_map[$item['code']],
				'note'       => $note,
				'location'   => '',
				'created_at' => date('Y-m-d H:i:s', $item['date_time']),
			];
		}

		return $result;
	}
}
