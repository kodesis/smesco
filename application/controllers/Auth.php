<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->model('M_Auth');
	}

	public function index()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');

		if ($this->session->userdata('is_logged_in')) {
			redirect('dashboard');
		}

		if ($this->form_validation->run() == false) {
			$data = [
				'title' => 'Login',
				'pages' => 'auth/v_login'
			];

			$this->load->view('auth/index', $data);
			return;
		}

		$this->_login();
	}

	private function _login()
	{
		$email    = trim($this->input->post('email', true));
		$password = $this->input->post('password');

		$user = $this->db
			->select('
				users.*,
				roles.slug AS role_slug,
				roles.scope AS role_scope,
				roles.name AS role_name
			')
			->from('users')
			->join('roles', 'roles.id = users.role_id')
			->where('users.email', $email)
			->where('users.deleted_at', null)
			->get()
			->row_array();

		if (!$user) {
			$this->session->set_flashdata(
				'message_success',
				'<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Username has not been registered.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);

			redirect('auth');
			return;
		}

		if ((int) $user['is_active'] !== 1) {
			$this->session->set_flashdata(
				'message_success',
				'<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Username has not been activated.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);

			redirect('auth');
			return;
		}

		if (!password_verify($password, $user['password'])) {
			$this->session->set_flashdata(
				'message_success',
				'<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Wrong password.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);

			redirect('auth');
			return;
		}

		/*
		|--------------------------------------------------------------------------
		| Data Agent
		|--------------------------------------------------------------------------
		| User dengan scope global tidak wajib memiliki agent_id.
		| User dengan scope agent wajib memiliki agent_id.
		*/
		$agent = null;

		if (!empty($user['agent_id'])) {
			$agent = $this->db
				->select('
					id,
					code,
					name,
					province_id,
					city_id,
					subdistrict_id,
					can_scan_pickup_as_driver,
					can_scan_at_origin
				')
				->from('agents')
				->where('id', $user['agent_id'])
				->where('deleted_at', null)
				->get()
				->row_array();
		}

		/*
		|--------------------------------------------------------------------------
		| Validasi Agent
		|--------------------------------------------------------------------------
		*/
		if ($user['role_scope'] === 'agent' && !$agent) {
			$this->session->set_flashdata(
				'message_success',
				'<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Akun agent belum terhubung dengan data agent.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);

			redirect('auth');
			return;
		}

		/*
		|--------------------------------------------------------------------------
		| Session User
		|--------------------------------------------------------------------------
		*/
		$session_user = [
			'id'         => $user['id'],
			'name'       => $user['name'],
			'email'      => $user['email'],
			'role_id'    => $user['role_id'],
			'role_slug'  => $user['role_slug'],
			'role_scope' => $user['role_scope'],
			'role_name'  => $user['role_name'],
			'agent_id'   => $user['agent_id'],

			// Data agent
			'agent_code' => $agent['code'] ?? null,
			'agent_name' => $agent['name'] ?? null,

			// Wilayah agent
			'province_id'    => $agent['province_id'] ?? null,
			'city_id'        => $agent['city_id'] ?? null,
			'subdistrict_id' => $agent['subdistrict_id'] ?? null,

			// Capability agent
			'can_scan_pickup_as_driver' => $agent['can_scan_pickup_as_driver'] ?? 0,
			'can_scan_at_origin'        => $agent['can_scan_at_origin'] ?? 0,
		];

		$this->session->set_userdata([
			'user'         => $session_user,
			'is_logged_in' => true,
		]);

		/*
		|--------------------------------------------------------------------------
		| Update Last Login
		|--------------------------------------------------------------------------
		*/
		$this->db
			->where('id', $user['id'])
			->update('users', [
				'last_login' => date('Y-m-d H:i:s')
			]);

		/*
		|--------------------------------------------------------------------------
		| Password Default
		|--------------------------------------------------------------------------
		|
		| Sebelumnya menggunakan $username yang tidak pernah didefinisikan.
		| Kita gunakan email sebagai pembanding.
		|
		*/
		if ($password === strtolower($user['email'])) {
			$this->session->set_flashdata(
				'message_warning',
				'<div class="alert alert-warning alert-dismissible fade show" role="alert">
					Anda masih menggunakan password bawaan. Silakan perbarui password.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);
		}

		/*
		|--------------------------------------------------------------------------
		| Redirect
		|--------------------------------------------------------------------------
		*/
		$last_page = $this->session->userdata('last_page');

		if ($last_page) {
			$this->session->unset_userdata('last_page');
			redirect($last_page);
			return;
		}

		$this->_redirect_by_role($user['role_scope']);
	}

	private function _redirect_by_role($scope)
	{
		// Semua role tetap masuk dashboard.
		// Dashboard menentukan tampilan berdasarkan role_slug.
		redirect('dashboard');
	}

	public function change_password($id)
	{
		$this->form_validation->set_rules(
			'password1',
			'Password',
			'required|trim|min_length[3]|matches[password2]',
			[
				'matches'    => 'Password dont match!',
				'min_length' => 'Password too short!'
			]
		);

		$this->form_validation->set_rules(
			'password2',
			'Password',
			'required|trim|matches[password1]'
		);

		if ($this->form_validation->run() == false) {
			// TODO
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();

		$this->session->set_flashdata(
			'message_success',
			'<div class="alert alert-success alert-dismissible fade show" role="alert">
				You have been logout.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>'
		);

		redirect('auth');
	}

	public function forbidden()
	{
		$this->load->view('errors/html/error_403');
	}
}
