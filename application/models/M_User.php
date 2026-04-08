<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_User extends MY_Model
{

	protected $table      = 'users';
	protected $soft_delete = TRUE;

	// ----------------------------------------------------------------
	// GET dengan join role & agent
	// ----------------------------------------------------------------

	public function get_all_with_role($agent_id = NULL)
	{
		$this->db->select('u.*, r.name AS role_name, r.slug AS role_slug, r.scope AS role_scope, a.name AS agent_name')
			->from('users u')
			->join('roles r', 'r.id = u.role_id', 'left')
			->join('agents a', 'a.id = u.agent_id', 'left')
			->where('u.deleted_at IS NULL');

		if ($agent_id !== NULL) {
			$this->db->where('u.agent_id', $agent_id);
		}

		$this->db->order_by('u.created_at', 'DESC');
		return $this->db->get()->result();
	}

	public function get_detail($id)
	{
		$this->db->select('u.*, r.name AS role_name, r.slug AS role_slug, r.scope AS role_scope, a.name AS agent_name')
			->from('users u')
			->join('roles r', 'r.id = u.role_id', 'left')
			->join('agents a', 'a.id = u.agent_id', 'left')
			->where('u.id', $id)
			->where('u.deleted_at IS NULL');

		return $this->db->get()->row();
	}

	public function get_paged_with_role($limit, $offset, $agent_id = NULL, $search = NULL, $role_filter = NULL, $status_filter = NULL)
	{
		$this->db->select('u.*, r.name AS role_name, r.slug AS role_slug, a.name AS agent_name')
			->from('users u')
			->join('roles r', 'r.id = u.role_id', 'left')
			->join('agents a', 'a.id = u.agent_id', 'left')
			->where('u.deleted_at IS NULL');

		if ($agent_id !== NULL) {
			$this->db->where('u.agent_id', $agent_id);
		}

		if ($role_filter !== NULL) {
			$this->db->where('u.role_id', $role_filter);
		}

		if ($status_filter !== NULL) {
			$this->db->where('u.is_active', $status_filter);
		}

		if ($search) {
			$this->db->group_start()
				->like('u.name', $search)
				->or_like('u.email', $search)
				->or_like('r.name', $search)
				->group_end();
		}

		$this->db->order_by('u.created_at', 'DESC')
			->limit($limit, $offset);

		return $this->db->get()->result();
	}

	public function count_paged($agent_id = NULL, $search = NULL, $role_filter = NULL, $status_filter = NULL)
	{
		$this->db->from('users u')
			->join('roles r', 'r.id = u.role_id', 'left')
			->where('u.deleted_at IS NULL');

		if ($agent_id !== NULL) {
			$this->db->where('u.agent_id', $agent_id);
		}

		if ($role_filter !== NULL) {
			$this->db->where('u.role_id', $role_filter);
		}

		if ($status_filter !== NULL) {
			$this->db->where('u.is_active', $status_filter);
		}


		if ($search) {
			$this->db->group_start()
				->like('u.name', $search)
				->or_like('u.email', $search)
				->or_like('r.name', $search)
				->group_end();
		}

		return $this->db->count_all_results();
	}

	// ----------------------------------------------------------------
	// Validasi email unik (exclude diri sendiri saat edit)
	// ----------------------------------------------------------------

	public function email_exists($email, $exclude_id = NULL)
	{
		$this->db->where('email', $email)->where('deleted_at IS NULL');
		if ($exclude_id) {
			$this->db->where('id !=', $exclude_id);
		}
		return $this->db->count_all_results('users') > 0;
	}

	// ----------------------------------------------------------------
	// Hash password
	// ----------------------------------------------------------------

	public function hash_password($plain)
	{
		return password_hash($plain, PASSWORD_BCRYPT);
	}

	public function verify_password($plain, $hash)
	{
		return password_verify($plain, $hash);
	}

	// ----------------------------------------------------------------
	// Toggle status aktif
	// ----------------------------------------------------------------

	public function toggle_status($id)
	{
		$user = $this->get_by_id($id);
		if (! $user) return FALSE;
		$new_status = ($user->is_active == 1) ? 0 : 1;
		return $this->update($id, ['is_active' => $new_status]);
	}

	// ----------------------------------------------------------------
	// Statistik
	// ----------------------------------------------------------------

	public function count_by_role_scope($scope)
	{
		return $this->db->where('deleted_at IS NULL')
			->join('roles', 'roles.id = users.role_id')
			->where('roles.scope', $scope)
			->count_all_results('users');
	}

	public function count_by_agent($agent_id)
	{
		return $this->db->where('agent_id', $agent_id)
			->where('deleted_at IS NULL')
			->count_all_results('users');
	}
}
