<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Public payment endpoints — extend MY_Controller LANGSUNG (bukan BaseController),
 * sama alasannya kayak Checkout.php: callback dipanggil server Duitku (no session),
 * success() dipanggil browser customer yang belum tentu ada session juga.
 */
class Payment extends MY_Controller
{
	/**
	 * POST /payment/callback
	 * Dipanggil server Duitku, BUKAN browser customer. Content-Type
	 * application/x-www-form-urlencoded, field beda dari Create Invoice —
	 * lihat docs.duitku.com bagian "Callback (Payment Notification)".
	 *
	 * Respon HARUS literal string "SUCCESS" (bukan JSON) kalau callback
	 * sudah "settled" di sisi kita — baik baru diproses ATAU sudah diproses
	 * duluan (idempotent no-op). Kalau tidak, Duitku anggap gagal dan retry
	 * sampai 5x lalu kirim email failure notice ke akun merchant.
	 */
	public function callback()
	{
		$merchantCode    = $this->input->post('merchantCode', TRUE);
		$amount          = $this->input->post('amount', TRUE);
		$merchantOrderId = $this->input->post('merchantOrderId', TRUE);
		$resultCode      = $this->input->post('resultCode', TRUE);
		$reference       = $this->input->post('reference', TRUE);
		$signature       = $this->input->post('signature', TRUE);

		if (empty($merchantCode) || $amount === null || empty($merchantOrderId) || $resultCode === null || empty($signature)) {
			log_message('error', 'Duitku callback: parameter tidak lengkap. Payload: ' . json_encode($this->input->post()));
			$this->output->set_status_header(200);
			echo 'Bad Parameter';
			return;
		}

		$apiKey = $this->appSettings['duitku_api_key'] ?? '';
		$expectedSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);

		if (!hash_equals($expectedSignature, (string) $signature)) {
			log_message('error', "Duitku callback: signature tidak valid untuk order {$merchantOrderId}.");
			$this->audit('payment', 'duitku_callback_bad_signature', null, [], [
				'order_number' => $merchantOrderId,
			], 'Callback Duitku dengan signature tidak valid — kemungkinan spoofing/payload dipalsukan.');
			$this->output->set_status_header(200);
			echo 'Bad Signature';
			return;
		}

		$this->load->model('M_Shipment');

		// merchantOrderId di sini = no_resi (bukan order_number Kopi Kargo)
		$result = ((string) $resultCode === '00')
			? $this->M_Shipment->confirmDuitkuPayment($merchantOrderId, $reference)
			: $this->M_Shipment->markDuitkuFailed($merchantOrderId, $reference);

		if ($result['outcome'] === 'not_found') {
			log_message('error', "Duitku callback: no_resi {$merchantOrderId} tidak ditemukan di sistem.");
			$this->output->set_status_header(200);
			echo 'Order Not Found';
			return;
		}

		if ($result['outcome'] === 'paid') {
			$this->audit('payment', 'duitku_payment_confirmed', $result['shipment_id'], [], [
				'no_resi'   => $merchantOrderId,
				'reference' => $reference,
			], 'Pembayaran Duitku dikonfirmasi via callback.');
		} elseif ($result['outcome'] === 'failed') {
			$this->audit('payment', 'duitku_payment_failed', $result['shipment_id'], [], [
				'no_resi' => $merchantOrderId,
			], 'Pembayaran Duitku gagal/dibatalkan via callback.');
		}
		// 'already_processed' -> idempotent no-op, sengaja tidak di-audit biar nggak spam log tiap Duitku retry.

		$this->output->set_status_header(200);
		echo 'SUCCESS';
	}

	/**
	 * GET /payment/success/{orderNumber}
	 * Redirect balik dari Duitku setelah CUSTOMER submit pembayaran di sisi mereka
	 * (browser customer, dari link WA — bukan browser admin).
	 * BUKAN bukti pembayaran valid — status asli SELALU dari callback/DB.
	 * TIDAK ADA write DB di method ini.
	 */
	public function success(string $orderNumber)
	{
		$this->load->model('M_Shipment');
		$shipment = $this->M_Shipment->get_by_no_resi($orderNumber);

		if (!$shipment) {
			show_404();
			return;
		}

		$shipment = (array) $shipment;
		// Halaman publik, tanpa auth — jangan expose nomor telepon pengirim/penerima
		unset($shipment['sender_phone'], $shipment['receiver_phone']);

		$data = [
			'pageTitle' => 'Status Pembayaran',
			'segment'   => 'checkout',
			'shipment'  => $shipment,
		];

		$data['pages'] = 'landing-page/pages/payment_pending';
		$this->load->view('landing-page/index', $data);
	}
}
