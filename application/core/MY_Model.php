<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	// Override di child model
	protected $table        = '';
	protected $primary_key  = 'id';
	protected $soft_delete  = TRUE;   // FALSE kalau tabel tidak punya deleted_at
	protected $timestamps   = TRUE;   // FALSE kalau tabel tidak punya created_at/updated_at

	// Kolom yang boleh di-insert/update (whitelist)
	// Override di child model, kosong = semua kolom boleh
	protected $fillable     = [];

	public function __construct()
	{
		parent::__construct();
	}

	// -------------------------------------------------------
	// READ
	// -------------------------------------------------------

	// Ambil semua data (aktif, belum dihapus)
	public function get_all($conditions = [], $order_by = 'id', $order_dir = 'ASC')
	{
		$this->_apply_soft_delete();

		if (! empty($conditions)) {
			$this->db->where($conditions);
		}

		return $this->db
			->order_by($this->table . '.' . $order_by, $order_dir)
			->get($this->table)
			->result();
	}

	// Ambil satu data by primary key
	public function get_by_id($id)
	{
		$this->_apply_soft_delete();

		return $this->db
			->where($this->table . '.' . $this->primary_key, $id)
			->get($this->table)
			->row();
	}

	// Ambil satu data by kondisi tertentu
	public function get_by($conditions = [])
	{
		$this->_apply_soft_delete();
		$this->db->where($conditions);

		return $this->db
			->get($this->table)
			->row();
	}

	// Ambil banyak data by kondisi tertentu
	public function get_many_by($conditions = [])
	{
		$this->_apply_soft_delete();
		$this->db->where($conditions);

		return $this->db
			->get($this->table)
			->result();
	}

	// Hitung total row (untuk pagination)
	public function count_all($conditions = [])
	{
		$this->_apply_soft_delete();

		if (! empty($conditions)) {
			$this->db->where($conditions);
		}

		return $this->db->count_all_results($this->table);
	}

	// Ambil data dengan pagination
	public function get_paged($limit = 10, $offset = 0, $conditions = [], $order_by = 'id', $order_dir = 'DESC')
	{
		$this->_apply_soft_delete();

		if (! empty($conditions)) {
			$this->db->where($conditions);
		}

		return $this->db
			->order_by($this->table . '.' . $order_by, $order_dir)
			->limit($limit, $offset)
			->get($this->table)
			->result();
	}

	// -------------------------------------------------------
	// CREATE
	// -------------------------------------------------------
	public function insert($data)
	{
		$data = $this->_filter_fillable($data);

		if ($this->timestamps) {
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
		}

		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	// -------------------------------------------------------
	// UPDATE
	// -------------------------------------------------------
	public function update($id, $data)
	{
		$data = $this->_filter_fillable($data);

		if ($this->timestamps) {
			$data['updated_at'] = date('Y-m-d H:i:s');
		}

		return $this->db
			->where($this->primary_key, $id)
			->update($this->table, $data);
	}

	// Update by kondisi tertentu
	public function update_by($conditions = [], $data = [])
	{
		$data = $this->_filter_fillable($data);

		if ($this->timestamps) {
			$data['updated_at'] = date('Y-m-d H:i:s');
		}

		return $this->db
			->where($conditions)
			->update($this->table, $data);
	}

	// -------------------------------------------------------
	// DELETE
	// -------------------------------------------------------

	// Soft delete — set deleted_at, data tetap ada di DB
	public function soft_delete($id)
	{
		if (! $this->soft_delete) {
			return $this->hard_delete($id);
		}

		return $this->db
			->where($this->primary_key, $id)
			->update($this->table, [
				'deleted_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
	}

	// Hard delete — benar-benar hapus dari DB
	// Pakai hati-hati bro!
	public function hard_delete($id)
	{
		return $this->db
			->where($this->primary_key, $id)
			->delete($this->table);
	}

	// Restore data yang di-soft delete
	public function restore($id)
	{
		return $this->db
			->where($this->primary_key, $id)
			->update($this->table, [
				'deleted_at' => NULL,
				'updated_at' => date('Y-m-d H:i:s'),
			]);
	}

	// -------------------------------------------------------
	// HELPER INTERNAL
	// -------------------------------------------------------

	// Auto tambah WHERE deleted_at IS NULL kalau soft_delete aktif
	protected function _apply_soft_delete()
	{
		if ($this->soft_delete) {
			$this->db->where($this->table . '.deleted_at', NULL);
		}
	}

	// Filter kolom yang boleh masuk (whitelist)
	// Kalau $fillable kosong, semua kolom diloloskan
	protected function _filter_fillable($data)
	{
		if (empty($this->fillable)) {
			return $data;
		}

		return array_intersect_key($data, array_flip($this->fillable));
	}
}
