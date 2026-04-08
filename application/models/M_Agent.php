<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Agent extends MY_Model
{
	protected $table       = 'agents';
	protected $soft_delete = TRUE;

	// ================================================================
	// GENERATE KODE AGEN
	// Format: SMC-{province_id}.{regency_id tanpa prefix provinsi}.{urut 3 digit}
	// Contoh: regency_id = "31.75" → SMC-31.75.001
	// Nomor urut dihitung per regency_id
	// ================================================================

	public function generate_code($regency_id)
	{
		// Hitung agen yang sudah ada di kota/kab yang sama (termasuk soft-deleted,
		// supaya nomor urut tidak pernah mundur / bentrok)
		$count = $this->db
			->where('regency_id', $regency_id)
			->count_all_results('agents');

		$urut = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

		return 'SMC-' . $regency_id . '.' . $urut;
		// → SMC-31.75.001
	}

    // ================================================================
    // MASTER WILAYAH — dropdown
    // ================================================================

	/**
	 * Semua provinsi untuk dropdown
	 * Return: [id => nama]
	 */
	public function get_provinces()
	{
		$rows = $this->db
			->select('id, nama_provinsi as nama')
			->order_by('nama')
			->get('mt_provinsi')
			->result();

		$dropdown = [];
		foreach ($rows as $r) {
			$dropdown[$r->id] = $r->nama;
		}
		return $dropdown;
	}

	/**
	 * Kota/kabupaten berdasarkan province_id (untuk cascading AJAX)
	 * Return: array of objects {id, nama}
	 */
	public function get_regencies_by_province($province_id)
	{
		return $this->db
			->select('id, nama_kota as nama')
			->like('id', $province_id, 'after') // id kota diawali dengan kode provinsi
			// misal province_id=31 → ambil semua id yang diawali "31."
			// hasilnya: 31.71, 31.72, 31.73, dst
			->order_by('nama')
			->get('mt_kota')
			->result();
	}

	/**
	 * Nama provinsi berdasarkan id
	 */
	public function get_province_name($province_id)
	{
		$row = $this->db->where('id', $province_id)->get('mt_provinsi')->row();
		return $row ? $row->nama : '—';
	}

	/**
	 * Nama kota berdasarkan id
	 */
	public function get_regency_name($regency_id)
	{
		$row = $this->db->where('id', $regency_id)->get('mt_kota')->row();
		return $row ? $row->nama : '—';
	}

	// ================================================================
	// GET dengan join wilayah & stats user
	// ================================================================

	public function get_all_with_stats()
	{
		$this->db
			->select('a.*, COUNT(u.id) AS total_users, p.nama_provinsi AS province_name, k.nama_kota AS regency_name')
			->from('agents a')
			->join('users u',       'u.agent_id = a.id AND u.deleted_at IS NULL', 'left')
			->join('mt_provinsi p', 'p.id = a.province_id',                       'left')
			->join('mt_kota k',     'k.id = a.regency_id',                        'left')
			->where('a.deleted_at IS NULL')
			->group_by('a.id')
			->order_by('a.created_at', 'DESC');

		return $this->db->get()->result();
	}

	public function get_detail($id)
	{
		$this->db
			->select('a.*, COUNT(u.id) AS total_users, p.nama_provinsi AS province_name, k.nama_kota AS regency_name')
			->from('agents a')
			->join('users u',       'u.agent_id = a.id AND u.deleted_at IS NULL', 'left')
			->join('mt_provinsi p', 'p.id = a.province_id',                       'left')
			->join('mt_kota k',     'k.id = a.regency_id',                        'left')
			->where('a.id', $id)
			->where('a.deleted_at IS NULL')
			->group_by('a.id');

		return $this->db->get()->row();
	}

	public function get_paged_with_stats($limit, $offset, $search = NULL)
	{
		$this->db
			->select('a.*, COUNT(u.id) AS total_users, p.nama_provinsi AS province_name, k.nama_kota AS regency_name')
			->from('agents a')
			->join('users u',       'u.agent_id = a.id AND u.deleted_at IS NULL', 'left')
			->join('mt_provinsi p', 'p.id = a.province_id',                       'left')
			->join('mt_kota k',     'k.id = a.regency_id',                        'left')
			->where('a.deleted_at IS NULL')
			->group_by('a.id');

		if ($search) {
			$this->db->group_start()
				->like('a.name',   $search)
				->or_like('a.code', $search)
				->or_like('k.nama_kota', $search)
				->or_like('p.nama', $search)
				->group_end();
		}

		$this->db->order_by('a.created_at', 'DESC')->limit($limit, $offset);

		return $this->db->get()->result();
	}

	public function count_paged($search = NULL)
	{
		$this->db
			->from('agents a')
			->join('mt_provinsi p', 'p.id = a.province_id', 'left')
			->join('mt_kota k',     'k.id = a.regency_id',  'left')
			->where('a.deleted_at IS NULL');

		if ($search) {
			$this->db->group_start()
				->like('a.name',   $search)
				->or_like('a.code', $search)
				->or_like('k.nama_kota', $search)
				->or_like('p.nama', $search)
				->group_end();
		}

		return $this->db->count_all_results();
	}

	// ================================================================
	// VALIDASI
	// ================================================================

	public function code_exists($code, $exclude_id = NULL)
	{
		$this->db->where('code', $code)->where('deleted_at IS NULL');
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}
		return $this->db->count_all_results('agents') > 0;
	}

	// ================================================================
	// DROPDOWN untuk form user
	// ================================================================

	public function get_dropdown()
	{
		$rows = $this->db
			->select('a.id, a.name, a.code, k.nama_kota AS regency_name')
			->from('agents a')
			->join('mt_kota k', 'k.id = a.regency_id', 'left')
			->where('a.deleted_at IS NULL')
			->where('a.is_active', 1)
			->order_by('a.name')
			->get()
			->result();

		$dropdown = [];
		foreach ($rows as $r) {
			$dropdown[$r->id] = $r->name . ' — ' . $r->code;
			if ($r->regency_name) {
				$dropdown[$r->id] .= ' (' . $r->regency_name . ')';
			}
		}
		return $dropdown;
	}

	// ================================================================
	// TOGGLE STATUS & STATISTIK
	// ================================================================

	public function toggle_status($id)
	{
		$agent = $this->get_by_id($id);
		if (!$agent) return FALSE;
		return $this->update($id, ['is_active' => ($agent->is_active == 1) ? 0 : 1]);
	}

	public function count_active()
	{
		return $this->db
			->where('deleted_at IS NULL')
			->where('is_active', 1)
			->count_all_results('agents');
	}

	public function count_by_regency($regency_id)
	{
		return $this->db
			->where('regency_id', $regency_id)
			->where('deleted_at IS NULL')
			->count_all_results('agents');
	}
}
