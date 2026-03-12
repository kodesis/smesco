<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Setting extends CI_Model
{
    public function countUser($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('name', $keyword);
            $this->db->or_like('email', $keyword);
            $this->db->or_like('username', $keyword);
            $this->db->or_like('role', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('user u')->join('user_role ur', 'u.role_id = ur.Id', 'left')->count_all_results();
    }

    public function listUserPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('name', $keyword);
            $this->db->or_like('email', $keyword);
            $this->db->or_like('username', $keyword);
            $this->db->or_like('role', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('user u')->join('user_role ur', 'u.role_id = ur.Id', 'left')->order_by('name', 'ASC')->limit($limit, $from)->get()->result();
    }

    public function countMenu($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_menu', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('menu')->where('parent_id', NULL)->count_all_results();
    }

    public function listMenuPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_menu', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->from('menu')->where('parent_id', NULL)->order_by('nama_menu', 'ASC')->limit($limit, $from)->get()->result();
    }

    public function addMenu($invoice_data)
    {
        $this->db->insert('menu', $invoice_data);

        // Dapatkan ID invoice yang baru saja di-generate
        return $this->db->insert_id();
    }

    public function addChildMenu($invoice_data)
    {
        return $this->db->insert_batch('menu', $invoice_data);
    }

    public function isAvailableMenu($url)
    {
        return $this->db->where('url', $url)->get('menu')->num_rows();
    }

    public function get_menus()
    {
        $this->db->where('parent_id', NULL);
        $this->db->order_by('url', 'ASC');
        return $this->db->get('menu')->result();
    }

    public function get_submenus($id)
    {
        $this->db->where('parent_id', $id);
        $this->db->order_by('id', 'ASC');
        return $this->db->get('menu')->result();
    }

    public function getUserMenu($id)
    {
        return $this->db->select('access_menu, access_sub_menu')->where('username', $id)->get('user')->row_array();
    }

    public function update_user($data, $id)
    {
        return $this->db->where('username', $id)->update('user', $data);
    }

    public function get_menu_by_url($url)
    {
        return $this->db->get_where('menu', ['url' => $url])->row_array();
    }

    public function getProvinsi()
    {
        return $this->db->order_by('nama_provinsi', 'ASC')->get('mt_provinsi')->result();
    }

    public function getKota($id)
    {
        return $this->db->where('provinsi_id', $id)->order_by('nama_kota', 'ASC')->get('mt_kota')->result();
    }

    public function getKecamatan($id)
    {
        return $this->db->where('kota_id', $id)->order_by('nama_kecamatan', 'ASC')->get('mt_kecamatan')->result();
    }

    public function getKelurahan($id)
    {
        return $this->db->where('kecamatan_id', $id)->order_by('nama_kelurahan', 'ASC')->get('mt_kelurahan')->result();
    }

    public function getProvinsiById($id)
    {
        return $this->db->where('id', $id)->order_by('nama_provinsi', 'ASC')->get('mt_provinsi')->row_array();
    }

    public function getKotaById($id)
    {
        return $this->db->where('id', $id)->order_by('nama_kota', 'ASC')->get('mt_kota')->row_array();
    }

    public function getKecamatanById($id)
    {
        return $this->db->where('id', $id)->order_by('nama_kecamatan', 'ASC')->get('mt_kecamatan')->row_array();
    }

    public function getKelurahanById($id)
    {
        return $this->db->where('id', $id)->order_by('nama_kelurahan', 'ASC')->get('mt_kelurahan')->row_array();
    }

    public function createAccount($data)
    {
        return $this->db->insert('user', $data);
    }
}
