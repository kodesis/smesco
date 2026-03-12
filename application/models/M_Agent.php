<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_agent extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_agent()
    {
        return $this->db->order_by('nama_agent', 'ASC')->get('agent')->result();
    }

    public function insert($data)
    {
        return $this->db->insert('agent', $data);
    }

    public function insertGetId($invoice_data)
    {
        $this->db->insert('agent', $invoice_data);

        // Dapatkan ID invoice yang baru saja di-generate
        return $this->db->insert_id();
    }

    public function update($data, $old_slug)
    {
        $this->db->where('slug', $old_slug);
        return $this->db->update('agent', $data);
    }

    public function show($id)
    {
        return $this->db->where('slug', $id)->get('agent')->row_array();
    }

    public function showById($id)
    {
        return $this->db->where('id', $id)->get('agent')->row_array();
    }

    public function is_available($id)
    {
        return $this->db->where('slug', $id)->get('agent')->num_rows();
    }

    public function count($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_agent', $keyword);
            $this->db->or_like('alamat_agent', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('agent')->count_all_results();
    }

    public function listCustomerPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_agent', $keyword);
            $this->db->or_like('alamat_agent', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('agent')->order_by('nama_agent', 'ASC')->limit($limit, $from)->get()->result();
    }

    public function getAgentById($id)
    {
        return $this->db->where('id', $id)->get('agent')->row_array();
    }

    public function listAgentByDestination($destination)
    {
        return $this->db->where('origin', $destination)->get('agent')->result();
    }

    public function insertMitra($data)
    {
        return $this->db->insert('mitra', $data);
    }
}
