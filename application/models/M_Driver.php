<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Driver extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_driver()
    {
        return $this->db->order_by('origin', 'ASC')->get('mt_driver')->result();
    }

    public function insert($data)
    {
        return $this->db->insert('mt_driver', $data);
    }

    public function insertGetId($invoice_data)
    {
        $this->db->insert('mt_driver', $invoice_data);

        // Dapatkan ID invoice yang baru saja di-generate
        return $this->db->insert_id();
    }

    public function update($data, $old_slug)
    {
        $this->db->where('slug', $old_slug);
        return $this->db->update('mt_driver', $data);
    }

    public function show($id)
    {
        return $this->db->where('slug', $id)->get('mt_driver')->row_array();
    }

    public function showById($id)
    {
        return $this->db->where('id', $id)->get('mt_driver')->row_array();
    }

    public function is_available($id)
    {
        return $this->db->where('slug', $id)->get('mt_driver')->num_rows();
    }

    public function count($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('name', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('user')->where('role_id', '4')->count_all_results();
    }

    public function listDriverPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('name', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('user')->where('role_id', '4')->order_by('name', 'ASC')->limit($limit, $from)->get()->result();
    }
}
