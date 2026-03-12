<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Registration extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['string', 'date']);
        $this->load->model(['M_Setting', 'M_Agent', 'M_Partner']);
    }

    public function index()
    {
        if ($this->session->userdata('is_logged_in')) {
            redirect('dashboard');
        }

        $data['title'] = 'Registration';

        $fields = [
            ['nama_agent', 'Nama'],
            ['no_telepon', 'No. Telepon'],
            ['email', 'Email', 'valid_email|is_unique[user.email]', 'The email has already registered'],
            ['provinsi', 'Provinsi'],
            ['kota', 'Kota'],
            ['kecamatan', 'Kecamatan'],
            ['kelurahan', 'Kelurahan'],
            ['rt', 'RT'],
            ['rw', 'RW'],
            ['alamat', 'Alamat'],
            ['google_maps', 'Google Maps URL']
        ];

        foreach ($fields as $field) {
            $this->form_validation->set_rules(
                $field[0],
                $field[1],
                isset($field[2]) ? "required|trim|$field[2]" : 'required|trim',
                isset($field[3]) ? ['is_unique' => $field[3]] : []
            );
        }

        if ($this->form_validation->run() == false) {
            $this->load->view('pages/registration', $data);
        } else {
            $formData = [
                'nama_agent' => htmlspecialchars($this->input->post('nama_agent', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'no_telepon' => $this->input->post('no_telepon')
            ];

            echo '<pre>', print_r($this->input->post()), '</pre>';
            exit;
        }
    }

    public function getProvinsi()
    {
        $provinsi = $this->M_Setting->getProvinsi();

        $show = '<option value="">:: Pilih provinsi</option>';
        foreach ($provinsi as $p) :
            $selected = (set_value('provinsi') == $p->id) ? 'selected' : '';
            $show .= "<option $selected value='$p->id'>$p->nama_provinsi</option>";
        endforeach;

        echo $show;
    }

    public function getKota()
    {
        $id = $this->input->post('provinsi');
        $kota = $this->M_Setting->getKota($id);

        $show = '<option value="">:: Pilih kota/kabupaten</option>';
        foreach ($kota as $p) :
            $show .= "<option value='$p->id' data-nama='$p->nama_kota'>$p->nama_kota</option>";
        endforeach;

        echo $show;
    }

    public function getKecamatan()
    {
        $id = $this->input->post('kota');
        $kecamatan = $this->M_Setting->getKecamatan($id);

        $show = '<option value="">:: Pilih kecamatan</option>';
        $show .= $id;
        foreach ($kecamatan as $p) :
            $show .= "<option value='$p->id'>$p->nama_kecamatan</option>";
        endforeach;

        echo $show;
    }

    public function getKelurahan()
    {
        $id = $this->input->post('kecamatan');
        $kelurahan = $this->M_Setting->getKelurahan($id);
        echo $id;

        $show = '<option value="">:: Pilih kelurahan</option>';
        foreach ($kelurahan as $p) :
            $show .= "<option value='$p->nama_kelurahan'>$p->nama_kelurahan</option>";
        endforeach;

        echo $show;
    }

    public function checkEmail()
    {
        $id = $this->input->post('email');

        $email = $this->db->where('email', $id)->get('user')->num_rows();

        echo $email;
    }

    public function submitRegistrasi()
    {
        // Load library upload dan image_lib
        $this->load->library('upload');
        $this->load->library('image_lib');

        $config['upload_path'] = FCPATH . 'assets/files/data-mitra/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['overwrite'] = TRUE;
        $config['max_size'] = '2048';

        // File yang di-upload
        $files = ['ktp', 'foto_depan', 'foto_dalam', 'npwp'];
        $gambar = [];
        $uploadedFiles = [];

        $time = time();

        foreach ($files as $file) {

            $pathInfo = pathinfo($_FILES[$file]['name']);
            $extension = $pathInfo['extension'];
            $newFileName = $file . '_' . $time . '.' . $extension;

            $config['file_name'] = $newFileName;
            $this->upload->initialize($config);

            if ($this->upload->do_upload($file)) {

                $uploadData = $this->upload->data();
                $fileSize = $uploadData['file_size']; // dalam KB
                $gambar[$file] = $uploadData['file_name'];

                $uploadedFiles[] = $uploadData['full_path'];

                // Cek ukuran file
                if ($fileSize > 200) {
                    $resizeConfig['image_library'] = 'gd2';
                    $resizeConfig['source_image'] = $uploadData['full_path'];
                    $resizeConfig['maintain_ratio'] = TRUE;
                    $resizeConfig['width'] = 800;  // ukuran maksimal lebar gambar
                    $resizeConfig['height'] = 800; // ukuran maksimal tinggi gambar
                    $resizeConfig['quality'] = '70%'; // kualitas gambar jadi 70%

                    $this->image_lib->initialize($resizeConfig);

                    if (!$this->image_lib->resize()) {
                        echo $this->image_lib->display_errors();
                    }

                    clearstatcache(); // Bersihkan cache file size
                    $resizedFileSize = filesize($uploadData['full_path']) / 1024; // Convert ke KB


                    if ($resizedFileSize > 200) {
                        $resizeConfig['quality'] = '50%';
                        $this->image_lib->initialize($resizeConfig);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }
                    }
                }
            } else {
                $error = $this->upload->display_errors();
                echo "Upload gagal untuk $file: $error";
                return;
            }
        }

        // Proses penyimpanan data ke database
        $provinsi = $this->M_Setting->getProvinsiById($this->input->post('provinsi'))['nama_provinsi'];
        $kota = $this->M_Setting->getKotaById($this->input->post('kota'))['nama_kota'];
        $kecamatan = $this->M_Setting->getKecamatanById($this->input->post('kecamatan'))['nama_kecamatan'];
        $kelurahan = $this->M_Setting->getKelurahanById($this->input->post('kelurahan'))['nama_kelurahan'];

        $data = [
            'jenis_pengajuan' => trim($this->input->post('jenis_pengajuan')),
            'nama_pendaftar' => trim($this->input->post('nama_pendaftar')),
            'no_handphone' => trim($this->input->post('no_handphone')),
            'no_handphone_alternatif' => trim($this->input->post('no_handphone_alternatif')),
            'alamat_email' => trim($this->input->post('alamat_email')),
            'sumber_info' => $this->input->post('sumber_info'),
            'lokasi' => $this->input->post('lokasi'),
            'jenis_bangunan' => $this->input->post('jenis_bangunan'),
            'status_bangunan' => $this->input->post('status_bangunan'),
            'usaha_lain' => $this->input->post('usaha_lain'),
            'provinsi' => $provinsi,
            'kota' => $kota,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
            'id_kelurahan' => $this->input->post('kelurahan'),
            'alamat_lengkap' => trim($this->input->post('alamat_lengkap')),
            'google_maps' => trim($this->input->post('google_maps')),
            'kode_referal' => trim($this->input->post('kode_referal')),
            'ktp' => $gambar['ktp'],
            'foto_depan' => $gambar['foto_depan'],
            'foto_dalam' => $gambar['foto_dalam'],
            'foto_npwp' => $gambar['npwp'],
        ];

        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit;

        $this->db->trans_begin();

        if ($this->M_Partner->insertMitra($data)) {
            $this->db->trans_commit();

            $this->session->set_flashdata('message_name', 'Registrasi berhasil. Terima kasih atas ketertarikan Anda untuk bergabung dengan kami.');
            redirect('home/agent');
        } else {
            $this->db->trans_rollback();

            // Jika query gagal, hapus file yang telah diupload
            foreach ($uploadedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);  // Hapus file
                }
            }

            $this->session->set_flashdata('message_name', 'Gagal input data registrasi. Silahkan coba lagi!');
            redirect('home/agent');
        }
    }
}
