<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Shipping_Api extends CI_Model
{
	/**
	 * Ambil pricelist aktif berdasarkan rute.
	 */
	public function get_pricelist($origin, $destination)
	{
		return $this->db
			->where('origin', $origin)
			->where('destination', $destination)
			->where('is_active', 1)
			->get('pricelist')
			->row();
	}

	/**
	 * Ambil tier harga berdasarkan berat chargeable.
	 * Fallback ke tier tertinggi kalau berat melampaui semua range.
	 */
	public function get_tier($pricelist_id, $weight)
	{
		$tier = $this->db
			->where('pricelist_id', $pricelist_id)
			->where('min_weight <=', $weight)
			->where('max_weight >=', $weight)
			->get('pricelist_tiers')
			->row();

		if ($tier) return $tier;

		// Fallback: ambil tier dengan max_weight tertinggi
		return $this->db
			->where('pricelist_id', $pricelist_id)
			->order_by('max_weight', 'DESC')
			->get('pricelist_tiers', 1)
			->row();
	}

	/**
	 * Ambil data pickup rate berdasarkan ID.
	 */
	public function get_pickup_rate($id)
	{
		return $this->db
			->where('area_name', $id)
			->where('is_active', 1)
			->get('master_pickup_rates')
			->row();
	}

	/**
	 * Ambil semua pickup rate aktif (untuk keperluan list di dokumentasi / frontend).
	 */
	public function get_all_pickup_rates()
	{
		return $this->db
			->where('is_active', 1)
			->order_by('area_name', 'ASC')
			->get('master_pickup_rates')
			->result();
	}
}
