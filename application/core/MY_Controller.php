<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $current_user;

	public function __construct()
	{
		parent::__construct();
		$this->current_user = $this->session->userdata('user');
	}

	// Render view dengan layout master
	protected function render($view, $data = [], $layout = 'index')
	{
		$data['current_user'] = $this->current_user;
		$data['pages']        = $view;
		$this->load->view('app/' . $layout, $data);
	}

	/**
	 * Build pagination data — pakai di controller mana saja.
	 *
	 * @param  int    $total        Total seluruh records (hasil COUNT dari model)
	 * @param  int    $per_page     Jumlah item per halaman (default 15)
	 * @param  array  $extra_params Query params tambahan yang harus ikut di URL
	 *                              Contoh: ['q' => $search, 'role' => $role, 'status' => $status]
	 *                              Nilai kosong/null akan otomatis dibuang.
	 * @return array  Siap di-merge ke $data controller, lalu pass ke partial _pagination
	 *
	 * Contoh pemakaian di controller:
	 *   $paginate = $this->paginate($total, 15, ['q' => $search, 'role' => $role]);
	 *   $data = array_merge(['title' => '...', 'users' => $users, 'total' => $total], $paginate);
	 *
	 * Contoh pemakaian di view:
	 *   $this->load->view('app/partials/_pagination', $data);
	 *   (cukup pass $data yang sama — partial hanya ambil key yang dibutuhkan)
	 */
}

// -------------------------------------------------------
// Controller khusus halaman yang butuh login
// -------------------------------------------------------
class Authenticated_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if (! $this->current_user) {
			redirect('auth');
		}
	}

	protected function paginate($total, $per_page = 15, $extra_params = [])
	{
		$page   = max(1, (int) ($this->input->get('page') ?: 1));
		$offset = ($page - 1) * $per_page;

		// Buang nilai kosong/null agar URL tetap bersih
		$clean_params = array_filter($extra_params, function ($v) {
			return $v !== NULL && $v !== '';
		});

		// Base URL: current URL + semua extra params + placeholder &page=
		// Contoh hasil: https://app.test/users?q=budi&role=admin-mitra&page=
		$base_url = current_url()
			. '?'
			. ($clean_params ? http_build_query($clean_params) . '&' : '')
			. 'page=';

		return [
			'page'        => $page,
			'per_page'    => $per_page,
			'offset'      => $offset,
			'total_pages' => ($total > 0) ? (int) ceil($total / $per_page) : 1,
			'base_url'    => $base_url,
		];
	}

	public function _check_access()
	{
		$sess = $this->session->userdata('user');
		if (! in_array($sess['role_slug'], $this->allowed_roles)) {
			show_error('Akses ditolak.', 403);
		}
	}
}

// -------------------------------------------------------
// Controller khusus scope global (Superadmin & Kribo HQ)
// -------------------------------------------------------
class Global_Controller extends Authenticated_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->current_user['role_scope'] !== 'global') {
			show_error('Akses ditolak.', 403);
		}
	}
}

// -------------------------------------------------------
// Controller khusus scope agent (semua sub-role Mitra)
// -------------------------------------------------------
class Agent_Controller extends Authenticated_Controller
{
	protected $agent_id;

	public function __construct()
	{
		parent::__construct();

		if ($this->current_user['role_scope'] !== 'agent') {
			show_error('Akses ditolak.', 403);
		}

		$this->agent_id = $this->current_user['agent_id'];
	}
}
