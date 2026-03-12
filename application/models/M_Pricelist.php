<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Pricelist extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_pricelist()
    {
        return $this->db->order_by('origin', 'ASC')->get('mt_pricelist')->result();
    }

    public function insert($data)
    {
        return $this->db->insert('mt_pricelist', $data);
    }

    public function insertGetId($invoice_data)
    {
        $this->db->insert('mt_pricelist', $invoice_data);

        // Dapatkan ID invoice yang baru saja di-generate
        return $this->db->insert_id();
    }

    public function update($data, $old_slug)
    {
        $this->db->where('slug', $old_slug);
        return $this->db->update('mt_pricelist', $data);
    }

    public function show($id)
    {
        return $this->db->where('slug', $id)->get('mt_pricelist')->row_array();
    }

    public function showById($id)
    {
        return $this->db->where('id', $id)->get('mt_pricelist')->row_array();
    }

    public function is_available($id)
    {
        return $this->db->where('slug', $id)->get('mt_pricelist')->num_rows();
    }

    public function count($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('origin', $keyword);
            $this->db->or_like('destination', $keyword);
            $this->db->or_like('city', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('mt_pricelist')->count_all_results();
    }

    public function listPricelistPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('origin', $keyword);
            $this->db->or_like('destination', $keyword);
            $this->db->or_like('city', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('mt_pricelist')->order_by('city', 'ASC')->order_by('min_chargeable', 'ASC')->limit($limit, $from)->get()->result();
    }
}
