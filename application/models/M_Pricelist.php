<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Pricelist extends MY_Model
{
	public function count_paged($search = NULL, $status_filter = NULL)
	{
		$this->db->from('pricelist');

		if ($status_filter !== '' && $status_filter !== NULL) {
			$this->db->where('is_active', $status_filter);
		}

		if ($search) {
			$this->db->group_start()
				->like('origin', $search)
				->or_like('destination', $search)
				->or_like('price_per_kg', $search)
				->group_end();
		}

		return $this->db->count_all_results();
	}

	public function get_paged_pricelist($limit, $offset, $search = NULL, $status_filter = NULL)
	{
		$this->db->from('pricelist');
		$this->db->join('users', 'users.id = pricelist.created_by', 'left')
			->select('pricelist.*, users.name AS created_by_name');
			$this->db->join('service_types', 'service_types.id = pricelist.service_type_id', 'left')
			->select('service_types.name AS service_name, service_types.code AS service_code');

		if ($status_filter !== '' && $status_filter !== NULL) {
			$this->db->where('pricelist.is_active', $status_filter);
		}

		if ($search) {
			$this->db->group_start()
				->like('origin', $search)
				->or_like('destination', $search)
				->or_like('price_per_kg', $search)
				->group_end();
		}

		$this->db->order_by('created_at', 'DESC')
			->limit($limit, $offset);

		return $this->db->get()->result();
	}

	public function get_cities()
	{
		return $this->db->select('id, code, name, is_active')->from('cities')->where('is_active', 1)->order_by('name')->get()->result();
	}

	public function get_services($category = NULL)
	{
		$this->db->select('id, code, name, description, is_active')->from('service_types')->where('is_active', 1);

		if ($category) {
			$this->db->where('category', $category);
		}
		
		return $this->db->order_by('name')->get()->result();
	}

	public function get_detail($id)
	{
		return $this->db->select('pricelist.*, users.name AS created_by_name, service_types.name AS service_name')->from('pricelist')->join('users', 'users.id = pricelist.created_by', 'left')->join('service_types', 'service_types.id = pricelist.service_type_id', 'left')->where('pricelist.id', $id)->get()->row();
	}

	public function get_all_pricelist_for_export($search = null, $status = null)
	{
		$this->db->select('
        p.*, 
        st.name as service_type_name, 
        v.vendor_name as vendor_name,
        pt.min_weight as tier_min_weight,
        pt.max_weight as tier_max_weight,
        pt.price_kribo as tier_price_kribo,
        pt.price_smesco as tier_price_smesco
    ');
		$this->db->from('pricelist p');
		$this->db->join('service_types st', 'st.id = p.service_type_id', 'left');
		$this->db->join('vendors v', 'v.id = p.vendor_id', 'left');
		// LEFT JOIN ke tiers agar data tiered ikut terambil
		$this->db->join('pricelist_tiers pt', 'pt.pricelist_id = p.id', 'left');

		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('p.origin', $search);
			$this->db->or_like('p.destination', $search);
			$this->db->group_end();
		}

		if ($status !== null && $status !== '') {
			$this->db->where('p.is_active', $status);
		}

		$this->db->order_by('p.id', 'ASC');
		$this->db->order_by('pt.min_weight', 'ASC');

		return $this->db->get()->result();
	}
}
