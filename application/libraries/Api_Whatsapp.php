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
}
