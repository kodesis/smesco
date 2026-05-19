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
		$this->load->helper(['url', 'tabler_icon']);
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
			'tiers'     => ($pricelist->is_tiered) ? $this->db->get_where('pricelist_tiers', ['pricelist_id' => $id])->result() : []
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

					$groups = []; // Kita grupkan berdasarkan rute

					for ($i = 2; $i <= count($sheetData); $i++) {
						$origin       = trim($sheetData[$i]['A'] ?? '');
						$dest         = trim($sheetData[$i]['B'] ?? '');
						$service_id   = trim($sheetData[$i]['C'] ?? '');
						$category     = strtoupper(trim($sheetData[$i]['D'] ?? 'DOMESTIC')); // Kolom Baru

						if (empty($origin) || empty($dest)) continue;

						$key = $origin . '|' . $dest . '|' . $service_id;

						// Jika rute belum ada di grup, inisialisasi master data
						if (!isset($groups[$key])) {
							$groups[$key] = [
								'origin'          => $origin,
								'destination'     => $dest,
								'service_type_id' => $service_id,
								'category'        => $category,
								'is_tiered'       => ($category === 'INTERNATIONAL') ? 1 : 0,
								'price_kribo'     => ($category === 'DOMESTIC') ? $this->_parse_indo_number($sheetData[$i]['E'] ?? 0) : 0,
								'price_smesco'    => ($category === 'DOMESTIC') ? $this->_parse_indo_number($sheetData[$i]['F'] ?? 0) : 0,
								'min_weight_kg'   => $this->_parse_indo_number($sheetData[$i]['G'] ?? 1),
								'tiers'           => []
							];
						}

						// Simpan harga ke dalam list tiers
						$groups[$key]['tiers'][] = [
							'price_kribo'  => $this->_parse_indo_number($sheetData[$i]['E'] ?? 0),
							'price_smesco' => $this->_parse_indo_number($sheetData[$i]['F'] ?? 0),
							'tier_min'     => $this->_parse_indo_number($sheetData[$i]['H'] ?? 0), // Kolom Baru
							'tier_max'     => $this->_parse_indo_number($sheetData[$i]['I'] ?? 9999), // Kolom Baru
						];
					}

					// Simpan ke Session buat diproses nanti
					$this->session->set_userdata('temp_import_pricelist', $groups);
					unlink($filePath); // Hapus file temp

					$data['title']   = 'Preview Import Pricelist';
					$data['preview'] = $groups;

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
		$groups = $this->session->userdata('temp_import_pricelist');

		// echo '<pre>';
		// print_r($groups);
		// echo '</pre>';
		// exit;

		if (empty($groups)) redirect('master/pricelist');

		$this->db->trans_start();

		foreach ($groups as $g) {
			// 1. Cek apakah master rute sudah ada
			$existing = $this->db->get_where('pricelist', [
				'origin' => $g['origin'],
				'destination' => $g['destination'],
				'service_type_id' => $g['service_type_id']
			])->row();

			$master_data = [
				'origin'          => $g['origin'],
				'destination'     => $g['destination'],
				'service_type_id' => $g['service_type_id'],
				'category'        => $g['category'],
				'is_tiered'       => $g['is_tiered'],
				'min_weight_kg'   => $g['min_weight_kg'],
				'is_active'       => 1,
				// Jika DOMESTIC, ambil harga dari baris pertama tiers
				'price_kribo'     => ($g['is_tiered'] == 0 && isset($g['tiers'][0])) ? $g['tiers'][0]['price_kribo'] : 0,
				'price_smesco'    => ($g['is_tiered'] == 0 && isset($g['tiers'][0])) ? $g['tiers'][0]['price_smesco'] : 0,
			];

			if ($existing) {
				$pricelist_id = $existing->id;
				$this->db->where('id', $pricelist_id)->update('pricelist', $master_data);
			} else {
				$this->db->insert('pricelist', $master_data);
				$pricelist_id = $this->db->insert_id();
			}

			// 2. Jika INTERNATIONAL, kelola Tiers
			if ($g['is_tiered'] == 1) {
				// Hapus tier lama agar data fresh (sinkron)
				$this->db->where('pricelist_id', $pricelist_id)->delete('pricelist_tiers');

				foreach ($g['tiers'] as $t) {
					$this->db->insert('pricelist_tiers', [
						'pricelist_id' => $pricelist_id,
						'min_weight'   => $t['tier_min'],
						'max_weight'   => $t['tier_max'],
						'price_kribo'  => $t['price_kribo'],
						'price_smesco' => $t['price_smesco']
					]);
				}
			}
		}

		$this->db->trans_complete();
		$this->session->unset_userdata('temp_import_pricelist');
		$this->session->set_flashdata('success', 'Data pricelist internasional & domestik berhasil disinkronkan!');
		redirect('master/pricelist');
	}

	// ----------------------------------------------------------------
	// DOWNLOAD TEMPLATE EXCEL
	// ----------------------------------------------------------------
	public function download_template_pricelist()
	{
		$this->_check_access();
		$this->load->helper('download');

		// Header Lengkap A-I
		$csv_content = "Origin,Destination,Service_ID,Category,Price_Kribo,Price_Smesco,Min_Weight_Charge,Tier_Min,Tier_Max\n";

		// Contoh Domestik (Flat)
		$csv_content .= "JAKARTA,BATAM,1,DOMESTIC,35000,42000,10,0,0\n";

		// Contoh Internasional (Tiering) - Masukkan beberapa baris untuk rute yang sama
		$csv_content .= "JAKARTA,SINGAPORE,1,INTERNATIONAL,80000,95000,1,0,1\n";
		$csv_content .= "JAKARTA,SINGAPORE,1,INTERNATIONAL,70000,85000,1,1.01,15\n";
		$csv_content .= "JAKARTA,SINGAPORE,1,INTERNATIONAL,60000,75000,1,15.01,999\n";

		force_download('Template_Pricelist_Smesco_V2.csv', $csv_content);
	}

	// ----------------------------------------------------------------
	// AJAX: Cek Harga Pricelist
	// ----------------------------------------------------------------
	public function ajax_cek_harga()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

		$origin          = $this->input->post('origin');
		$destination     = $this->input->post('destination');
		$service_type_id = $this->input->post('service_type_id');
		$weight          = floatval($this->input->post('weight') ?? 0);

		$pricelist = $this->db->get_where('pricelist', [
			'origin'          => $origin,
			'destination'     => $destination,
			'service_type_id' => $service_type_id,
			'is_active'       => 1
		])->row();

		if ($pricelist) {
			$price_smesco = $pricelist->price_smesco;
			$price_kribo  = $pricelist->price_kribo;
			$category     = $pricelist->category;
			$min_weight   = $pricelist->min_weight_kg;

			// LOGIC TIERING: Jika rute ini pake tiering
			if ($pricelist->is_tiered == 1) {
				// Cari tier yang sesuai dengan berat input
				$tier = $this->db->where('pricelist_id', $pricelist->id)
					->where('min_weight <=', $weight)
					->where('max_weight >=', $weight)
					->get('pricelist_tiers')->row();

				if ($tier) {
					$price_smesco = $tier->price_smesco;
					$price_kribo  = $tier->price_kribo;
				} else {
					// Jika berat melebihi tier tertinggi, ambil tier terakhir (max_weight 9999)
					$last_tier = $this->db->where('pricelist_id', $pricelist->id)
						->order_by('max_weight', 'DESC')
						->get('pricelist_tiers', 1)->row();
					if ($last_tier) {
						$price_smesco = $last_tier->price_smesco;
						$price_kribo  = $last_tier->price_kribo;
					}
				}
			}

			echo json_encode([
				'status' => true,
				'data'   => [
					'price_per_kg'  => $price_smesco,
					'min_weight_kg' => $min_weight,
					'category'      => $category,
					'is_tiered'     => $pricelist->is_tiered
				]
			]);
		} else {
			echo json_encode(['status' => false, 'message' => 'Harga tidak ditemukan.',
				'origin'          => $origin,
				'destination'     => $destination,
				'service_type_id' => $service_type_id,
				'is_active'       => 1]);
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

	// ================================================================
	// AJAX: MASTER WILAYAH (PROVINSI - KELURAHAN)
	// ================================================================

	public function ajax_get_provinsi()
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$provinsi = $this->db->order_by('nama_provinsi', 'ASC')->get('mt_provinsi')->result();
		echo json_encode($provinsi);
	}

	public function ajax_get_kota($provinsi_id)
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$kota = $this->db->where('provinsi_id', $provinsi_id)
			->order_by('nama_kota', 'ASC')
			->get('mt_kota')->result();
		echo json_encode($kota);
	}

	public function ajax_get_kecamatan($kota_id)
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$kecamatan = $this->db->where('kota_id', $kota_id)
			->order_by('nama_kecamatan', 'ASC')
			->get('mt_kecamatan')->result();
		echo json_encode($kecamatan);
	}

	public function ajax_get_kelurahan($kecamatan_id)
	{
		if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
		$kelurahan = $this->db->where('kecamatan_id', $kecamatan_id)
			->order_by('nama_kelurahan', 'ASC')
			->get('mt_kelurahan')->result();
		echo json_encode($kelurahan);
	}

	public function ajax_get_domestic_destination_by_origin()
	{
		$origin = $this->input->get('origin', TRUE);
		if (!$origin) {
			echo json_encode([]);
			return;
		}

		$destinations = $this->db
			->select('destination')
			->where('category', 'DOMESTIC')
			->where('origin', $origin)
			->where('is_active', 1)
			->group_by('destination')
			->order_by('destination', 'ASC')
			->get('pricelist')
			->result();

		echo json_encode($destinations);
	}

	public function ajax_get_international_destination_by_origin()
	{
		$origin = $this->input->get('origin', TRUE);
		if (!$origin) {
			echo json_encode([]);
			return;
		}

		$destinations = $this->db
			->select('destination')
			->where('category', 'INTERNATIONAL')
			->where('origin', $origin)
			->where('is_active', 1)
			->group_by('destination')
			->order_by('destination', 'ASC')
			->get('pricelist')
			->result();

		echo json_encode($destinations);
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
