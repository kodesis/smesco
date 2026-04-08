<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Report extends CI_Model 
{
	public function get_shipment_report($filters = [])
	{
		$this->db->select('
        shipments.*, 
        agents.name as agent_name, 
        service_types.code as service_code,
        (shipments.sell_price - shipments.cost_price) as margin_per_kg
    ');
		$this->db->from('shipments');
		$this->db->join('agents', 'agents.id = shipments.agent_id', 'left');
		$this->db->join('service_types', 'service_types.id = shipments.service_type_id', 'left');

		// Filter Tanggal (Wajib)
		if (!empty($filters['start'])) {
			$this->db->where('DATE(shipments.created_at) >=', $filters['start']);
		}
		if (!empty($filters['end'])) {
			$this->db->where('DATE(shipments.created_at) <=', $filters['end']);
		}

		// Filter Tambahan
		if (!empty($filters['agent_id'])) {
			$this->db->where('shipments.agent_id', $filters['agent_id']);
		}
		if (!empty($filters['status'])) {
			$this->db->where('shipments.status', $filters['status']);
		}

		$this->db->order_by('shipments.created_at', 'ASC');
		return $this->db->get()->result();
	}
}
