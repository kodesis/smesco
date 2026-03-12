<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Partner extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'pagination', 'form_validation', 'PHPExcel']);
        $this->load->helper(['string', 'url', 'date']);
        $this->load->model(['M_Partner', 'M_Agent']);

        if (!$this->session->userdata('is_logged_in')) {

            $this->session->set_userdata('last_page', current_url());

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

            redirect('auth');
        }
        // else {
        //     $url = "agent/index";
        //     $this->checkAccess($url);
        // }
    }

    private function checkAccess($url)
    {
        $id_function = $this->db->where('url', $url)->get('menu')->row_array();
        $login_menu = $this->M_Setting->getUserMenu($this->session->userdata('username'));

        // Pastikan 'access_menu' terdekode dengan benar
        $access_menu = json_decode($login_menu['access_menu'], true);

        // Cek apakah hasil 'json_decode' adalah array
        if (is_array($access_menu)) {
            if (!in_array($id_function['Id'], $access_menu)) {
                redirect('auth/forbidden');
            }
        } else {
            redirect('auth/forbidden');
        }
    }

    public function index()
    {
        // $url = "agent/index";
        // $this->checkAccess($url);

        $per_page = ($this->input->post('show_per_page')) ? trim($this->input->post('show_per_page')) : (($this->session->userdata('show_per_page')) ? $this->session->userdata('show_per_page') : '10');
        if ($per_page === null) $per_page = $this->session->userdata('show_per_page');
        else $this->session->set_userdata('show_per_page', $per_page);

        $url = "partner/index";
        $this->checkAccess($url);

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_partner')) ? $this->session->userdata('search_partner') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_partner');
        else $this->session->set_userdata('search_partner', $keyword);

        $config = [
            'base_url' => site_url('partner/pendaftaran'),
            'total_rows' => $this->M_Partner->countPartner($keyword),
            'per_page' => $per_page,
            'uri_segment' => 3,
            'num_links' => 1,
            'full_tag_open' => '<ul class="pagination m-0 ms-auto">',
            'full_tag_close' => '</ul>',
            'prev_link' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg> prev',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',
            'prev_tag_open_disabled' => '<li class="page-item disabled">',
            'prev_tag_close_disabled' => '</li>',

            'next_link' => 'next <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M9 6l6 6l-6 6" /></svg>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',
            'next_tag_open_disabled' => '<li class="page-item disabled">',
            'next_tag_close_disabled' => '</li>',

            'first_link' => 'First',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',

            'last_link' => 'Last',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',

            'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close' => '</a></li>',

            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',

            'attributes' => array('class' => 'page-link'),

            'use_page_numbers' => TRUE,
        ];

        $this->pagination->initialize($config);

        $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;

        $data = [
            "title" => "Partner",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "partner",
            "pages" => "pages/partner/v_partner",
            "registrations" => $this->M_Partner->listPartnerPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        $this->load->view('pages/index', $data);
    }

    public function store()
    {
        $url = "agent/index";
        $this->checkAccess($url);

        $nama_agent = $this->input->post('nama_agent');
        $slug = url_title($nama_agent, 'dash', true);

        $data = [
            'nama_agent' => $nama_agent,
            'alamat_agent' => $this->input->post('alamat_agent'),
            'telepon_agent' => $this->input->post('telepon_agent'),
            'status_agent' => $this->input->post('status_agent'),
            'origin' => $this->input->post('origin'),
            'slug' => $slug,
        ];

        // print_r($data);
        // exit;

        $old_slug = $this->uri->segment(3);

        if ($old_slug) {
            $this->M_Agent->update($data, $old_slug);

            $this->session->set_flashdata('message_name', 'The customer has been successfully updated.');
        } else {
            if ($this->M_Agent->is_available($slug)) {
                if ($slug == $old_slug) {
                    $this->session->set_flashdata('message_error', 'Customer ' . $nama_agent . ' sudah ada.');
                }
            } else {
                $this->M_Agent->insert($data);

                $this->session->set_flashdata('message_name', 'The customer has been successfully added.');
            }
        }

        redirect("agent");
    }

    public function formEdit()
    {
        $id = $this->input->post('id');

        $data = $this->M_Agent->show($id);

        $url_form = base_url('agent/store/' . $id);

        $nama_agent = $data['nama_agent'];
        $telepon_agent = $data['telepon_agent'];
        $alamat_agent = $data['alamat_agent'];
        $origin = $data['origin'];

        $output = "<form method='POST' action='$url_form'>";
        $output .= "<div class='row'>
                        <div class='col-md-12 col-12'>
                            <div class='mb-3'>
                                <label class='form-label'>Nama</label>
                                <input type='text' name='nama_agent' class='form-control' value='$nama_agent'>
                            </div>
                        </div>";

        $output .= "
                <div class='col-md-6 col-12'>
                    <div class='mb-3'>
                        <label class='form-label'>Origin</label>
                        <input type='text' class='form-control' name='origin' value='$origin'>
                    </div>
                </div>
                <div class='col-md-6 col-12'>
                    <div class='mb-3'>
                        <label class='form-label'>No. HP</label>
                        <input type='text' class='form-control' name='telepon_agent' value='$telepon_agent'>
                    </div>
                </div>";

        $output .= "
                <div class='col-md-12 col-12'>
                    <div class='mb-3'>
                        <label class='form-label'>Alamat</label>
                        <textarea class='form-control' name='alamat_agent'>$alamat_agent</textarea>
                    </div>
                </div>
            </div>";

        $output .= "<div class='row mt-2'><div class='col-12 text-end'><button type='submit' class='btn btn-primary btn-submit'>Perbarui</button></div></div>";
        $output .= "</form>";

        echo $output;
    }

    public function pendaftaran()
    {
        $url = "partner/index";
        $this->checkAccess($url);

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_pendaftaran')) ? $this->session->userdata('search_pendaftaran') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_pendaftaran');
        else $this->session->set_userdata('search_pendaftaran', $keyword);

        $config = [
            'base_url' => site_url('partner/pendaftaran'),
            'total_rows' => $this->M_Partner->countPendaftaran($keyword),
            'per_page' => 10,
            'uri_segment' => 3,
            'num_links' => 1,
            'full_tag_open' => '<ul class="pagination m-0 ms-auto">',
            'full_tag_close' => '</ul>',
            'prev_link' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg> prev',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',
            'prev_tag_open_disabled' => '<li class="page-item disabled">',
            'prev_tag_close_disabled' => '</li>',

            'next_link' => 'next <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M9 6l6 6l-6 6" /></svg>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',
            'next_tag_open_disabled' => '<li class="page-item disabled">',
            'next_tag_close_disabled' => '</li>',

            'first_link' => 'First',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',

            'last_link' => 'Last',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',

            'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close' => '</a></li>',

            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',

            'attributes' => array('class' => 'page-link'),

            'use_page_numbers' => TRUE,
        ];

        $this->pagination->initialize($config);

        $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;

        $data = [
            "title" => "Pendaftaran",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "partner",
            "pages" => "pages/partner/v_pendaftaran",
            "registrations" => $this->M_Partner->listPendaftaranPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        // echo '<pre>';
        // print_r($data['registrations']);
        // echo '</pre>';
        // exit;

        $this->load->view('pages/index', $data);
    }

    public function review()
    {
        $id = $this->input->post('id');
        $nama = $this->input->post('nama');

        $data = [
            'reg' => $this->M_Partner->getPendaftaran($id)
        ];

        // echo $nama;
        $this->load->view('pages/partner/v_review', $data);
    }

    public function processReview($id)
    {
        $hasil_review = ucfirst($this->input->post('hasil_review'));

        if ($this->input->post('hasil_review') == 'diterima') {
            // $pendaftar = $this->M_Partner->getPartnerById($id);

            $max_num = $this->M_Partner->selectMaxGerai();

            // $kode = "KRX" . $pendaftar['id_kelurahan'];
            $kode = "KRX";

            if (!$max_num['max']) {
                $bilangan = 1;
            } else {
                $bilangan = $max_num['max'] + 1;
            }

            $no_urut = sprintf("%04d", $bilangan);
            $no_gerai = $kode . $no_urut;
        } else {
            $no_urut = NULL;
            $no_gerai = NULL;
        }

        $data = [
            'hasil_review' => $this->input->post('hasil_review'),
            'no_urut' => $no_urut,
            'kode_gerai' => $no_gerai,
            'has_account' => '0'
        ];

        $this->db->trans_begin();

        if ($this->M_Partner->updatePartner($id, $data)) {
            $this->db->trans_commit();
            $this->session->set_flashdata("message_name", "Pengajuan keagenan telah $hasil_review .");
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata("message_error", "Pengajuan keagenan telah $hasil_review.");
        }

        redirect('partner/pendaftaran');
    }

    public function topupSaldo()
    {
        $id = $this->input->post('id');

        $partner = $this->M_Partner->getPartnerById($id);

        $data = [
            'partner' => $partner
        ];

        $this->load->view('pages/partner/v_topup_saldo', $data);
    }

    public function createUser($id)
    {
        $partner = $this->M_Partner->getPartnerById($id);

        $data = [
            'name' => htmlspecialchars($partner['nama_pendaftar']),
            'email' => htmlspecialchars($partner['alamat_email']),
            'username' => strtolower($partner['kode_gerai']),
            'phone_number' => strtolower($partner['no_handphone']),
            'image' => 'default.jpg',
            'password' => password_hash(strtolower($partner['kode_gerai']), PASSWORD_DEFAULT),
            'role_id' => '3',
            'is_active' => '1',
            'date_created' => time(),
            'access_menu' => '[2]',
            'partner_id' => $partner['Id']
        ];

        $this->db->trans_begin();

        if ($this->M_Partner->createAccount($data)) {
            $data_update = [
                'has_account' => '1'
            ];

            $this->M_Partner->updatePartner($id, $data_update);
            $this->db->trans_commit();
            $this->session->set_flashdata('message_name', 'Akun sudah berhasil dibuat.');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('message_error', 'Gagal membuat akun. Silahkan coba lagi');
        }

        redirect('partner');
    }

    public function processTopUpSaldo($id)
    {
        $max_num = $this->M_Partner->selectMaxDepositCode();

        $kode = "KRXDPS";

        if (!$max_num['max']) {
            $bilangan = 1;
        } else {
            $bilangan = $max_num['max'] + 1;
        }

        $no_urut_topup = sprintf("%06d", $bilangan);
        $kode_topup = $kode . $no_urut_topup;

        $nominal_topup = $this->convertToNumber($this->input->post('nominal'));

        $saldo = $this->M_Partner->getSaldoAkhirPartner($id)['saldo_akhir'];

        $saldo_baru = $saldo + $nominal_topup;


        $photo = $_FILES['bukti_transfer']['name']; // Nama file 

        // Ambil extension
        $pathInfo = pathinfo($photo);
        $extension = $pathInfo['extension']; // Extension file
        $newPhotoFileName = $kode_topup . '.' . $extension;

        $data = [
            'kode_topup' => $kode_topup,
            'no_urut_topup' => $no_urut_topup,
            'partner_id' => $id,
            'nominal_topup' => $nominal_topup,
            'topup_date' => $this->input->post('tanggal'),
            'saldo' => $saldo_baru,
            'user_topup' => $this->session->userdata('user_id'),
            'bukti_transfer' => $newPhotoFileName,
        ];

        $this->db->trans_begin();

        if ($this->db->insert('deposit', $data)) {

            $config = [
                'upload_path' => FCPATH . 'assets/files/bukti-topup/',
                'allowed_types' => 'JPG|jpg|JPEG|jpeg',
                'overwrite' => TRUE,
                'max_size' => '1200',
                'file_name' => $newPhotoFileName,
            ];

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('bukti_transfer')) {
                $this->db->trans_rollback();

                $this->session->set_flashdata('message_error', 'Gagal upload bukti, topup saldo tidak diproses. Silahkan coba lagi! ' . $this->upload->display_errors());
            } else {
                $this->db->trans_commit();

                $this->session->set_flashdata('message_name', 'Topup saldo berhasil!');
            }
        } else {
            $this->db->trans_rollback();

            $this->session->set_flashdata('message_error', 'Gagal topup saldo. Silahkan coba lagi! ' . $this->upload->display_errors());
        }

        redirect('partner');
    }

    private function convertToNumber($formattedNumber)
    {
        $numberWithoutThousandsSeparator = str_replace('.', '', $formattedNumber);

        // $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);

        // Mengonversi string ke float
        return (float) $numberWithoutThousandsSeparator;
    }
}
