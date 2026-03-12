<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Agent extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'pagination']);
        $this->load->helper(['string', 'url', 'date']);
        $this->load->model('M_Agent');

        if (!$this->session->userdata('is_logged_in')) {

            $this->session->set_userdata('last_page', current_url());

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

            redirect('auth');
        } else {
            $url = "agent/index";
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
        // $url = "agent/index";
        // $this->checkAccess($url);

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_agent')) ? $this->session->userdata('search_agent') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_agent');
        else $this->session->set_userdata('search_agent', $keyword);

        $config = [
            'base_url' => site_url('agent/index'),
            'total_rows' => $this->M_Agent->count($keyword),
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
            "title" => "Agent",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "agent",
            "pages" => "pages/agent/v_agent",
            "agents" => $this->M_Agent->listCustomerPaginate($config["per_page"], $page, $keyword),
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

    // public function updateData($id)
    // {
    //     $data = ['']
    // }
}
