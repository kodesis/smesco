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
			$this->db->where('is_active', $status_filter);
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

	public function get_services()
	{
		return $this->db->select('id, code, name, description, is_active')->from('service_types')->where('is_active', 1)->order_by('name')->get()->result();
	}

	public function get_detail($id)
	{
		return $this->db->select('pricelist.*, users.name AS created_by_name, service_types.name AS service_name')->from('pricelist')->join('users', 'users.id = pricelist.created_by', 'left')->join('service_types', 'service_types.id = pricelist.service_type_id', 'left')->where('pricelist.id', $id)->get()->row();
	}

}
