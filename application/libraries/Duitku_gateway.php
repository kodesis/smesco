<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Duitku_gateway
{
	protected $CI;
	private string $merchantCode;
	private string $apiKey;
	private bool $isSandbox;
	private string $duitkuMode; // 'pop' atau 'api'

	public function __construct()
	{
		$this->CI = &get_instance();

		// Satu sumber config buat semua: tabel `setting`, di-load sebagai $this->appSettings
		// di MY_Controller/BaseController. Dipakai konsisten juga di Shipment.php dan
		// Payment.php::callback() — JANGAN pisah sumber lagi, biar apiKey buat create invoice
		// dan apiKey buat verify signature callback selalu sama.
		$this->merchantCode = $this->CI->appSettings['duitku_merchant_code'] ?? 'DSxxxx';
		$this->apiKey       = $this->CI->appSettings['duitku_api_key'] ?? 'xxxxxx';
		$this->isSandbox    = ($this->CI->appSettings['duitku_env'] ?? 'sandbox') === 'sandbox';
		$this->duitkuMode   = $this->CI->appSettings['duitku_payment_mode'] ?? 'pop';
	}

	/**
	 * Ambil durasi expiry (menit) dari setting, dipakai buat expiryPeriod invoice
	 * DAN buat payment_expired_at di sisi caller — biar dua-duanya selalu sama,
	 * gak ada gap antara "invoice masih valid di Duitku" vs "shipment kadung expired di kita".
	 */
	public function getExpiryMinutes(): int
	{
		return (int) ($this->CI->appSettings['duitku_expiry_minutes'] ?? 30);
	}

	/**
	 * Membuat Invoice Transaksi Duitku
	 *
	 * @param array $orderData   total_amount, order_number, customer_name, customer_email, customer_wa
	 * @param array $itemDetails Array item SUDAH DALAM BENTUK FINAL Duitku: [['name'=>string,'price'=>int,'quantity'=>int], ...]
	 *                           (BUKAN raw cart item — kalau caller punya bentuk lain kayak cart Kopi Kargo,
	 *                           transform ke bentuk ini SEBELUM manggil method ini, bukan di dalam sini)
	 */
	public function createInvoice(array $orderData, array $itemDetails): array
	{
		$duitkuConfig = new \Duitku\Config($this->apiKey, $this->merchantCode);
		$duitkuConfig->setSandboxMode($this->isSandbox);
		$duitkuConfig->setSanitizedMode(false);
		$duitkuConfig->setDuitkuLogs(false);

		$paymentAmount = (int) round($orderData['total_amount']);
		$cleanPhone    = preg_replace('/[^0-9]/', '', $orderData['customer_wa']);

		$itemDetailsSum = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $itemDetails));
		if ($itemDetailsSum !== $paymentAmount) {
			log_message('error', "DUITKU createInvoice: itemDetails sum ({$itemDetailsSum}) != paymentAmount ({$paymentAmount}) untuk order {$orderData['order_number']} — Duitku kemungkinan bakal reject/invalid transaction.");
		}

		$params = [
			'paymentAmount'   => $paymentAmount,
			'merchantOrderId' => $orderData['order_number'],
			'productDetails'  => $orderData['product_details'] ?? ('Pesanan #' . $orderData['order_number']),
			'customerVaName'  => substr($orderData['customer_name'], 0, 30),
			'email'           => !empty($orderData['customer_email']) ? $orderData['customer_email'] : ($this->CI->appSettings['duitku_fallback_email'] ?? 'noreply@smescoexpress.id'),
			'phoneNumber'     => $cleanPhone,
			'itemDetails'     => $itemDetails,
			'callbackUrl'     => base_url('payment/callback'),
			'returnUrl'       => base_url('payment/success/' . $orderData['order_number']),
			'expiryPeriod'    => $this->getExpiryMinutes()
		];

		log_message('debug', 'DUITKU createInvoice paymentAmount: ' . $paymentAmount . ' | itemDetails sum: ' . $itemDetailsSum);

		try {
			if ($this->duitkuMode === 'api') {
				$params['paymentMethod'] = $this->CI->appSettings['duitku_default_method'] ?? 'VC';
				$responseDuitku = \Duitku\Api::createInvoice($params, $duitkuConfig);
			} else {
				$params['paymentMethod'] = '';
				$responseDuitku = \Duitku\Pop::createInvoice($params, $duitkuConfig);
			}

			$result = json_decode($responseDuitku, true);

			if (isset($result['paymentUrl'])) {
				return [
					'status'     => true,
					'paymentUrl' => $result['paymentUrl'],
					'reference'  => $result['reference'] ?? null
				];
			}

			$errorMessage = $result['statusMessage'] ?? 'Gagal membuat Invoice Duitku';
			log_message('error', 'Duitku API responded with error: ' . $errorMessage . ' | Raw response: ' . $responseDuitku);

			return [
				'status'  => false,
				'message' => $errorMessage
			];
		} catch (Exception $e) {
			log_message('error', 'Duitku SDK Error: ' . $e->getMessage());
			return [
				'status'  => false,
				'message' => 'Duitku Exception: ' . $e->getMessage()
			];
		}
	}
}
