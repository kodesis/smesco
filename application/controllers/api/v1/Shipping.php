<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Shipping extends API_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Shipping_Api');
	}

	// ──────────────────────────────────────────
	// POST /api/v1/shipping/calculate
	// ──────────────────────────────────────────
	public function calculate()
	{
		if ($this->input->method() !== 'post') {
			return $this->_respond(['message' => 'Method not allowed.'], 405);
		}

		// ── 1. Ambil & sanitasi input
		$origin        = strtoupper(trim($this->input->post('origin', TRUE) ?? ''));
		$destination   = strtoupper(trim($this->input->post('destination', TRUE) ?? ''));
		$actual_weight = floatval($this->input->post('actual_weight') ?: 0);
		$items         = $this->input->post('items');           // array, opsional
		$pickup_area = ($this->input->post('pickup_area') ?: '') ? strtoupper(trim($this->input->post('pickup_area', TRUE))) : null; // opsional

		// ── 2. Validasi input wajib
		$errors = [];
		if (!$origin)           $errors[] = 'origin wajib diisi.';
		if (!$destination)      $errors[] = 'destination wajib diisi.';
		if ($actual_weight <= 0) $errors[] = 'actual_weight harus lebih dari 0.';

		if (!empty($errors)) {
			return $this->_respond([
				'status'  => false,
				'message' => 'Validasi gagal.',
				'errors'  => $errors,
			], 422);
		}

		// ── 3. Hitung volume weight (opsional)
		$volume_kg = $this->_calc_volume_weight($items);

		// ── 4. Ambil pricelist
		$pricelist = $this->M_Shipping_Api->get_pricelist($origin, $destination);

		if (!$pricelist) {
			return $this->_respond([
				'status'  => false,
				'message' => 'Rute ' . $origin . ' → ' . $destination . ' tidak ditemukan atau tidak aktif.',
			], 404);
		}

		// ── 5. Hitung chargeable weight
		$min_weight    = floatval($pricelist->min_weight_kg);
		$chargeable_raw = max($actual_weight, $volume_kg);

		// Terapkan minimum weight dari pricelist
		if ($chargeable_raw > 0 && $chargeable_raw < $min_weight) {
			$chargeable_raw = $min_weight;
		}

		$chargeable_kg = (int) ceil($chargeable_raw);

		// ── 6. Tentukan harga per kg (flat atau tiered)
		$price_per_kg = floatval($pricelist->price_smesco);

		if ($pricelist->is_tiered == 1) {
			$tier = $this->M_Shipping_Api->get_tier($pricelist->id, $chargeable_kg);
			if ($tier) {
				$price_per_kg = floatval($tier->price_smesco);
			}
		}

		$shipping_cost = $chargeable_kg * $price_per_kg;

		// ── 7. Pickup (opsional)
		$pickup_data = null;
		$pickup_cost = 0;

		if ($pickup_area) {
			$pickup = $this->M_Shipping_Api->get_pickup_rate($pickup_area);

			if (!$pickup) {
				return $this->_respond([
					'status'  => false,
					'message' => 'Area pickup tidak ditemukan atau tidak aktif.',
				], 404);
			}

			$min_pickup_weight = floatval($pickup->min_weight);

			if ($chargeable_kg < $min_pickup_weight) {
				return $this->_respond([
					'status'  => false,
					'message' => "Layanan pickup memerlukan minimal {$min_pickup_weight} Kg. Chargeable weight saat ini {$chargeable_kg} Kg.",
				], 422);
			}

			$pickup_cost = floatval($pickup->price_smesco);
			$pickup_data = [
				// 'area_id'    => (int) $pickup->id,
				'area_name'  => $pickup->area_name,
				'min_weight' => $min_pickup_weight,
				'cost'       => $pickup_cost,
			];
		}

		// ── 8. Grand total
		$grand_total = $shipping_cost + $pickup_cost;

		$this->_log('shipping/calculate', 200, compact('origin', 'destination', 'actual_weight', 'chargeable_kg'));

		// ── 9. Response
		return $this->_respond([
			'status' => true,
			'data'   => [
				'route' => [
					'origin'      => $origin,
					'destination' => $destination,
					'service'     => $pricelist->category,
					'is_tiered'   => (bool) $pricelist->is_tiered,
				],
				'weight' => [
					'actual_kg'     => $actual_weight,
					'volume_kg'     => round($volume_kg, 2),
					'chargeable_kg' => $chargeable_kg,
					'min_weight_kg' => $min_weight,
				],
				'pricing' => [
					'price_per_kg'  => $price_per_kg,
					'shipping_cost' => $shipping_cost,
					'pickup'        => $pickup_data,
					'grand_total'   => $grand_total,
				],
			],
		]);
	}

	// ──────────────────────────────────────────
	// Helper: hitung volume weight dari array items
	// Divisor: 5000 (standar air cargo)
	// ──────────────────────────────────────────
	private function _calc_volume_weight($items)
	{
		if (empty($items) || !is_array($items)) return 0;

		$total = 0;
		foreach ($items as $item) {
			$qty    = intval($item['qty']    ?? 1);
			$length = floatval($item['length'] ?? 0);
			$width  = floatval($item['width']  ?? 0);
			$height = floatval($item['height'] ?? 0);

			if ($qty > 0 && $length > 0 && $width > 0 && $height > 0) {
				$total += (($length * $width * $height) / 5000) * $qty;
			}
		}

		return $total;
	}
}
