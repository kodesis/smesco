<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_Setting', 'M_Partner']);
        $this->load->library(['session', 'pagination', 'form_validation']);
        $this->load->helper(['string', 'url', 'date']);

        if (!$this->session->userdata('is_logged_in')) {

            $this->session->set_userdata('last_page', current_url());

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');
            redirect('auth');
        } else {
            if ($this->session->userdata('role_id') != '1') {
                redirect('auth/forbidden');
            }
        }
    }

    public function menu()
    {
        $keyword = $this->get_search_keyword('menu');
        $total_rows = $this->M_Setting->countMenu($keyword);
        $config = $this->configure_pagination('setting/menu', $total_rows);

        $this->pagination->initialize($config);
        $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;

        $data = [
            "title" => "Menu",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "setting",
            "pages" => "pages/setting/v_menu",
            "menus" => $this->M_Setting->listMenuPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $total_rows,
            "per_page" => $config['per_page'],
        ];

        $this->load->view('pages/index', $data);
    }

    public function user()
    {
        $keyword = $this->get_search_keyword('user');
        $total_rows = $this->M_Setting->countUser($keyword);
        $config = $this->configure_pagination('setting/user', $total_rows);

        $this->pagination->initialize($config);
        $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;

        $data = [
            "title" => "User",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "setting",
            "pages" => "pages/setting/v_user",
            "users" => $this->M_Setting->listUserPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $total_rows,
            "per_page" => $config['per_page'],
            "partners" => $this->M_Partner->list_active_partner(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function getDataUser()
    {
        $results = $this->M_User->getDataUser();
        $data = [];

        $no = 1;
        foreach ($results as $r) {
            $btn_edit = base_url('integrasiap2/invoice/' . $r->user_id);
            $btn_access = base_url('setting/user_access/' . $r->user_id);
            $btn_toggle_status = base_url('setting/' . ($r->is_active == "1" ? "hold" : "activate") . '/' . $r->user_id);
            $btn_border = $r->is_active == "1" ? "danger" : "primary";
            $user_status = $r->is_active == "1" ? "Active" : "Non-active";
            $user_status_icon = $r->is_active == "1" ? "Hold" : "Activate";

            $row = [
                $no++,
                $r->user_name,
                $r->user_id,
                $r->user_phone,
                $user_status,
                "<a href='$btn_edit' class='btn btn-outline-info btn-sm'>Edit</a>
                <a href='$btn_access' class='btn btn-outline-success btn-sm'>Access menu</a>
                <a href='$btn_toggle_status' class='btn btn-outline-$btn_border btn-sm btn-process'>$user_status_icon</a>"
            ];
            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_User->count_all_data(),
            "recordsFiltered" => $this->M_User->count_filtered_data(),
            "data" => $data
        ];
        $this->output->set_content_type('application/json')->set_output(json_encode($output));
    }

    public function user_access($id)
    {
        $data = [
            'title' => 'User Access',
            'pages' => 'pages/setting/v_access',
            'segment' => 'setting',
            'keyword' => '',
            'menu' => $this->M_Setting->get_menus(),
            'user_menu' => $this->M_Setting->getUserMenu($id),
        ];

        $this->load->view('pages/index', $data);
    }

    public function addUser()
    {
        $data = [
            'name' => htmlspecialchars($this->input->post('nama_user')),
            'email' => htmlspecialchars($this->input->post('email_user')),
            'username' => strtolower($this->input->post('username')),
            'phone_number' => strtolower($this->input->post('no_handphone')),
            'image' => 'default.jpg',
            'password' => password_hash(strtolower($this->input->post('username')), PASSWORD_DEFAULT),
            'role_id' => $this->input->post('role'),
            'is_active' => '1',
            'date_created' => time(),
        ];

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;

        $this->db->trans_begin();

        if ($this->M_Setting->createAccount($data)) {
            $this->db->trans_commit();
            $this->session->set_flashdata('message_name', 'Akun sudah berhasil dibuat.');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('message_error', 'Gagal membuat akun. Silahkan coba lagi');
        }

        redirect('setting/user');
    }

    public function update_access($id)
    {
        $access_menu = $this->input->post('input_menu');
        $access_submenu = $this->input->post('input_submenu');

        $access_menu = is_array($access_menu) ? $access_menu : [];
        $access_submenu = is_array($access_submenu) ? $access_submenu : [];

        $formatted_menu = '[' . implode(',', $access_menu) . ']';
        $formatted_submenu = '[' . implode(',', $access_submenu) . ']';

        $data = [
            'access_menu' => $formatted_menu,
            'access_sub_menu' => $formatted_submenu,
        ];

        $this->M_Setting->update_user($data, $id);

        $this->session->set_flashdata('message_name', 'The access updated successfully.');

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function activate($id)
    {
        $data = [
            'is_active' => '1'
        ];

        $this->M_Setting->update_user($data, $id);

        $this->session->set_flashdata('message_name', 'The user account has been activated successfully.');

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function hold($id)
    {
        $data = [
            'is_active' => '0'
        ];

        $this->M_Setting->update_user($data, $id);

        $this->session->set_flashdata('message_name', 'The user account has been activated successfully.');

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function addNewMenu()
    {
        $url = trim($this->input->post('url'));
        $icon = trim($this->input->post('icon'));
        $segment = trim($this->input->post('segment'));

        $cekMenu = $this->M_Setting->isAvailableMenu($url);

        if ($cekMenu) {
            $this->session->set_flashdata('message_error', 'Menu tersebut sudah ada');
            redirect('setting/menu');
        } else {

            $data_parent = [
                'nama_menu' => trim($this->input->post('nama_menu')),
                'url' => $url,
                'icon' => $icon,
                'has_child' => trim($this->input->post('has_child')),
                'controller' => trim($this->input->post('url')),
                'segment' => $segment,
            ];

            $menu_childs = $this->input->post('menu_child');
            $url_childs = $this->input->post('url_child');

            $id_parent = $this->M_Setting->addMenu($data_parent);

            $child_data = [];

            if (is_array($menu_childs)) {
                for ($i = 0; $i < count($menu_childs); $i++) {
                    $menu_child = trim($menu_childs[$i]);
                    $url_child = trim($url_childs[$i]);

                    // Cek apakah menu_child dan url_child tidak kosong
                    if (!empty($menu_child) && !empty($url_child)) {
                        $child_data[] = [
                            'nama_menu' => $menu_child,
                            'url' => $url_child,
                            'parent_id' => $id_parent,
                            'segment' => $segment
                        ];
                    }
                }

                // Jika ada child_data yang valid, maka lakukan insert
                if (!empty($child_data)) {
                    $insert = $this->M_Setting->addChildMenu($child_data);

                    if ($insert) {
                        $this->session->set_flashdata('message_name', 'New menu has been successfully added.');
                        // Setelah itu gunakan fungsi redirect
                        redirect("setting/menu");
                    }
                } else {
                    redirect('setting/menu');
                }
            }
        }
    }


    private function get_search_keyword($type)
    {
        $keyword = $this->input->post('keyword') ? trim($this->input->post('keyword')) : $this->session->userdata('search_' . $type);
        $this->session->set_userdata('search_' . $type, $keyword);
        return $keyword;
    }

    private function configure_pagination($base_url, $total_rows)
    {
        return [
            'base_url' => site_url($base_url),
            'total_rows' => $total_rows,
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
            'attributes' => ['class' => 'page-link'],
            'use_page_numbers' => TRUE,
        ];
    }
}
