<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_AgentKecamatan extends CI_Model
{
    public function get_pricelist($destination, $service_id)
    {
        return $this->db
            ->where('origin', 'JAKARTA')
            ->where('destination', $destination)
            ->where('service_type_id', $service_id)
            ->where('category', 'INTERNATIONAL')
            ->where('is_active', 1)
            ->get('pricelist')
            ->row();
    }

    public function get_price_tier($pricelist_id, $chargeable)
    {
        return $this->db
            ->where('pricelist_id', $pricelist_id)
            ->where('min_weight <=', $chargeable)
            ->where('max_weight >=', $chargeable)
            ->order_by('min_weight', 'ASC')
            ->get('pricelist_tiers')
            ->row();
    }

    public function get_shipments($agent_id, $filters = [])
    {
        $this->db
            ->select('
                shipments.*,
                service_types.name AS service_name,
                service_types.code AS service_code
            ')
            ->from('shipments')
            ->join(
                'service_types',
                'service_types.id = shipments.service_type_id',
                'left'
            )
            ->where('shipments.agent_id', $agent_id)
            ->where('shipments.category', 'INTERNATIONAL');

        if (!empty($filters['q'])) {
            $q = $filters['q'];

            $this->db->group_start();
            $this->db->like('shipments.no_resi', $q);
            $this->db->or_like('shipments.sender_name', $q);
            $this->db->or_like('shipments.receiver_name', $q);
            $this->db->or_like('shipments.destination', $q);
            $this->db->group_end();
        }

        if (!empty($filters['status'])) {
            $this->db->where('shipments.status', $filters['status']);
        }

        $this->db->order_by('shipments.id', 'DESC');

        return $this->db->get()->result();
    }

    public function get_by_id($id, $agent_id)
    {
        return $this->db
            ->select('
                shipments.*,
                service_types.name AS service_name,
                service_types.code AS service_code,
                master_commodities.name AS commodity_name
            ')
            ->from('shipments')
            ->join(
                'service_types',
                'service_types.id = shipments.service_type_id',
                'left'
            )
            ->join(
                'master_commodities',
                'master_commodities.id = shipments.commodity_id',
                'left'
            )
            ->where('shipments.id', $id)
            ->where('shipments.agent_id', $agent_id)
            ->get()
            ->row_array();
    }
}
