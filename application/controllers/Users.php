<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Users Controller
 *
 * Akses:
 *   - superadmin     : full CRUD semua user
 *   - admin-kribo    : full CRUD semua user
 *   - admin-mitra    : CRUD user di agent sendiri saja
 */
class Users extends Authenticated_Controller
{

	// Role yang boleh akses modul ini
	protected $allowed_roles = ['superadmin', 'admin-kribo', 'admin-mitra'];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_User');
		$this->load->model('M_Agent');
		$this->load->library('form_validation');
		$this->load->helper('url');
	}

	// ----------------------------------------------------------------
	// INDEX — daftar user dengan pagination & search
	// ----------------------------------------------------------------

	public function index()
	{
		$this->_check_access();

		$sess     = $this->session->userdata('user');
		$agent_id = ($sess['role_scope'] === 'agent') ? $sess['agent_id'] : NULL;
		$search   = $this->input->get('q');
		$role     = $this->input->get('role');
		$status   = $this->input->get('status');

		// print_r($role);exit;

		$total = $this->M_User->count_paged($agent_id, $search, $role, $status);

		// ✅ Satu baris — semua pagination dihitung otomatis
		$paginate = $this->paginate($total, 15, [
			'q'      => $search,
			'role'   => $role,
			'status' => $status,
		]);

		// print_r($status);exit;
		$users = $this->M_User->get_paged_with_role(
			$paginate['per_page'],
			$paginate['offset'],
			$agent_id,
			$search,
			$role,
			$status
		);

		$data = array_merge([
			'title'         => 'Manajemen User',
			'users'         => $users,
			'total'         => $total,
			'search'        => $search,
			'role_filter'   => $role,
			'status_filter' => $status,
			'agent_scope'   => ($agent_id !== NULL),
		], $paginate);

		$this->render('app/pages/users/index', $data);
	}

	// ----------------------------------------------------------------
	// DETAIL
	// ----------------------------------------------------------------

	public function detail($id)
	{
		$this->_check_access();
		$user = $this->M_User->get_detail($id);
		$this->_guard_agent($user);

		// Log aktivitas terakhir user ini
		$logs = $this->db->where('user_id', $id)
			->order_by('created_at', 'DESC')
			->limit(10)
			->get('user_activity_logs')
			->result();

		$data = [
			'title'  => 'Detail User',
			'user'   => $user,
			'logs'   => $logs,
		];

		$this->render('app/pages/users/detail', $data);
	}

	// ----------------------------------------------------------------
	// CREATE
	// ----------------------------------------------------------------

	public function create()
	{
		$this->_check_access();

		$sess     = $this->session->userdata('user');
		$agent_id = ($sess['role_scope'] === 'agent') ? $sess['agent_id'] : NULL;

		// Roles tersedia + metadata scope per role_id (untuk JS show/hide field agen)
		$roles       = $this->_get_available_roles($sess['role_slug']);
		$roles_scope = $this->_get_roles_scope($sess['role_slug']);

		// Dropdown agen hanya relevan untuk scope global
		$agents       = ($agent_id === NULL) ? $this->M_Agent->get_dropdown() : [];
		$agents_empty = ($agent_id === NULL && empty($agents));

		// Validasi
		$this->form_validation->set_rules('name',     'Nama',     'required|trim|max_length[100]');
		$this->form_validation->set_rules('email',    'Email',    'required|trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
		$this->form_validation->set_rules('role_id',  'Role',     'required|integer');

		// agent_id wajib jika scope global dan role yang dipilih ber-scope agent
		if ($agent_id === NULL) {
			$selected_role_id = $this->input->post('role_id');
			$selected_scope   = isset($roles_scope[$selected_role_id])
				? $roles_scope[$selected_role_id]
				: NULL;

			if ($selected_scope === 'agent') {
				// Role mitra dipilih → agent_id wajib
				$this->form_validation->set_rules('agent_id', 'Agen', 'required');
			}
			// Role global dipilih → tidak perlu validasi agent_id sama sekali
		}

		if ($this->form_validation->run()) {
			$email = $this->input->post('email');

			if ($this->M_User->email_exists($email)) {
				$this->session->set_flashdata('error', 'Email sudah terdaftar.');
				redirect('users/create');
			}

			$final_agent_id = ($agent_id !== NULL)
				? $agent_id
				: ($this->input->post('agent_id') ?: NULL);

			$insert = [
				'name'      => $this->input->post('name'),
				'email'     => $email,
				'password'  => $this->M_User->hash_password($this->input->post('password')),
				'role_id'   => $this->input->post('role_id'),
				'agent_id'  => $final_agent_id,
				'is_active' => 1,
			];

			$new_id = $this->M_User->insert($insert);
			if ($new_id) {
				$this->_log('create_user', 'Membuat user baru: ' . $email);
				$this->session->set_flashdata('success', 'User berhasil ditambahkan.');
				redirect('users/detail/' . $new_id);
			}

			$this->session->set_flashdata('error', 'Gagal menyimpan data user.');
			redirect('users/create');
		}

		$data = [
			'title'        => 'Tambah User',
			'roles'        => $roles,
			'roles_scope'  => $roles_scope,
			'agents'       => $agents,
			'agents_empty' => $agents_empty,
			'agent_id'     => $agent_id,
		];

		$this->render('app/pages/users/create', $data);
	}

	// ----------------------------------------------------------------
	// EDIT
	// ----------------------------------------------------------------

	public function edit($id)
	{
		$this->_check_access();
		$sess   = $this->session->userdata('user');
		$user   = $this->M_User->get_detail($id);
		$this->_guard_agent($user);

		$agent_id = ($sess['role_scope'] === 'agent') ? $sess['agent_id'] : NULL;
		$roles    = $this->_get_available_roles($sess['role_slug']);
		$agents   = ($agent_id === NULL) ? $this->M_Agent->get_dropdown() : [];

		$this->form_validation->set_rules('name', 'Nama', 'required|trim|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|max_length[150]');
		$this->form_validation->set_rules('role_id', 'Role', 'required|integer');

		if ($this->form_validation->run()) {
			$email = $this->input->post('email');
			if ($this->M_User->email_exists($email, $id)) {
				$this->session->set_flashdata('error', 'Email sudah digunakan user lain.');
				redirect('users/edit/' . $id);
			}

			$update = [
				'name'    => $this->input->post('name'),
				'phone'    => $this->input->post('phone'),
				'email'   => $email,
				'role_id' => $this->input->post('role_id'),
			];

			// agent_id hanya boleh diubah oleh global scope
			if ($agent_id === NULL) {
				$update['agent_id'] = $this->input->post('agent_id') ?: NULL;
			}

			// Ganti password hanya jika diisi
			$new_pw = $this->input->post('password');
			if ($new_pw) {
				$update['password'] = $this->M_User->hash_password($new_pw);
			}

			$this->M_User->update($id, $update);
			$this->_log('edit_user', 'Mengedit user ID: ' . $id);
			$this->session->set_flashdata('success', 'User berhasil diperbarui.');
			redirect('users/detail/' . $id);
		}

		$data = [
			'title'    => 'Edit User',
			'user'     => $user,
			'roles'    => $roles,
			'agents'   => $agents,
			'agent_id' => $agent_id,
		];

		$this->render('app/pages/users/edit', $data);
	}

	// ----------------------------------------------------------------
	// TOGGLE STATUS (AJAX-friendly, redirect fallback)
	// ----------------------------------------------------------------

	public function toggle_status($id)
	{
		$this->_check_access();
		$user = $this->M_User->get_detail($id);
		$this->_guard_agent($user);

		$this->M_User->toggle_status($id);
		$this->_log('toggle_user_status', 'Toggle status user ID: ' . $id);
		$this->session->set_flashdata('success', 'Status user berhasil diubah.');
		redirect('users');
	}

	// ----------------------------------------------------------------
	// DELETE (soft delete)
	// ----------------------------------------------------------------

	public function delete($id)
	{
		$this->_check_access();
		$sess = $this->session->userdata('user');

		// Tidak bisa hapus diri sendiri
		if ((int)$id === (int)$sess['id']) {
			$this->session->set_flashdata('error', 'Tidak dapat menghapus akun sendiri.');
			redirect('users');
		}

		$user = $this->M_User->get_detail($id);
		$this->_guard_agent($user);

		$this->M_User->soft_delete($id);
		$this->_log('delete_user', 'Menghapus user ID: ' . $id);
		$this->session->set_flashdata('success', 'User berhasil dihapus.');
		redirect('users');
	}

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

	/**
	 * Cek apakah role_slug ada di allowed_roles
	 */
	// private function _check_access()
	// {
	// 	$sess = $this->session->userdata('user');
	// 	if (! in_array($sess['role_slug'], $this->allowed_roles)) {
	// 		show_error('Akses ditolak.', 403);
	// 	}
	// }

	/**
	 * Pastikan admin-mitra hanya bisa lihat user di agent-nya sendiri
	 */
	private function _guard_agent($user)
	{
		if (! $user) show_404();

		$sess = $this->session->userdata('user');
		if ($sess['role_scope'] === 'agent' && (int)$user->agent_id !== (int)$sess['agent_id']) {
			show_error('Akses ditolak.', 403);
		}
	}

	/**
	 * Roles yang bisa dibuat/diedit sesuai role pembuat
	 */
	private function _get_available_roles($creator_slug)
	{
		// superadmin & admin-kribo bisa assign semua role
		// admin-mitra hanya bisa assign role scope=agent
		$this->db->where('deleted_at IS NULL');

		if ($creator_slug === 'admin-mitra') {
			$this->db->where('scope', 'agent');
		}

		$rows = $this->db->order_by('level')->get('roles')->result();
		$dropdown = [];
		foreach ($rows as $r) {
			$dropdown[$r->id] = $r->name . ' (' . $r->scope . ')';
		}
		return $dropdown;
	}

	private function _get_roles_scope($creator_slug)
	{
		if ($creator_slug === 'admin-mitra') {
			$this->db->where('scope', 'agent');
		}

		$rows = $this->db->select('id, scope')->get('roles')->result();
		$map  = [];
		foreach ($rows as $r) {
			$map[$r->id] = $r->scope;
		}
		return $map;
	}

	/**
	 * Log aktivitas
	 */
	private function _log($action, $description)
	{
		$sess = $this->session->userdata('user');
		$this->db->insert('user_activity_logs', [
			'user_id'     => $sess['id'],
			'action'      => $action,
			'description' => $description,
			'ip_address'  => $this->input->ip_address(),
			'created_at'  => date('Y-m-d H:i:s'),
		]);
	}

	/**
	 * Render view dalam layout master
	 */
	// private function _render($view, $data = [])
	// {
	// 	$sess              = $this->session->userdata('user');
	// 	$data['user_sess'] = $sess;
	// 	$data['menu']      = $this->_get_menu($sess['role_slug']);

	// 	$data['content_view'] = $view;
	// 	$this->load->view('app/index', $data);
	// }
}
