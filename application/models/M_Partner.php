<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_Partner extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function list_partner()
    {
        return $this->db->order_by('nama_pendaftar', 'ASC')->get('partner')->result();
    }

    public function list_active_partner()
    {
        return $this->db->where('has_account', '1')->where('hasil_review', 'diterima')->order_by('nama_pendaftar', 'ASC')->get('partner')->result();
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
        return $this->db->where('slug', $id)->get('partner')->num_rows();
    }

    public function countPartner($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_pendaftar', $keyword);
            $this->db->or_like('alamat_lengkap', $keyword);
            $this->db->or_like('kode_gerai', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->where('hasil_review =', 'diterima')->from('partner')->count_all_results();
    }

    public function listPartnerPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_pendaftar', $keyword);
            $this->db->or_like('alamat_lengkap', $keyword);
            $this->db->or_like('kode_gerai', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->where('hasil_review =', 'diterima')->from('partner')->order_by('nama_pendaftar', 'ASC')->limit($limit, $from)->get()->result();
    }

    public function countPendaftaran($keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_pendaftar', $keyword);
            $this->db->or_like('alamat_pendaftar', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->where('hasil_review !=', 'diterima')->from('partner')->count_all_results();
    }

    public function listPendaftaranPaginate($limit, $from, $keyword)
    {
        if ($keyword) {
            $this->db->group_start(); // Mulai grup kondisi
            $this->db->like('nama_pendaftar', $keyword);
            $this->db->or_like('alamat_pendaftar', $keyword);
            $this->db->group_end(); // Akhiri grup kondisi
        }

        return $this->db->where('hasil_review', 'belum di-review')->from('partner')->order_by('nama_pendaftar', 'ASC')->limit($limit, $from)->get()->result();
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
        return $this->db->insert('partner', $data);
    }

    public function getPendaftaran($id)
    {
        return $this->db->where('id', $id)->get('partner')->row_array();
    }

    public function updatePartner($id, $data)
    {
        $this->db->where('Id', $id);
        return $this->db->update('partner', $data);
    }

    public function selectMaxGerai()
    {
        return $this->db->select('max(no_urut) as max')->get('partner')->row_array();
    }

    public function getPartnerById($id)
    {
        return $this->db->where('Id', $id)->get('partner')->row_array();
    }

    public function selectMaxDepositCode()
    {
        return $this->db->select('max(no_urut_topup) as max')->get('deposit')->row_array();
    }

    public function getSaldoAkhirPartner($id)
    {
        return $this->db->select('(SUM(nominal_topup) - SUM(usage_saldo)) as saldo_akhir')
            ->where('partner_id', $id)
            ->get('deposit')
            ->row_array();
    }

    public function getDepositSummary($partner_id, $from = NULL, $to = NULL)
    {
        $this->queryDeposit($partner_id);

        if ($from && $to) {
            $this->db->where('DATE(t.post_date) >=', $from);
            $this->db->where('DATE(t.post_date) <=', $to);
        }

        return $this->db->order_by('t.id', 'ASC')->get()->result();
    }

    private function queryDeposit($partner_id)
    {
        $this->db->select('t.id, 
                   t.kode_topup, 
                   t.topup_date, 
                   t.nominal_topup,
                   t.usage_saldo, 
                   t.resi_id, 
                   t.partner_id, 
                   r.no_resi, 
                   r.created_at AS tanggal_resi, 
                   r.berat_timbang,
                   r.chargeable, 
                   r.qty, 
                   r.nominal AS nominal_resi,
                   r.origin,
                   r.destination,
                   r.commodity,
                   r.volume');

        // Subquery untuk menghitung sisa saldo
        $subquery = "(SELECT SUM(d.nominal_topup) - SUM(d.usage_saldo) 
              FROM deposit d 
              WHERE d.partner_id = t.partner_id AND d.id <= t.id)";

        // Menambahkan subquery ke dalam select
        $this->db->select("($subquery) AS sisa_saldo");

        // Join tabel resi
        $this->db->from('deposit t');
        $this->db->join('resi r', 't.resi_id = r.id', 'left');

        // Menambahkan kondisi WHERE
        $this->db->where('t.partner_id', $partner_id);
    }

    public function createAccount($data)
    {
        return $this->db->insert('user', $data);
    }
}
