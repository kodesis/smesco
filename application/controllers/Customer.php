<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'pagination']);
        $this->load->helper(['string', 'url', 'date']);
        $this->load->model('M_Customer');

        if (!$this->session->userdata('is_logged_in')) {

            $this->session->set_userdata('last_page', current_url());

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

            redirect('auth');
        } else {
            $url = "customer/index";
            $this->checkAccess($url);
        }
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

        $per_page = ($this->input->post('show_per_page')) ? trim($this->input->post('show_per_page')) : (($this->session->userdata('show_per_page')) ? $this->session->userdata('show_per_page') : '10');
        if ($per_page === null) $per_page = $this->session->userdata('show_per_page');
        else $this->session->set_userdata('show_per_page', $per_page);

        // print_r($per_page)

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_customer')) ? $this->session->userdata('search_customer') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_customer');
        else $this->session->set_userdata('search_customer', $keyword);

        $config = [
            'base_url' => site_url('customer/index'),
            'total_rows' => $this->M_Customer->count($keyword),
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
            "title" => "Customer",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "customer",
            "pages" => "pages/customer/v_customer",
            "customers" => $this->M_Customer->listCustomerPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        $this->load->view('pages/index', $data);
    }

    public function store()
    {
        $url = "customer/index";
        $this->checkAccess($url);

        $nama_customer = $this->input->post('nama_customer');
        $slug = url_title($nama_customer, 'dash', true);

        $data = [
            'nama_customer' => $nama_customer,
            'alamat_customer' => $this->input->post('alamat_customer'),
            'telepon_customer' => $this->input->post('telepon_customer'),
            'status_customer' => $this->input->post('status_customer'),
            'slug' => $slug,
        ];

        $old_slug = $this->uri->segment(4);
        if ($old_slug) {
            $this->M_Customer->update($data, $old_slug);

            $this->session->set_flashdata('message_name', 'The customer has been successfully updated.');
        } else {
            if ($this->M_Customer->is_available($slug)) {
                $this->session->set_flashdata('message_error', 'Customer ' . $nama_customer . ' sudah ada.');
            } else {
                $this->M_Customer->insert($data);

                $this->session->set_flashdata('message_name', 'The customer has been successfully added.');
            }
        }

        redirect("customer");
    }

    public function formEdit()
    {
        $id = $this->input->post('id');

        $data = $this->M_Customer->show($id);

        $url_form = base_url('customer/updateData/' . $id);

        $nama_customer = $data['nama_customer'];
        $telepon_customer = $data['telepon_customer'];
        $alamat_customer = $data['alamat_customer'];

        $output = "<form method='POST' action='$url_form'>";
        $output .= "<div class='row'>
                        <div class='col-md-12 col-12'>
                            <div class='mb-3'>
                                <label class='form-label'>Nama</label>
                                <input type='text' name='nama_customer' class='form-control' value='$nama_customer'>
                            </div>
                        </div>";

        $output .= "
                <div class='col-md-12 col-12'>
                    <div class='mb-3'>
                        <label class='form-label'>No. HP</label>
                        <input type='text' class='form-control' name='telepon_customer' value='$telepon_customer'>
                    </div>
                </div>";

        $output .= "
                <div class='col-md-12 col-12'>
                    <div class='mb-3'>
                        <label class='form-label'>Alamat</label>
                        <textarea class='form-control' name='alamat_customer'>$alamat_customer</textarea>
                    </div>
                </div>
            </div>";

        $output .= "<div class='row mt-2'><div class='col-12 text-end'><button type='submit' class='btn btn-primary btn-submit'>Perbarui</button></div></div>";
        $output .= "</form>";

        echo $output;
    }

    public function updateData($id)
    {
    }
}
