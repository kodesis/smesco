<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Tracking extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Shipment');
	}

	// ──────────────────────────────────────────
	// GET /api/v1/tracking/{no_resi}
	// ──────────────────────────────────────────
	public function get($no_resi = NULL)
	{
		if ($this->input->method() !== 'get') {
			return $this->_respond(['message' => 'Method not allowed.'], 405);
		}

		if (empty($no_resi)) {
			return $this->_respond([
				'status'  => false,
				'message' => 'Nomor resi wajib diisi.',
			], 422);
		}

		$no_resi = strtoupper(trim($no_resi));

		// ── 1. Ambil data shipment
		$shipment = $this->M_Shipment->getResi($no_resi);

		if (!$shipment) {
			return $this->_respond([
				'status'  => false,
				'message' => 'Resi ' . $no_resi . ' tidak ditemukan.',
			], 404);
		}

		// ── 2. Ambil riwayat tracking (sudah include masking note)
		$history = $this->M_Shipment->get_tracking_public($shipment['id']);

		// ── 3. Format history
		$tracking_history = array_map(function ($row) {
			return [
				'status'     => $row['status'],
				'note'       => $row['note'],
				'location'   => $row['location'] ?? '',
				'timestamp'  => $row['created_at'],
			];
		}, $history);

		$this->_log('tracking/get', 200, ['no_resi' => $no_resi]);

		// ── 4. Response
		return $this->_respond([
			'status' => true,
			'data'   => [
				'shipment' => [
					'no_resi'        => $shipment['no_resi'],
					'status'         => $shipment['status'],
					'service_name'   => $shipment['service_name'],
					'service_code'   => $shipment['service_code'],
					'origin'         => $shipment['origin'],
					'destination'    => $shipment['destination'],
					'sender_name'    => $shipment['sender_name'],
					'receiver_name'  => $shipment['receiver_name'],
					'koli'           => (int) $shipment['koli'],
					'actual_weight'  => (float) $shipment['actual_weight'],
					'chargeable_weight' => (float) $shipment['chargeable_weight'],
					'booked_at'      => $shipment['created_at'],
				],
				'tracking' => $tracking_history,
			],
		]);
	}
}
