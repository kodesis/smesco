<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Delivery extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_delivery()
    {
        return $this->db->from('delivery b')->order_by('b.id', 'DESC')->get()->result();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch('booking', $data);
    }

    public function updateBooking($id, $data)
    {
        return $this->db->where('Id', $id)->update('booking', $data);
    }

    public function is_available($awb)
    {
        $this->queryId($awb);

        return $this->db->get('booking')->num_rows();
    }

    public function detailAwb($awb)
    {
        $this->queryId($awb);

        return $this->db->get('booking')->row_array();
    }

    private function queryId($awb)
    {
        $this->db->where('awb', $awb);
    }

    public function insert_batch_detail_awb($data)
    {
        return $this->db->insert_batch('awb_detail', $data);
    }

    public function delete_detail_awb($id)
    {
        return $this->db->where('awb_id', $id)->delete('awb_detail');
    }

    public function detailItemAwb($id)
    {
        return $this->db->where('awb_id', $id)->get('awb_detail')->result();
    }
}
