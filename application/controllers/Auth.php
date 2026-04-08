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
		} else {
			if ($this->form_validation->run() ==  false) {
				$data = [
					"title" => "Login",
					"pages" => "auth/v_login"
				];
				$this->load->view('auth/index', $data);
			} else {
				// validasinya sukses
				$this->_login();
			}
		}
	}

	private function _login()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		$user = $this->db
			->select('users.*, roles.slug as role_slug, roles.scope as role_scope, roles.name as role_name')
			->join('roles', 'roles.id = users.role_id')
			->where('users.email', $email)
			->where('users.deleted_at', NULL)
			->get('users')
			->row_array();

		if ($user) {

			// usernya ada
			if ($user['is_active'] == 1) {
				if (password_verify($password, $user['password'])) {
					$data = [
						'user'       => [
							'id'         => $user['id'],
							'name'       => $user['name'],
							'email'      => $user['email'],
							'role_id'    => $user['role_id'],
							'role_slug'  => $user['role_slug'],    // untuk cek permission
							'role_scope' => $user['role_scope'],   // 'global' atau 'agent'
							'role_name'  => $user['role_name'],    // untuk tampilan di UI
							'agent_id'   => $user['agent_id'],     // NULL kalau scope global
						],
						'is_logged_in' => TRUE,
					];

					// set last_login
					$this->db->where('id', $user['id'])
						->update('users', ['last_login' => date('Y-m-d H:i:s')]);

					$this->session->set_userdata($data);
					// Cek apakah ada halaman terakhir yang diakses
					$last_page = $this->session->userdata('last_page');

					if ($last_page) {
						// Arahkan kembali ke halaman terakhir
						redirect($last_page);
					} else {
						// Arahkan ke halaman default (misalnya dashboard)
						if ($password == strtolower($username)) {
							$this->session->set_flashdata('message_warning', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Anda masih menggunakan password bawaan. Silahkan perbarui password!.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>');
						}
						$this->_redirect_by_role($user['role_scope']);
					}
				} else {
					$this->session->set_flashdata('message_success', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Wrong password.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>');
					redirect('auth');
				}
			} else {
				$this->session->set_flashdata('message_success', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				Username has not been activated.
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>');
				redirect('auth');
			}
		} else {
			$this->session->set_flashdata('message_success', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			Username has not been registered.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

			redirect('auth');
		}
	}

	private function _redirect_by_role($scope)
	{
		// Semua role masuk ke route 'dashboard'
		// tapi controller Dashboard akan render view berbeda by role_slug
		redirect('dashboard');
	}

	public function change_password($id)
	{

		$this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
			'matches' => 'Password dont match!',
			'min_length' => 'Password too short!'
		]);
		$this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

		if ($this->form_validation->run() == false) {
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();

		$this->session->set_flashdata('message_success', '<div class="alert alert-success alert-dismissible fade show" role="alert">
		You have been logout.
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>');
		redirect('auth');
	}

	public function forbidden()
	{
		$this->load->view('errors/html/error_403');
	}
}
