<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Auth extends CI_Model
{

    public function registration($data)
    {
        $this->db->insert('user', $data);
        $this->session->set_flashdata('success', 'You have successfully registered. Please login!');
        redirect('auth');
    }

    public function cek_user($id)
    {
        if (empty($id)) {
            redirect('auth');
        }
        return $this->db->get_where('user', ['username' => $id])->row_array();
    }

    public function cek_role($id)
    {
        return $this->db->get_where('user_role', ['id' => $id])->row_array();
    }

    public function cek_user_id($id)
    {
        if (empty($id)) {
            redirect('auth');
        }
        return $this->db->get_where('user', ['Id' => $id])->row_array();
    }

    public function update_user($data, $id)
    {
        $this->db->where('Id', $id);
        $this->db->update('user', $data);
        return $this->db->affected_rows();
    }

    public function role($id)
    {
        return $this->db->get_where('user_role', ['Id' => $id])->row_array();
    }

    public function users_list()
    {
        return $this->db->get('user')->result();
    }

    public function add_member($data)
    {
        $this->db->insert('user', $data);
        $this->session->set_flashdata('message_name', 'Member successfully added');
        redirect('dashboard/user');
    }

    public function count($keyword)
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

    public function getUserById($id)
    {
        return $this->db->where('Id', $id)->get('user')->row_array();
    }
}
