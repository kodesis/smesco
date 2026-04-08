<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends Authenticated_Controller
{
	protected $allowed_roles = ['admin-kribo', 'finance-kribo', 'superadmin'];

	public function __construct()
	{
		parent::__construct();

		$this->load->model(['M_Pricelist']);
		$this->load->library('form_validation');
		$this->load->helper('url');
	}

	public function index()
	{
		show_404();
	}

	public function pricelist()
	{
		$this->_check_access();

		$sess = $this->session->userdata('user');
		$search = $this->input->get('q');
		$status = $this->input->get('status');

		$total = $this->M_Pricelist->count_paged($search, $status);

		$paginate = $this->paginate($total, 15, [
			'q' => $search,
			'status' => $status,
		]);

		$pricelists = $this->M_Pricelist->get_paged_pricelist($paginate['per_page'], $paginate['offset'], $search, $status);

		$data = array_merge([
			'title' => 'Pricelist',
			'search' => $search,
			'status' => $status,
			'total' => $total,
			'pricelists' => $pricelists
		], $paginate);

		$this->render('app/pages/master/pricelist/index', $data);
	}

	public function create_pricelist()
	{
		$this->_check_access();

		// 1. Cek apakah ada request POST (Form disubmit)
		if ($this->input->server('REQUEST_METHOD') === 'POST') {

			// 2. Load Library Validation (kalau belum di autoload)
			$this->load->library('form_validation');

			// 3. Set Rules Validasi Form
			$this->form_validation->set_rules('origin', 'Kota Asal', 'required|trim');
			$this->form_validation->set_rules('destination', 'Kota Tujuan', 'required|trim');
			$this->form_validation->set_rules('service_type_id', 'Tipe Layanan', 'required|numeric');
			$this->form_validation->set_rules('min_weight_kg', 'Minimum Berat', 'required|numeric');
			$this->form_validation->set_rules('price_kribo', 'Harga Kribo (Modal)', 'required|numeric');
			$this->form_validation->set_rules('price_smesco', 'Harga Smesco (Jual)', 'required|numeric');

			// 4. Jalankan Validasi
			if ($this->form_validation->run() !== FALSE) {

				$origin = $this->input->post('origin', TRUE);
				$destination = $this->input->post('destination', TRUE);
				$service_type_id = $this->input->post('service_type_id', TRUE);

				// 5. Logic Bisnis: Cek Duplikasi Kombinasi (Origin + Dest + Service)
				// (Lu bisa pindahin query ini ke M_Pricelist biar lebih rapi, tapi gua taruh sini buat contoh cepat)
				$cek_duplikat = $this->db->get_where('pricelist', [
					'origin' => $origin,
					'destination' => $destination,
					'service_type_id' => $service_type_id
				])->num_rows();

				if ($cek_duplikat > 0) {
					// Kalau duplikat, tolak dan kembalikan error
					$this->session->set_flashdata('error', 'Gagal! Kombinasi Rute dan Layanan ini sudah ada di database.');
				} else {

					// 6. Siapkan data untuk insert
					$insert_data = [
						'origin'          => $origin,
						'destination'     => $destination,
						'service_type_id' => $service_type_id,
						'price_kribo'     => $this->input->post('price_kribo', TRUE),
						'price_smesco'    => $this->input->post('price_smesco', TRUE),
						'min_weight_kg'   => $this->input->post('min_weight_kg', TRUE),
						'is_active'       => $this->input->post('is_active') ? 1 : 0,
						// created_at & updated_at otomatis diisi oleh MySQL karena setelan DB lu
					];

					// 7. Mulai Database Transaction
					$this->db->trans_start();

					// Insert ke tabel pricelist
					$this->db->insert('pricelist', $insert_data);

					// Selesaikan Transaction
					$this->db->trans_complete();

					// 8. Cek Status Transaction
					if ($this->db->trans_status() === FALSE) {
						// Kalau ada error query / DB down
						$this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat menyimpan data Pricelist.');
					} else {
						// Kalau sukses, redirect ke halaman list pricelist
						$this->session->set_flashdata('success', 'Data Pricelist rute ' . $origin . ' ke ' . $destination . ' berhasil ditambahkan!');
						redirect('master/pricelist');
						return; // Stop eksekusi agar tidak render view lagi
					}
				}
			}
		}

		// Default load view (Terpanggil saat awal load halaman ATAU saat validasi form gagal)
		$data = [
			'title'    => 'Tambah Pricelist',
			'cities'   => $this->M_Pricelist->get_cities(),
			'services' => $this->M_Pricelist->get_services(),
		];

		$this->render('app/pages/master/pricelist/create', $data);
	}

	// ----------------------------------------------------------------
	// DETAIL PRICELIST
	// ----------------------------------------------------------------

	public function detail_pricelist($id)
	{
		$this->_check_access();

		$pricelist = $this->M_Pricelist->get_detail($id);
		if (!$pricelist) show_404();

		$data = [
			'title'     => 'Detail Pricelist',
			'pricelist' => $pricelist,
		];

		$this->render('app/pages/master/pricelist/detail', $data);
	}

	// ----------------------------------------------------------------
	// EDIT PRICELIST
	// ----------------------------------------------------------------

	public function edit_pricelist($id)
	{
		$this->_check_access();

		$pricelist = $this->M_Pricelist->get_detail($id);
		if (!$pricelist) show_404();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('origin', 'Kota Asal', 'required|trim');
			$this->form_validation->set_rules('destination', 'Kota Tujuan', 'required|trim');
			$this->form_validation->set_rules('service_type_id', 'Tipe Layanan', 'required|numeric');
			$this->form_validation->set_rules('price_kribo', 'Harga Kribo (Modal)', 'required|numeric');
			$this->form_validation->set_rules('price_smesco', 'Harga Smesco (Jual)', 'required|numeric');
			$this->form_validation->set_rules('min_weight_kg', 'Minimum Berat', 'required|numeric');

			if ($this->form_validation->run() !== FALSE) {
				$origin = $this->input->post('origin', TRUE);
				$destination = $this->input->post('destination', TRUE);
				$service_type_id = $this->input->post('service_type_id', TRUE);

				// Cek Duplikasi (Kecuali ID yang sedang diedit)
				$cek_duplikat = $this->db->get_where('pricelist', [
					'origin' => $origin,
					'destination' => $destination,
					'service_type_id' => $service_type_id,
					'id !=' => $id // Abaikan pricelist ini sendiri
				])->num_rows();

				if ($cek_duplikat > 0) {
					$this->session->set_flashdata('error', 'Gagal! Kombinasi Rute dan Layanan ini sudah digunakan.');
				} else {
					$update_data = [
						'origin'          => $origin,
						'destination'     => $destination,
						'service_type_id' => $service_type_id,
						'price_kribo'     => $this->input->post('price_kribo', TRUE),
						'price_smesco'    => $this->input->post('price_smesco', TRUE),
						'min_weight_kg'   => $this->input->post('min_weight_kg', TRUE),
						'is_active'       => $this->input->post('is_active') ? 1 : 0,
					];

					$this->db->trans_start();
					$this->db->where('id', $id)->update('pricelist', $update_data);
					$this->db->trans_complete();

					if ($this->db->trans_status() === FALSE) {
						$this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat mengupdate Pricelist.');
					} else {
						$this->session->set_flashdata('success', 'Pricelist berhasil diperbarui!');
						redirect('master/detail_pricelist/' . $id);
						return;
					}
				}
			}
		}

		$data = [
			'title'     => 'Edit Pricelist',
			'pricelist' => $pricelist,
			'cities'    => $this->M_Pricelist->get_cities(),
			'services'  => $this->M_Pricelist->get_services(),
		];

		$this->render('app/pages/master/pricelist/edit', $data);
	}

	// ----------------------------------------------------------------
	// TOGGLE STATUS PRICELIST
	// ----------------------------------------------------------------

	public function toggle_status_pricelist($id)
	{
		$this->_check_access();
		$pricelist = $this->M_Pricelist->get_detail($id);
		if (!$pricelist) show_404();

		// Toggle value (jika 1 jadi 0, jika 0 jadi 1)
		$new_status = $pricelist->is_active == 1 ? 0 : 1;
		$this->db->where('id', $id)->update('pricelist', ['is_active' => $new_status]);

		$this->session->set_flashdata('success', 'Status Pricelist berhasil diubah.');
		redirect('master/pricelist');
	}

	// ----------------------------------------------------------------
	// DELETE PRICELIST
	// ----------------------------------------------------------------

	public function delete_pricelist($id)
	{
		$this->_check_access();
		$pricelist = $this->M_Pricelist->get_detail($id);
		if (!$pricelist) show_404();

		$this->db->where('id', $id)->delete('pricelist');

		$this->session->set_flashdata('success', 'Data Pricelist berhasil dihapus.');
		redirect('master/pricelist');
	}

	// ----------------------------------------------------------------
	// IMPORT EXCEL PRICELIST
	// ----------------------------------------------------------------
	public function import_excel_pricelist()
	{
		$this->_check_access();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$upload_path = FCPATH . 'uploads/temp/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true);
			}

			$config['upload_path']   = $upload_path;
			$config['allowed_types'] = 'xls|xlsx|csv';
			$config['max_size']      = 5120; // Maksimal 5MB
			$config['encrypt_name']  = TRUE;

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('file_excel')) {
				$fileData = $this->upload->data();
				$filePath = $fileData['full_path'];
				require_once FCPATH . 'vendor/autoload.php';

				try {
					$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
					$sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

					$preview_data = [];

					for ($i = 2; $i <= count($sheetData); $i++) {
						$origin       = trim($sheetData[$i]['A'] ?? '');
						$destination  = trim($sheetData[$i]['B'] ?? '');
						$service_id   = trim($sheetData[$i]['C'] ?? '');
						$price_kribo  = $this->_parse_indo_number($sheetData[$i]['D'] ?? 0);
						$price_smesco = $this->_parse_indo_number($sheetData[$i]['E'] ?? 0);
						$min_weight   = $this->_parse_indo_number($sheetData[$i]['F'] ?? 1);

						if (empty($origin) || empty($destination)) continue;

						// LOGIC PENGECEKAN STATUS
						$existing = $this->db->get_where('pricelist', [
							'origin' => $origin,
							'destination' => $destination,
							'service_type_id' => $service_id
						])->row();

						$status = 'NEW'; // Default
						$diff = [];

						if ($existing) {
							if ($existing->price_kribo != $price_kribo || $existing->price_smesco != $price_smesco) {
								$status = 'UPDATE';
								$diff = ['old_kribo' => $existing->price_kribo, 'old_smesco' => $existing->price_smesco];
							} else {
								$status = 'SKIP'; // Data sama persis
							}
						}

						$preview_data[] = [
							'origin'       => $origin,
							'destination'  => $destination,
							'service_id'   => $service_id,
							'price_kribo'  => $price_kribo,
							'price_smesco' => $price_smesco,
							'min_weight'   => $min_weight,
							'status'       => $status,
							'diff'         => $diff
						];
					}

					// Simpan ke Session buat diproses nanti
					$this->session->set_userdata('temp_import_pricelist', $preview_data);
					unlink($filePath); // Hapus file temp

					$data['title']   = 'Preview Import Pricelist';
					$data['preview'] = $preview_data;
					$this->render('app/pages/master/pricelist/pricelist_import_preview', $data);
					return;
				} catch (Exception $e) {
					$this->session->set_flashdata('error', 'Gagal membaca file.');
					redirect('master/pricelist');
				}
			}
		}
	}

	public function confirm_import_pricelist()
	{
		$this->_check_access();
		$data_import = $this->session->userdata('temp_import_pricelist');

		if (empty($data_import)) {
			redirect('master/pricelist');
		}

		$this->db->trans_start();
		foreach ($data_import as $item) {
			if ($item['status'] == 'SKIP') continue;

			$db_data = [
				'origin'          => $item['origin'],
				'destination'     => $item['destination'],
				'service_type_id' => $item['service_id'],
				'price_kribo'     => $item['price_kribo'],
				'price_smesco'    => $item['price_smesco'],
				'min_weight_kg'   => $item['min_weight'],
				'is_active'       => 1,
				'created_by'      => $this->session->userdata('user')['id']
			];

			if ($item['status'] == 'NEW') {
				$this->db->insert('pricelist', $db_data);
			} else {
				$this->db->where([
					'origin' => $item['origin'],
					'destination' => $item['destination'],
					'service_type_id' => $item['service_id']
				]);
				$this->db->update('pricelist', $db_data);
			}
		}
		$this->db->trans_complete();

		$this->session->unset_userdata('temp_import_pricelist');
		$this->session->set_flashdata('success', 'Data pricelist berhasil disinkronkan!');
		redirect('master/pricelist');
	}

	// ----------------------------------------------------------------
	// DOWNLOAD TEMPLATE EXCEL
	// ----------------------------------------------------------------
	public function download_template_pricelist()
	{
		$this->_check_access();
		$this->load->helper('download');

		// Header Baru: D = Modal, E = Jual
		$csv_content = "Origin,Destination,Service_Type_ID,Price_Kribo,Price_Smesco,Min_Weight_KG\n";

		// Contoh Data: Jakarta-Batam
		$csv_content .= "Jakarta,Batam,1,39000,42000,10.00\n";
		$csv_content .= "Jakarta,Medan,1,35000,38500,10.00\n";

		force_download('Template_Import_Pricelist_Smesco.csv', $csv_content);
	}

	// ----------------------------------------------------------------
	// AJAX: Cek Harga Pricelist
	// ----------------------------------------------------------------
	public function ajax_cek_harga()
	{
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		}

		$origin          = $this->input->post('origin');
		$destination     = $this->input->post('destination');
		$service_type_id = $this->input->post('service_type_id');

		$pricelist = $this->db->get_where('pricelist', [
			'origin'          => $origin,
			'destination'     => $destination,
			'service_type_id' => $service_type_id,
			'is_active'       => 1
		])->row();

		if ($pricelist) {
			$sess = $this->session->userdata('user');

			// Data dasar yang dikirim ke frontend
			$res_data = [
				'price_per_kg'  => $pricelist->price_smesco, // Kita lempar price_smesco sebagai 'price_per_kg'
				'min_weight_kg' => $pricelist->min_weight_kg
			];

			// OPSIONAL: Kalau yang nge-cek adalah Superadmin, kasih info tambahan harga modal
			if ($sess['role_slug'] === 'superadmin') {
				$res_data['price_modal_kribo'] = $pricelist->price_kribo;
			}

			echo json_encode([
				'status' => true,
				'data'   => $res_data
			]);
		} else {
			echo json_encode([
				'status'  => false,
				'message' => 'Harga tidak ditemukan untuk rute dan layanan ini.'
			]);
		}
	}

	// ================================================================
	// MASTER SERVICES (Tipe Layanan)
	// ================================================================

	public function services()
	{
		$this->_check_access();

		// Anggap lu punya fungsi get_all_services di M_Pricelist atau M_Service
		$services = $this->db->order_by('id', 'DESC')->get('service_types')->result();

		$data = [
			'title'    => 'Master Layanan',
			'services' => $services
		];

		$this->render('app/pages/master/services/index', $data);
	}

	public function create_service()
	{
		$this->_check_access();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->load->library('form_validation');

			// is_unique[tabel.kolom] otomatis ngecek duplikat di CI3
			$this->form_validation->set_rules('code', 'Kode Layanan', 'required|trim|max_length|is_unique[service_types.code]', [
				'is_unique' => 'Kode Layanan ini sudah terdaftar!'
			]);
			$this->form_validation->set_rules('name', 'Nama Layanan', 'required|trim|max_length');
			$this->form_validation->set_rules('description', 'Deskripsi', 'trim');

			if ($this->form_validation->run() !== FALSE) {
				$insert_data = [
					'code'        => strtoupper($this->input->post('code', TRUE)),
					'name'        => $this->input->post('name', TRUE),
					'description' => $this->input->post('description', TRUE),
					'is_active'   => $this->input->post('is_active') ? 1 : 0,
				];

				$this->db->insert('service_types', $insert_data);
				$this->session->set_flashdata('success', 'Layanan baru berhasil ditambahkan!');
				redirect('master/services');
				return;
			}
		}

		$data = ['title' => 'Tambah Layanan'];
		$this->render('app/pages/master/services/create', $data);
	}

	public function edit_service($id)
	{
		$this->_check_access();

		$service = $this->db->get_where('service_types', ['id' => $id])->row();
		if (!$service) show_404();

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$this->load->library('form_validation');

			$post_code = trim($this->input->post('code', TRUE));

			// 1. Perbaikan: Tambahkan di batas max_length
			$rules_code = ['required', 'trim', 'max_length[20]'];

			// Jika code diubah, tambahkan rule is_unique ke dalam array
			if (strtolower($post_code) !== strtolower($service->code)) {
				$rules_code[] = 'is_unique[service_types.code]';
			}

			$this->form_validation->set_rules('code', 'Kode Layanan', $rules_code, [
				'is_unique' => 'Kode Layanan ini sudah digunakan oleh layanan lain!'
			]);

			// 2. Perbaikan: Tambahkan di batas max_length
			$this->form_validation->set_rules('name', 'Nama Layanan', ['required', 'trim', 'max_length[100]']);
			$this->form_validation->set_rules('description', 'Deskripsi', ['trim']);

			if ($this->form_validation->run() !== FALSE) {
				$update_data = [
					'code'        => strtoupper($post_code),
					'name'        => $this->input->post('name', TRUE),
					'description' => $this->input->post('description', TRUE),
					'is_active'   => $this->input->post('is_active') ? 1 : 0,
				];

				$this->db->where('id', $id)->update('service_types', $update_data);
				$this->session->set_flashdata('success', 'Data Layanan berhasil diperbarui!');
				redirect('master/services');
				return;
			}
		}

		$data = [
			'title'   => 'Edit Layanan',
			'service' => $service
		];
		$this->render('app/pages/master/services/edit', $data);
	}

	public function toggle_status_service($id)
	{
		$this->_check_access();
		$service = $this->db->get_where('service_types', ['id' => $id])->row();
		if (!$service) show_404();

		$new_status = $service->is_active == 1 ? 0 : 1;
		$this->db->where('id', $id)->update('service_types', ['is_active' => $new_status]);

		$this->session->set_flashdata('success', 'Status Layanan berhasil diubah.');
		redirect('master/services');
	}

	public function delete_service($id)
	{
		$this->_check_access();
		$service = $this->db->get_where('service_types', ['id' => $id])->row();
		if (!$service) show_404();

		// Opsional: Cek apakah service ini sedang dipakai di tabel pricelist
		$is_used = $this->db->get_where('pricelist', ['service_type_id' => $id])->num_rows();
		if ($is_used > 0) {
			$this->session->set_flashdata('error', 'Gagal dihapus! Layanan ini sedang digunakan pada data Pricelist.');
		} else {
			$this->db->where('id', $id)->delete('service_types');
			$this->session->set_flashdata('success', 'Data Layanan berhasil dihapus.');
		}

		redirect('master/services');
	}

	// Fungsi helper internal untuk membersihkan format angka Indonesia
	private function _parse_indo_number($str)
	{
		if (empty($str)) return 0;
		// Hilangkan titik (ribuan) jika ada
		$str = str_replace('.', '', $str);
		// Ubah koma jadi titik (desimal) jika ada
		$str = str_replace(',', '.', $str);
		return floatval($str);
	}
}
