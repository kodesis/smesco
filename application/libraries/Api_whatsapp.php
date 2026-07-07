<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once('PHPExcel.php');

class Api_whatsapp
{
  function wa_notif($msgg, $phonee)
  {
    // $sender = 'buskipm';
    $phone = $phonee;
    $msg = $msgg;

    // $token = "whvSyvdbM5CDv1wKvXhQmajGAbTgJfyxfV5xuX1g7hpuMktyj4kllsalda";
    $token = "whvSyvdbM5CDv1wKvXhQmajGAbTgJfyxfV5xuX1g7hpuMktyj4";
    // $phone= "62812xxxxxx"; //untuk group pakai groupid contoh: 62812xxxxxx-xxxxx
    // $message = "Testing by API ruangwa";

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.ruangwa.id/api/send_message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'token=' . $token . '&number=' . $phone . '&message=' . $msgg,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

	function wa_notif_v2($phone_number, $message)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.fastwa.com/api/v1/C9E3ED48A460F61F60384815FB4C0B83/send_text',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 10, // Timeout 10 detik aja
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array(
				'api_key' => '53C92CE6A40AC365CD9D1FF128EB1B8E',
				'phone' => $phone_number,
				'message' => $message
			),
		));

		$response = curl_exec($curl);
		$error = curl_error($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		// Cek error
		if ($error) {
			log_message('error', 'CURL Error: ' . $error);
			throw new Exception('WhatsApp API Error: ' . $error);
		}

		if ($http_code >= 400) {
			log_message('error', 'WA API HTTP Error: ' . $http_code . ' - Response: ' . $response);
			throw new Exception('WhatsApp API returned error code: ' . $http_code);
		}

		return $response;
	}

	function wa_notif_v3($msgg, $phone)
	{
		// jika mengandung @g.us berarti group, tidak perlu format ulang
		if (strpos($phone, '@g.us') !== false) {
			$payload = array(
				'group_id' => $phone,
				'message'  => $msgg,
			);
		} else {
			// format nomor pribadi: ganti awalan 0 dengan 62
			$number = preg_replace('/\D/', '', $phone);
			if (substr($number, 0, 1) === '0') {
				$number = '62' . substr($number, 1);
			}
			$payload = array(
				'number'  => $number,
				'message' => $msgg,
			);
		}

		$headers = array(
			'x-api-key: bariskodeindonesia123456!@@',
			'Content-Type: application/json',
			'Accept: application/json',
		);

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL            => 'http://103.27.206.233:3000/send-message',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_POSTFIELDS     => json_encode($payload),
		));

		$response   = curl_exec($curl);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($curl_error) {
			return array('success' => false, 'message' => 'Gagal: ' . $curl_error);
		}

		$result = json_decode($response, true);
		return $result ?: array('success' => false, 'message' => 'Response tidak valid');
	}
}
