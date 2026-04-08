<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Agents Controller
 * Akses: superadmin, admin-kribo
 * Edit kode agen: superadmin only
 */
class Agents extends Authenticated_Controller
{
	protected $allowed_roles = ['superadmin', 'admin-kribo'];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_Agent');
		$this->load->model('M_User');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->_check_access();
	}

	// ----------------------------------------------------------------
	// INDEX
	// ----------------------------------------------------------------

	public function index()
	{
		$search   = $this->input->get('q');
		$per_page = 15;
		$page     = (int) ($this->input->get('page') ?: 1);
		$offset   = ($page - 1) * $per_page;

		$total  = $this->M_Agent->count_paged($search);
		$agents = $this->M_Agent->get_paged_with_stats($per_page, $offset, $search);

		$data = [
			'title'       => 'Manajemen Agen',
			'agents'      => $agents,
			'total'       => $total,
			'per_page'    => $per_page,
			'page'        => $page,
			'search'      => $search,
			'total_pages' => ceil($total / $per_page),
			'offset' 	 => $offset,
		];

		$this->render('app/pages/agents/index', $data);
	}

	// ----------------------------------------------------------------
	// DETAIL
	// ----------------------------------------------------------------

	public function detail($id)
	{
		$agent = $this->M_Agent->get_detail($id);
		if (!$agent) show_404();

		$users = $this->M_User->get_all_with_role($id);

		$data = [
			'title' => 'Detail Agen',
			'agent' => $agent,
			'users' => $users,
		];

		$this->render('app/pages/agents/detail', $data);
	}

	// ----------------------------------------------------------------
	// CREATE
	// ----------------------------------------------------------------

	public function create()
	{
		$provinces = $this->M_Agent->get_provinces();
		// AMBIL DATA KOTA KARGO (HUB)
		$cargo_cities = $this->db->get_where('cities', ['is_active' => 1])->result();

		$this->form_validation->set_rules('name',        'Nama Agen',  'required|trim|max_length[150]');
		$this->form_validation->set_rules('province_id', 'Provinsi',   'required|trim');
		$this->form_validation->set_rules('regency_id',  'Kota/Kab',   'required|trim');
		$this->form_validation->set_rules('address',     'Alamat',     'trim');
		$this->form_validation->set_rules('phone',       'Telepon',    'trim|max_length[30]');
		$this->form_validation->set_rules('email',       'Email Agen', 'trim|valid_email|max_length[150]');

		if ($this->form_validation->run()) {
			$regency_id  = $this->input->post('regency_id');
			$province_id = $this->input->post('province_id');

			// Generate kode otomatis
			$code = $this->M_Agent->generate_code($regency_id);

			// Pastikan tidak collision (edge case concurrent request)
			if ($this->M_Agent->code_exists($code)) {
				// Coba generate ulang dengan count actual
				$count = $this->db
					->where('regency_id', $regency_id)
					->count_all_results('agents');
				$code = 'SMC-' . $regency_id . '.' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
			}

			$insert = [
				'name'        => $this->input->post('name'),
				'code'        => $code,
				'city_id'     => $this->input->post('city_id'),
				'province_id' => $province_id,
				'regency_id'  => $regency_id,
				'address'     => $this->input->post('address'),
				'phone'       => $this->input->post('phone'),
				'email'       => $this->input->post('email'),
				'is_active'   => 1,
			];

			$new_id = $this->M_Agent->insert($insert);
			if ($new_id) {
				$this->_log('create_agent', 'Membuat agen baru: ' . $code);
				$this->session->set_flashdata('success', 'Agen berhasil ditambahkan dengan kode <strong>' . $code . '</strong>.');
				redirect('agents/detail/' . $new_id);
			}

			$this->session->set_flashdata('error', 'Gagal menyimpan data agen.');
			redirect('agents/create');
		}

		$data = [
			'title'     => 'Tambah Agen',
			'provinces' => $provinces,
			'cargo_cities' => $cargo_cities,
		];

		$this->render('app/pages/agents/create', $data);
	}

	// ----------------------------------------------------------------
	// EDIT
	// ----------------------------------------------------------------

	public function edit($id)
	{
		$agent = $this->M_Agent->get_detail($id);
		if (!$agent) show_404();

		$sess      = $this->session->userdata('user');
		$cargo_cities = $this->db->get_where('cities', ['is_active' => 1])->result();
		$provinces = $this->M_Agent->get_provinces();

		// Kode agen hanya bisa diedit oleh superadmin
		$can_edit_code = ($sess['role_slug'] === 'superadmin');

		$this->form_validation->set_rules('name',        'Nama Agen',  'required|trim|max_length[150]');
		$this->form_validation->set_rules('province_id', 'Provinsi',   'required|trim');
		$this->form_validation->set_rules('regency_id',  'Kota/Kab',   'required|trim');
		$this->form_validation->set_rules('address',     'Alamat',     'trim');
		$this->form_validation->set_rules('phone',       'Telepon',    'trim|max_length[30]');
		$this->form_validation->set_rules('email',       'Email Agen', 'trim|valid_email|max_length[150]');

		if ($this->form_validation->run()) {
			$regency_id  = $this->input->post('regency_id');
			$province_id = $this->input->post('province_id');

			$update = [
				'name'        => $this->input->post('name'),
				'city_id'     => $this->input->post('city_id'),
				'province_id' => $province_id,
				'regency_id'  => $regency_id,
				'address'     => $this->input->post('address'),
				'phone'       => $this->input->post('phone'),
				'email'       => $this->input->post('email'),
			];

			// Regenerate kode jika wilayah berubah
			$wilayah_berubah = ($regency_id !== $agent->regency_id);
			if ($wilayah_berubah) {
				$new_code = $this->M_Agent->generate_code($regency_id);
				$update['code'] = $new_code;
				$this->session->set_flashdata('info', 'Kode agen diperbarui menjadi <strong>' . $new_code . '</strong> karena wilayah berubah.');
			}

			$this->M_Agent->update($id, $update);
			$this->_log('edit_agent', 'Mengedit agen ID: ' . $id . ' (' . $agent->code . ')');
			$this->session->set_flashdata('success', 'Agen berhasil diperbarui.');
			redirect('agents/detail/' . $id);
		}

		// Ambil daftar kota untuk provinsi yang sudah tersimpan (untuk pre-populate dropdown)
		$regencies = $this->M_Agent->get_regencies_by_province($agent->province_id);

		$data = [
			'title'         => 'Edit Agen',
			'agent'         => $agent,
			'provinces'     => $provinces,
			'regencies'     => $regencies,
			'can_edit_code' => $can_edit_code,
			'cargo_cities' => $cargo_cities,
		];

		$this->render('app/pages/agents/edit', $data);
	}

	// ----------------------------------------------------------------
	// AJAX — ambil kota berdasarkan provinsi (untuk cascading dropdown)
	// ----------------------------------------------------------------

	public function get_regencies($province_id)
	{
		// Hanya terima request dari dalam sistem (basic check)
		if (!$this->input->is_ajax_request()) {
			show_error('Bad Request', 400);
		}

		$regencies = $this->M_Agent->get_regencies_by_province($province_id);

		// Sertakan preview kode untuk setiap kota (berapa agen sudah ada)
		$result = [];
		foreach ($regencies as $r) {
			$count  = $this->M_Agent->count_by_regency($r->id);
			$urut   = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
			$result[] = [
				'id'           => $r->id,
				'nama'         => $r->nama,
				'code_preview' => 'SMC-' . $r->id . '.' . $urut,
			];
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));
	}

	// ----------------------------------------------------------------
	// TOGGLE STATUS
	// ----------------------------------------------------------------

	public function toggle_status($id)
	{
		$agent = $this->M_Agent->get_by_id($id);
		if (!$agent) show_404();

		$this->M_Agent->toggle_status($id);
		$this->_log('toggle_agent_status', 'Toggle status agen ID: ' . $id);
		$this->session->set_flashdata('success', 'Status agen berhasil diubah.');
		redirect('agents');
	}

	// ----------------------------------------------------------------
	// DELETE (soft delete)
	// ----------------------------------------------------------------

	public function delete($id)
	{
		$agent = $this->M_Agent->get_by_id($id);
		if (!$agent) show_404();

		$user_count = $this->M_User->count_by_agent($id);
		if ($user_count > 0) {
			$this->session->set_flashdata(
				'error',
				'Agen masih memiliki ' . $user_count . ' user aktif. Hapus atau pindahkan user terlebih dahulu.'
			);
			redirect('agents/detail/' . $id);
		}

		$this->M_Agent->soft_delete($id);
		$this->_log('delete_agent', 'Menghapus agen: ' . $agent->code);
		$this->session->set_flashdata('success', 'Agen berhasil dihapus.');
		redirect('agents');
	}

	// ================================================================
	// PRIVATE HELPERS
	// ================================================================

	// private function _check_access()
	// {
	// 	$sess = $this->session->userdata('user');
	// 	if (!in_array($sess['role_slug'], $this->allowed_roles)) {
	// 		show_error('Akses ditolak.', 403);
	// 	}
	// }

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
}
