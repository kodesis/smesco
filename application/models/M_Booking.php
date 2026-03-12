<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Booking extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function countBooking($keyword)
    {
        $role_id = $this->session->userdata('role_id');

        if ($role_id == '3') {
            $this->db->where('partner_id', $role_id);
        }

        if ($keyword) {
            $this->db->like('b.no_resi', $keyword);
            $this->db->or_like('b.origin', $keyword);
            $this->db->or_like('b.destination', $keyword);
            // $this->db->or_like('nama_customer', $keyword);
        }

        return $this->db->from('resi b')->count_all_results();
    }

    public function listBookingPaginate($limit, $from, $keyword)
    {
        $role_id = $this->session->userdata('role_id');
        $username = $this->session->userdata('username');

        if ($role_id == '3') {
            $this->db->where('partner_id', $this->session->userdata('partner_id'));
        }

        if ($keyword) {
            $this->db->like('b.no_resi', $keyword);
            $this->db->or_like('b.origin', $keyword);
            $this->db->or_like('b.destination', $keyword);
        }

        $this->db->from('resi b');
        if ($role_id != '3' or $username == "krx0005") {
            $this->db->join('partner p', 'b.partner_id = p.id');
        }
        return $this->db->order_by('b.no_resi', 'DESC')->limit($limit, $from)->get()->result();
    }

    public function list_booking()
    {
        return $this->db->from('booking b')->join('customer c', 'b.customer_id = c.id', 'left')->order_by('b.id', 'DESC')->get()->result();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch('booking', $data);
    }

    public function insert_dimensi($data)
    {
        return $this->db->insert_batch('dimensi', $data);
    }

    public function updateBooking($id, $data)
    {
        return $this->db->where('Id', $id)->update('booking', $data);
    }

    public function updateAwb($id, $data)
    {
        return $this->db->where('awb', $id)->update('booking', $data);
    }

    public function is_available($no_booking)
    {
        $this->queryBooking($no_booking);

        return $this->db->get('booking')->num_rows();
    }

    public function detailBookingByAwb($awb)
    {
        $this->db->where('awb', $awb);

        return $this->db->get('booking')->row_array();
    }

    public function detailBooking($no_booking)
    {
        $this->db->from('booking b')->join('customer c', 'b.customer_id = c.id', 'left');
        $this->queryBooking($no_booking);

        return $this->db->get()->row_array();
    }

    private function queryBooking($no_booking)
    {
        $this->db->where('no_booking', $no_booking);
    }

    public function insert_batch_detail_awb($data)
    {
        return $this->db->insert_batch('awb_detail', $data);
    }

    public function delete_detail_awb($id)
    {
        return $this->db->where('booking_id', $id)->delete('awb_detail');
    }

    public function detailItemBooking($id)
    {
        return $this->db->where('booking_id', $id)->get('awb_detail')->result();
    }

    public function list_detail_awb()
    {
        // return $this->db->order_by('slug', 'ASC')->get('awb_detail')->result();

        return $this->db->from('awb_detail ad')->join('booking b', 'ad.booking_id = b.Id', 'left')->order_by('slug', 'ASC')->get()->result();
    }

    public function updateAwbDetail($slug, $data)
    {
        return $this->db->where('slug', $slug)->update('awb_detail', $data);
    }

    public function list_driver()
    {
        return $this->db->where('role_id', '4')->get('user')->result();
    }

    public function getBooking($no_booking)
    {
        return $this->db->where('no_booking', $no_booking)->get('booking')->row_array();
    }

    public function getBookingById($id_booking)
    {
        return $this->db->where('Id', $id_booking)->get('booking')->row_array();
    }

    public function getItemAwb($slug)
    {
        return $this->db->where('slug', $slug)->get('awb_detail')->row_array();
    }

    public function cekItemAwb($slug)
    {
        return $this->db->where('slug', $slug)->get('awb_detail')->num_rows();
    }

    public function countItem($keyword)
    {
        if ($keyword) {
            $this->db->like('a.slug', $keyword);
            $this->db->or_like('nama_customer', $keyword);
            // $this->db->or_like('alamat_pickup', $keyword);
        }
        return $this->db->select('*, a.slug as slug_item')->from('awb_detail a')->join('booking b', 'a.booking_id = b.Id', 'left')->join('customer c', 'b.customer_id = c.id', 'left')->count_all_results();
    }

    public function listItemAwbPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->like('a.slug', $keyword);
            $this->db->or_like('nama_customer', $keyword);
            // $this->db->or_like('alamat_pickup', $keyword);
        }
        return $this->db->select('*, a.slug as slug_item')->from('awb_detail a')->join('booking b', 'a.booking_id = b.Id', 'left')->join('customer c', 'b.customer_id = c.id', 'left')->order_by('a.created_at', 'DESC')->order_by('a.slug', 'DESC')->limit($limit, $from)->get()->result();
    }

    public function dashboard()
    {
        $this->db->select("
        SUM(CASE WHEN status_tracking = 1 THEN 1 ELSE 0 END) AS 'status_1',
        SUM(CASE WHEN status_tracking = 2 THEN 1 ELSE 0 END) AS 'status_2',
        SUM(CASE WHEN status_tracking = 3 THEN 1 ELSE 0 END) AS 'status_3',
        SUM(CASE WHEN status_tracking = 4 THEN 1 ELSE 0 END) AS 'status_4' ");

        return $this->db->from('awb_detail')->get()->row_array();
    }

    public function updateBatchBooking($data)
    {
        return $this->db->update_batch('awb_detail', $data, 'slug');
    }

    public function updateAwbDetailByBookingId($id, $data)
    {
        return $this->db->where('booking_id', $id)->update('awb_detail', $data);
    }

    public function select_max_booking()
    {
        return $this->db->select('max(no_urut) as max')->where('DATE(created_at)', date('Y-m-d'))->get('booking')->row_array();
    }

    public function getPrice($origin, $destination)
    {
        $this->db->group_start();
        $this->db->where('origin', $origin);
        $this->db->where('destination', $destination);
        $this->db->group_end();
        return $this->db->get('mt_pricelist')->row_array();
    }

    public function selectMaxResi()
    {
        return $this->db->select('max(no_urut) as max')->where('DATE(created_at)', date('Y-m-d'))->get('resi')->row_array();
    }

    public function insertResi($data)
    {
        $this->db->insert('resi', $data);
        return $this->db->insert_id(); // Dapatkan ID terakhir yang diinsert
    }


    public function getResi($no_resi)
    {
        return $this->db->where('no_resi', $no_resi)->get('resi')->row_array();
    }

    public function updateResi($no_resi, $data)
    {
        return $this->db->where('no_resi', $no_resi)->update('resi', $data);
    }

    public function cekResi($slug)
    {
        return $this->db->where('no_resi', $slug)->get('resi')->num_rows();
    }

    public function getRevenueSummary($partner_id, $from, $to)
    {
        $this->db->where('partner_id', $partner_id);
        $this->db->where('DATE(created_at) >=', $from);
        $this->db->where('DATE(created_at) <=', $to);

        return $this->db->get('resi')->result();
    }

    public function getManifestPickup($partner_id, $driver_pickup_id, $jadwal_pickup)
    {
        $this->db->where('partner_id', $partner_id);
        $this->db->where('driver_pickup_id', $driver_pickup_id);
        $this->db->where('jadwal_pickup', $jadwal_pickup);

        return $this->db->get('resi')->result();
    }
}
