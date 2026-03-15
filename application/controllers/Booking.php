<?php
defined('BASEPATH') or exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

class Booking extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'pagination', 'Api_Whatsapp', 'pdfgenerator']);
        $this->load->helper(['string', 'url', 'date', 'number']);
        $this->load->model(['M_Customer', 'M_Booking', 'M_Auth', 'M_Agent', 'M_Partner']);

        if (!$this->session->userdata('is_logged_in')) {

            $this->session->set_userdata('last_page', current_url());

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
			You have to login first.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>');

            redirect('auth');
        }
    }

    public function index()
    {
        if ($this->session->userdata('role_id') == '3') {
            $saldo = $this->checkSaldo(($this->session->userdata('partner_id')));

            if ($saldo < 500000) {
                $this->session->set_flashdata('message_warning', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                Saldo Anda kurang dari 500.000. Segera lakukan top-up Saldo!.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>');
            }
        }

        $per_page = ($this->input->post('show_per_page')) ? trim($this->input->post('show_per_page')) : (($this->session->userdata('show_per_page')) ? $this->session->userdata('show_per_page') : '10');
        if ($per_page === null) $per_page = $this->session->userdata('show_per_page');
        else $this->session->set_userdata('show_per_page', $per_page);

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_booking')) ? $this->session->userdata('search_booking') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_booking');
        else $this->session->set_userdata('search_booking', $keyword);

        $config = [
            'base_url' => site_url('booking/index'),
            'total_rows' => $this->M_Booking->countBooking($keyword),
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
            "title" => "Booking",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "booking",
            "pages" => "pages/booking/v_booking",
            "bookings" => $this->M_Booking->listBookingPaginate($config["per_page"], $page, $keyword),
            "partners" => $this->M_Partner->list_partner(),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        // echo '<pre>';
        // print_r($data['bookings']);
        // echo '</pre>';
        // exit;

        $this->load->view('pages/index', $data);
    }

    public function create_booking()
    {
        if ($this->session->userdata('role_id') == '3') {
            $saldo = $this->checkSaldo(($this->session->userdata('partner_id')));

            if ($saldo < 500000) {
                $this->session->set_flashdata('message_error', 'Saldo kurang dari Rp500.000. Booking tidak dapat diproses');
                redirect('booking');
            }
        }

        // $pages_booking = ($this->session->userdata('role_id') == '3') ? 'v_create_booking_v2' : 'v_create_booking';
        $pages_booking = 'v_create_booking_v2';

        $data = [
            "title" => "Create Booking",
            "segment" => "booking",
            "pages" => "pages/booking/" . $pages_booking,
            "bookings" => $this->M_Booking->list_booking(),
            "customers" => $this->M_Customer->list_customer(),
            "drivers" => $this->M_Booking->list_driver(),
            "saldo_mitra" => $saldo
        ];

        $this->load->view('pages/index', $data);
    }

    public function load_data()
    {
        $mode = $this->input->get('mode'); // Ambil mode dari AJAX (desktop/mobile)

        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_order_keyword')) ? $this->session->userdata('search_order_keyword') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_order_keyword');
        else $this->session->set_userdata('search_order_keyword', $keyword);

        // $status = ($this->input->post('status')) ? trim($this->input->post('status')) : (($this->session->userdata('search_order_status')) ? $this->session->userdata('search_order_status') : '');
        // if ($status === null) $status = $this->session->userdata('search_order_status');
        // else $this->session->set_userdata('search_order_status', $status);

        // $tanggal = ($this->input->post('tanggal')) ? trim($this->input->post('tanggal')) : (($this->session->userdata('search_order_tanggal')) ? $this->session->userdata('search_order_tanggal') : '');
        // if ($tanggal === null) $tanggal = $this->session->userdata('search_order_tanggal');
        // else $this->session->set_userdata('search_order_tanggal', $tanggal);

        $config['per_page'] = 10;

        $page = $this->uri->segment(3) ? ($this->uri->segment(3) - 1) * $config['per_page'] : 0;

        $data = [
            "keyword" => $keyword,
            "bookings" => $this->M_Booking->listBookingPaginate($config["per_page"], $page, $keyword),
            "partners" => $this->M_Partner->list_partner(),
            "per_page" => $config['per_page'],
            "total_rows" => $this->M_Booking->countBooking($keyword),
        ];

        if ($mode == 'mobile') {
            $this->load->view('pages/booking/v_booking_mobile', $data);
        } else {
            $this->load->view('pages/booking/v_booking_desktop', $data);
        }
    }

    private function checkSaldo($id)
    {
        return $this->M_Partner->getSaldoAkhirPartner($id)['saldo_akhir'];
    }

    public function store_booking()
    {
        $nama_pengirim = trim($this->input->post('nama_pengirim'));
        $telepon_pengirim = trim($this->input->post('telepon_pengirim'));
        $alamat_pengirim = trim($this->input->post('alamat_pengirim'));

        $pengirim = $nama_pengirim . ' ' . $telepon_pengirim;

        $slug_pengirim = url_title($pengirim, 'dash', true);

        $this->db->trans_begin();

        $cek_pengirim = $this->M_Customer->is_available($slug_pengirim);

        if (!$cek_pengirim) {

            $data_pengirim = [
                'nama_customer' => $nama_pengirim,
                'telepon_customer' => $telepon_pengirim,
                'alamat_customer' => $alamat_pengirim,
                'slug' => $slug_pengirim,
            ];

            $this->M_Customer->insert($data_pengirim);
        }

        $nama_penerima = trim($this->input->post('nama_penerima'));
        $telepon_penerima = trim($this->input->post('telepon_penerima'));
        $alamat_penerima = trim($this->input->post('alamat_penerima'));
        $penerima = $nama_penerima . ' ' . $telepon_penerima;

        $slug_penerima = url_title($penerima, 'dash', true);

        $cek_penerima = $this->M_Customer->is_available($slug_penerima);

        if (!$cek_penerima) {

            $data_penerima = [
                'nama_customer' => $nama_penerima,
                'telepon_customer' => $telepon_penerima,
                'alamat_customer' => $alamat_penerima,
                'slug' => $slug_penerima
            ];

            $this->M_Customer->insert($data_penerima);
        }

        $max_num = $this->M_Booking->selectMaxResi();

        $kode = "SMC" . date('ymd');

        if (!$max_num['max']) {
            $bilangan = 1;
        } else {
            $bilangan = $max_num['max'] + 1;
        }

        $no_urut = sprintf("%04d", $bilangan);
        $no_resi = $kode . $no_urut;

        // nominal
        $nominal = $this->convertToNumberWithComma($this->input->post('nominal')); // 1055530.00
        $harga_jual = $this->input->post('harga_jual');
        $chargeable = $this->convertToNumberWithComma($this->input->post('chargeable'));
        $partner_fee = ceil($nominal * (0.2));
        $ppm = $nominal - $partner_fee;
        $cost_ppm = $harga_jual * $chargeable;
        $benefit_ppm = $ppm - $cost_ppm;
        $dom_int = $this->input->post('jenis_pengiriman');
        $data = [
            'no_resi' => $no_resi,
            'no_urut' => $no_urut,
            'nama_pengirim' => trim($this->input->post('nama_pengirim')),
            'telepon_pengirim' => trim($this->input->post('telepon_pengirim')),
            'alamat_pengirim' => trim($this->input->post('alamat_pengirim')),
            'nama_penerima' => trim($this->input->post('nama_penerima')),
            'telepon_penerima' => trim($this->input->post('telepon_penerima')),
            'alamat_penerima' => trim($this->input->post('alamat_penerima')),
            'commodity' => trim($this->input->post('jenis_barang')),
            'qty' => $this->convertToNumberWithComma($this->input->post('total_qty')),
            'berat_timbang' => $this->convertToNumberWithComma($this->input->post('berat_timbang')),
            'chargeable' => $chargeable,
            'volume' => $this->convertToNumberWithComma($this->input->post('total_volume')),
            'origin' => trim($this->input->post('origin')),
            'destination' => trim($this->input->post('destination')),
            'price_per_kg' => ($this->input->post('harga')),
            'harga_jual_per_kg' => $harga_jual,
            'nominal' => $nominal,
            'created_by' => $this->session->userdata('user_id'),
            'partner_id' => $this->session->userdata('partner_id'),
            'ppm' => $ppm,
            'cost_ppm' => $cost_ppm,
            'benefit_ppm' => $benefit_ppm,
            'partner_fee' => $partner_fee,
            'jenis_pengiriman' => $dom_int,
        ];

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;

        $id_resi = $this->M_Booking->insertResi($data);

        if (!$id_resi) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('message_error', 'Gagal menyimpan resi. Silakan coba lagi.');
            redirect('booking');
        }

        $panjangs = $this->input->post('panjang');
        $lebars = $this->input->post('lebar');
        $tinggis = $this->input->post('tinggi');
        $jumlahs = $this->input->post('jumlah');
        $volumes = $this->input->post('volume');

        $dimensi = [];

        if (is_array($panjangs)) {
            for ($i = 0; $i < count($panjangs); $i++) {
                $panjang = $this->convertToNumberWithComma($panjangs[$i]);
                $lebar = $this->convertToNumberWithComma($lebars[$i]);
                $tinggi = $this->convertToNumberWithComma($tinggis[$i]);
                $jumlah = $this->convertToNumberWithComma($jumlahs[$i]);
                $volume = $this->convertToNumberWithComma($volumes[$i]);

                $dimensi[] = [
                    'panjang' => $panjang,
                    'lebar' => $lebar,
                    'tinggi' => $tinggi,
                    'jumlah' => $jumlah,
                    'volume' => $volume,
                    'id_resi' => $id_resi,
                    'created_by' => $this->session->userdata('user_id'),
                ];
            }

            if (!empty($dimensi)) {
                $insert = $this->M_Booking->insert_dimensi($dimensi);
                if ($insert) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message_name', 'Booking berhasil diinput!');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');
                }
            } else {
                $this->session->set_flashdata('message_name', 'Booking berhasil diinput!');
                $this->db->trans_commit(); // Tambahkan ini jika tidak ada dimensi
            }
            redirect('booking');
        }
    }

    public function editResi($no_resi)
    {
        if ($this->session->userdata('role_id') == '3') {
            $saldo = $this->checkSaldo(($this->session->userdata('partner_id')));

            if ($saldo < 500000) {
                $this->session->set_flashdata('message_error', 'Saldo kurang dari Rp500.000. Booking tidak dapat diproses');
                redirect('booking');
            }
        }

        $pages_booking = 'v_edit_booking';

        $data = [
            "title" => "Edit Booking",
            "segment" => "booking",
            "pages" => "pages/booking/" . $pages_booking,
            "booking" => $this->M_Booking->detailBooking($no_resi),
        ];

        $this->load->view('pages/index', $data);
    }


    public function detailBooking($id)
    {
        $booking = $this->M_Booking->detailBooking($id);
        $max = $this->M_Booking->selectMaxResi($booking['no_booking'])['max'];

        if (!$max) {
            $bilangan = 1; // Jika belum ada, mulai dari 1
        } else {
            $bilangan = $max + 1; // Jika sudah ada, tambah 1
        }

        $price = $this->M_Booking->getPrice($booking['origin'], $booking['destination']);

        if (!$price) {

            $this->session->set_flashdata('message_error', "Pricelist {$booking['origin']} to {$booking['destination']} not available. Please try again!");
            redirect("booking");
        }

        $no_urut = sprintf("%04d", $bilangan);

        $data = [
            "title" => $id,
            "max" => $no_urut,
            "segment" => "booking",
            "price" => $price,
            "pages" => "pages/booking/v_detail_booking",
            "customers" => $this->M_Customer->list_customer(),
            "agents" => $this->M_Agent->listAgentByDestination($booking['destination']),
            "booking" => $booking,
            "details" => $this->M_Booking->detailItemBooking($booking['Id']),
            "drivers" => $this->M_Booking->list_driver(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function simpanDetailAwb($id)
    {
        $no_booking = $this->input->post('no_booking');
        $service = $this->input->post('service');
        $price_per_kg = $this->input->post('price');
        $dooring_per_kg = $this->input->post('dooring');

        $nomor_uruts = $this->input->post('nomor_urut');
        $qtys = $this->input->post('qty');
        $chargeables = $this->input->post('chargeable');
        $cities = $this->input->post('kota_tujuan');
        $agent_ids = $this->input->post('agent_id');
        $nominals = $this->input->post('nominal');
        $pickup_prices = $this->input->post('pickup_price');
        $dooring_prices = $this->input->post('dooring_price');
        $total_amounts = $this->input->post('total_amount');

        $awb = trim($this->input->post('awb_num'));

        $data_booking = [
            'opsi_service' => $this->input->post('service'),
            'awb' => $awb,
        ];

        $detail = [];

        if (is_array($nomor_uruts)) {
            $this->db->trans_begin();

            $status_tracking = ($service == 'port-to-port' || $service == 'port-to-door') ? '1' : '0';

            $this->M_Booking->delete_detail_awb($id);

            for ($i = 0; $i < count($nomor_uruts); $i++) {
                $nomor_urut = trim($nomor_uruts[$i]);
                $qty = $this->convertToNumber($qtys[$i]); // trim tidak diperlukan untuk angka
                $chargeable = $this->convertToNumber($chargeables[$i]);
                $city = trim($cities[$i]);
                $agent_id = $this->convertToNumber($agent_ids[$i]);
                $nominal = $this->convertToNumber($nominals[$i]);
                $pickup_price = $this->convertToNumber($pickup_prices[$i]);
                $dooring_price = $this->convertToNumber($dooring_prices[$i]);
                $total_amount = $this->convertToNumber($total_amounts[$i]);

                $slug = $no_booking . '-' . $nomor_urut;

                // Masukkan path QR code ke dalam detail
                if ($qty) {
                    $detail[] = [
                        'no_urut' => $nomor_urut,
                        'booking_id' => $id,
                        'slug' => $slug,
                        'qty' => $qty,
                        'chargeable' => $chargeable,
                        'kota_tujuan' => $city,
                        'nominal' => $nominal,
                        'pickup_price' => $pickup_price,
                        'dooring_price' => $dooring_price,
                        'total_amount' => $total_amount,
                        'status_tracking' => $status_tracking,
                        'created_by' => $this->session->userdata('user_id'),
                        // 'qr_code' => $image_name,
                        'agent_id' => $agent_id,
                        'price_per_kg' => $price_per_kg,
                        'dooring_per_kg' => $dooring_per_kg,
                    ];
                }
            }

            if (!empty($detail)) {

                if ($this->M_Booking->insert_batch_detail_awb($detail)) {
                    $this->M_Booking->updateBooking($id, $data_booking);

                    $this->db->trans_commit();

                    $detailBooking = $this->M_Booking->detailBooking($no_booking);
                    $driver = $this->M_Auth->getUserById($detailBooking['id_driver'])['phone_number'];

                    // notifikasi awb ke driver untuk pengantaran barang
                    $driver = $this->M_Auth->getUserById($detailBooking['id_driver'])['phone_number'];
                    $pesan_driver = $this->messageToDriver($detailBooking, $no_booking);
                    $this->api_whatsapp->wa_notif($pesan_driver, $driver);

                    $this->session->set_flashdata('message_name', 'Resi berhasil ditambahkan.');

                    redirect('booking/list_detail');
                } else {
                    $this->db->trans_rollback();

                    $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');

                    redirect('booking');
                }
            }
        } else {
            $this->db->trans_rollback();

            $this->session->set_flashdata('message_error', 'Silahkan isi awb!');

            redirect('booking');
        }
    }

    public function updateStatusBayar()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        if (!in_array($status, ['0', '1'])) {
            $response = array('success' => false, 'message' => 'Status tidak valid.');
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $detailResi = $this->M_Booking->getResi($id);

        if (empty($detailResi)) {
            $response = array('success' => false, 'message' => 'Data resi tidak ditemukan.');
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $data = [
            'status_bayar' => $status
        ];

        $this->db->trans_begin();

        if ($this->M_Booking->updateResi($id, $data)) {

            $data_deposit = [
                'partner_id' => $detailResi['partner_id'],
                'resi_id' => $detailResi['id'],
                'usage_saldo' => ($status == '1') ? $detailResi['ppm'] : '0',
            ];

            $cek_deposit = $this->db->where('resi_id', $detailResi['id'])->get('deposit')->num_rows();

            if ($cek_deposit) {
                // Jika sudah ada, update data deposit
                if (!$this->db->where('resi_id', $detailResi['id'])->update('deposit', $data_deposit)) {
                    $this->db->trans_rollback();
                    $response = array('success' => false, 'message' => 'Gagal memperbarui deposit.');
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }
            } else {
                // Jika belum ada, insert data deposit
                if (!$this->db->insert('deposit', $data_deposit)) {
                    $this->db->trans_rollback();
                    $response = array('success' => false, 'message' => 'Gagal menambah deposit.');
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }
            }

            $this->db->trans_commit();

            if ($status == '1') {
                $message = "Resi sudah dibayar";

                // pesan untuk pengirim
                $pesan_pengirim = $this->messageToCustomer($id, $detailResi['nama_pengirim']);

                // pesan untuk penerima
                $pesan_penerima = $this->messageToCustomer($id, $detailResi['nama_penerima']);

                // notifikasi WA ke pengirim
                $this->api_whatsapp->wa_notif($pesan_pengirim, $detailResi['telepon_pengirim']);

                // notifikasi WA ke penerima
                $this->api_whatsapp->wa_notif($pesan_penerima, $detailResi['telepon_penerima']);
            } else {
                $message = "Resi belum dibayar";
            }
        } else {

            $this->db->trans_rollback();
            $response = array('success' => false, 'message' => 'Gagal memperbarui resi.');
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
            return;
        }

        $response = array('success' => true, 'status' => $status, 'message' => $message);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    function convertToNumber($formattedNumber)
    {
        $numberWithoutThousandsSeparator = str_replace('.', '', $formattedNumber);

        // $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);

        // Mengonversi string ke float
        return (float) $numberWithoutThousandsSeparator;
    }

    function convertToNumberWithComma($formattedNumber)
    {
        // Mengganti titik sebagai pemisah ribuan dengan string kosong
        $numberWithoutThousandsSeparator = str_replace(',', '', $formattedNumber);

        $standardNumber = $numberWithoutThousandsSeparator;

        // Mengonversi string ke float
        return (float) $standardNumber;
    }

    public function list_detail()
    {
        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_resi')) ? $this->session->userdata('search_resi') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_resi');
        else $this->session->set_userdata('search_resi', $keyword);

        $config = [
            'base_url' => site_url('booking/list_detail'),
            'total_rows' => $this->M_Booking->countItem($keyword),
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
            "title" => "Receipt",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "detail_awb",
            "pages" => "pages/booking/v_resi",
            "awbs" => $this->M_Booking->listItemAwbPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        $this->load->view('pages/index', $data);
    }

    public function formConfirmWarehouse()
    {
        $id = $this->input->post('id');
        $drivers = $this->M_Booking->list_driver();

        $data = $this->M_Booking->getBooking($id);

        $today = date('Y-m-d');

        $url_form = base_url('booking/arriveWarehouse/' . $id);

        $tanggal_berangkat = $data['flight_date'];

        $min = (!$tanggal_berangkat) ? "min='$today'" : '';

        $output = "<form method='POST' action='$url_form'>";
        $output .= "
                <div class='row'>
                    <div class='col-md-6 col-12'>
                        <div class='mb-3'>
                            <label class='form-label'>Jenis moda pengiriman</label>
                            <select class='form-select' name='moda_pengiriman' required>
                                <option value=''>:: Pilih moda pengiriman</option>
                                <option value='kereta'>Kereta</option>
                                <option value='pesawat'>Pesawat</option>
                            </select>
                        </div>
                    </div> 
                    <div class='col-md-6 col-12'>
                        <div class='mb-3'>
                            <label class='form-label'>Tanggal keberangkatan</label>
                            <input type='date' class='form-control' name='tanggal_berangkat' $min value='$tanggal_berangkat'>
                        </div>
                    </div>
                </div>";
        $output .= "<div class='row mt-2'><div class='col-12 text-end'><button type='submit' class='btn btn-primary btn-submit'>Confirm</button></div></div>";
        $output .= "</form>";

        echo $output;
    }

    public function formPickup()
    {
        $id = $this->input->post('id');
        $drivers = $this->M_Booking->list_driver();

        $dataAwb = $this->M_Booking->getBooking($id);

        $data = [
            'awb' => $dataAwb,
            'drivers' => $drivers
        ];

        $this->load->view('pages/booking/v_modal_setpickup', $data);
    }

    public function formInputAwb()
    {
        $id = $this->input->post('id');

        $data = [
            'id' => $id,
        ];

        $this->load->view('pages/booking/v_modal_input_awb', $data);
    }

    public function updateTrackingStatus($id, $status, $successMessage, $errorMessage)
    {
        $data = ['status_tracking' => $status];

        if ($this->M_Booking->updateAwbDetail($id, $data)) {
            $this->session->set_flashdata('message_name', $successMessage);
        } else {
            $this->session->set_flashdata('message_error', $errorMessage);
        }

        redirect('booking/list_detail');
    }

    public function setPickup($id)
    {
        $detailAwb = $this->M_Booking->detailBooking($id);

        $driver_id = $this->input->post('driver_id');
        $titik_jemput = $this->input->post('titik_jemput');
        $gudang_tujuan = $this->input->post('gudang_tujuan');
        $tanggal_pickup = $this->input->post('tanggal_pickup');

        // Ambil nomor HP driver untuk notifikasi ke whatsapp
        $driver = $this->M_Auth->getUserById($driver_id);

        $data_resi = [
            'status_tracking' => '1',
        ];

        $this->db->trans_begin();

        $this->M_Booking->updateAwbDetailByBookingId($detailAwb['Id'], $data_resi);

        $data_booking = [
            'id_driver' => $driver_id,
            'tanggal_pickup' => $tanggal_pickup,
            'set_pickup' => '1',
        ];

        $nama_driver = $driver['name'];
        $no_smu = $detailAwb['awb'];
        $nama_customer = $detailAwb['nama_customer'];
        $qty = $detailAwb['total_qty'];
        $beratBarang = $detailAwb['total_chargeable'];
        $jenisBarang = $detailAwb['commodity'];

        // Kunci rahasia untuk hash
        $secret_key = "kriboexpress-kirimbro";

        // Buat token hash menggunakan no_smu dan driver_id
        $token = hash_hmac('sha256', $no_smu . $driver_id, $secret_key);

        // URL dengan token
        $linkKonfirmasi = base_url("confirm/pickup/$no_smu/$driver_id?token=$token");

        if ($this->M_Booking->updateAwb($id, $data_booking)) {
            $pesan = "*Halo, $nama_driver!*\n";
            $pesan .= "Kami ingin mengingatkan bahwa anda mendapatkan tugas untuk pick-up barang customer. Berikut adalah detail barang yang harus Anda pickup:\n";
            $pesan .= "*• Nomor SMU: $no_smu*\n";
            $pesan .= "*• Nama Pengirim: $nama_customer*\n";
            $pesan .= "*• Alamat Barang: $titik_jemput*\n";
            $pesan .= "*• Gudang Tujuan: $gudang_tujuan*\n";
            $pesan .= "*• Kuantitas Barang: $qty*\n";
            $pesan .= "*• Jenis Barang: $jenisBarang*\n";
            $pesan .= "*• Berat Barang: $beratBarang*\n";
            $pesan .= "Setelah paket diambil, mohon klik tautan di bawah ini untuk mengonfirmasi bahwa barang sudah dipickup dan sedang dalam perjalanan ke gudang:\n";
            $pesan .= "$linkKonfirmasi\n\n";
            $pesan .= "Terima kasih atas kerja keras Anda dalam memastikan pengiriman berjalan lancar!\n";
            $pesan .= "*Salam hangat,*\n";
            $pesan .= "Tim Smesco Express\n";
            $pesan .= "Kirim Bro"; //dibuat miring

            $no_whatsapp = $driver['phone_number'];

            $this->api_whatsapp->wa_notif($pesan, $no_whatsapp);
            $this->db->trans_commit();

            $this->session->set_flashdata('message_name', "Penjemputan barang $id sudah diatur.");
        } else {
            $this->db->trans_rollback();

            $this->session->set_flashdata('message_error', "Gagal atur penjemputan barang $id. Silahkan coba lagi!");
        }

        redirect('booking');
    }

    public function confirmPickup()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        // Mulai transaksi DB
        $this->db->trans_begin();

        $foto_path = null;

        // Proses upload file jika ada
        if (!empty($_FILES['foto_pickup']['name'])) {
            $config['upload_path']   = './assets/files/dokumentasi_pickup/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size']      = 2048;
            $config['file_name']     = 'pickup_' . time();

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('foto_pickup')) {
                // Batalkan transaksi karena upload gagal
                $this->db->trans_rollback();

                $response = array(
                    'success' => false,
                    'message' => 'Upload foto gagal: ' . strip_tags($this->upload->display_errors())
                );
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }

            $upload_data = $this->upload->data();
            $foto_path = 'assets/files/dokumentasi_pickup/' . $upload_data['file_name'];
        }

        // Data yang akan diupdate
        $data = [
            'confirm_pickup' => $status,
            'status_tracking' => '2',
            'foto_pickup' => $foto_path
        ];

        // Update database
        $this->M_Booking->updateResi($id, $data);

        // Cek apakah ada error selama transaksi
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();

            $response = array(
                'success' => false,
                'message' => 'Gagal memperbarui status pickup di database.'
            );
        } else {
            $this->db->trans_commit();

            $message = $status == '1' ? "Barang sudah di-pickup" : "Barang batal di-pickup";
            $response = array(
                'success' => true,
                'message' => $message
            );
        }

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }



    public function confirmWarehouse()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $resi = $this->M_Booking->getResi($id);

        $agent = $this->M_Agent->showById($resi['agent_id']);

        $data = [
            'confirm_arr_warehouse' => $status,
            'status_tracking' => '3'
        ];

        $this->db->trans_begin();

        if ($this->M_Booking->updateResi($id, $data)) {
            $this->db->trans_commit();
            if ($status == '1') {
                $message = "Barang sudah dikonfirmasi tiba di gudang";

                // kirim notifikasi ke agent
                $this->messageToAgent($agent, $resi);
            } else {
                $message = "Barang batal konfirmasi tiba di gudang";
            }
        }

        // Kembalikan respon JSON
        $response = array('success' => true, 'status', 'message' => $message);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function confirmArrDestination()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $detailResi = $this->M_Booking->getResi($id);

        $data = [
            'confirm_arrival' => $status,
            'status_tracking' => '4'
        ];

        // Lakukan pembaruan status pada database
        $this->M_Booking->updateResi($id, $data);

        if ($status == '1') {
            $message = "Barang sudah dikonfirmasi tiba di alamat penerima";

            $pesan_pengirim = $this->messageArrivalToCustomer($detailResi);
            $pesan_penerima = $this->messageArrivalToCustomer($detailResi);

            $this->api_whatsapp->wa_notif($pesan_pengirim, $detailResi['telepon_pengirim']);
            $this->api_whatsapp->wa_notif($pesan_penerima, $detailResi['telepon_penerima']);
        } else {
            $message = "Barang batal konfirmasi tiba di alamat penerima";
        }
        // Kembalikan respon JSON
        $response = array('success' => true, 'status', 'message' => $message);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function updateAwb($id)
    {
        $data = [
            'awb' => trim($this->input->post('awb')),
        ];

        $id_booking = $this->M_Booking->detailBooking($id)['Id'];

        if ($this->M_Booking->updateBooking($id_booking, $data)) {
            $this->session->set_flashdata('message_name', "AWB untuk nomor booking $id sudah diperbarui");
        } else {
            $this->session->set_flashdata('message_error', "Gagal perbarui awb untuk nomor booking $id. Silahkan coba lagi!");
        }

        redirect('booking');
    }

    public function arriveWarehouse($id)
    {
        $detailBooking = $this->M_Booking->detailBooking($id);
        // $resi = $this->M_Booking->detailItemBooking($detailBooking['Id']);
        // echo '<pre>';
        // print_r($resi);
        // echo '</pre>';
        // exit;

        $data_resi = [
            'status_tracking' => '3',
            'tanggal_berangkat' => $this->input->post('tanggal_berangkat'),
            'moda_pengiriman' => $this->input->post('moda_pengiriman'),
        ];

        $data_booking = ['confirm_warehouse' => '1'];

        // print_r($id);
        // exit;

        $this->db->trans_begin();
        $this->M_Booking->updateAwbDetailByBookingId($detailBooking['Id'], $data_resi);

        if ($this->M_Booking->updateBooking($detailBooking['Id'], $data_booking)) {
            $resi = $this->M_Booking->detailItemBooking($detailBooking['Id']);

            // foreach ($resi as $r) :
            // endforeach;

            // Kirim notifikasi ke agent
            // $this->api_whatsapp->wa_notif($pesan_agent, $customer);

            // Kirim notifikasi ke customer
            // $pesan_customer = $this->messageToCustomer($detailBooking, $resi);
            // $this->api_whatsapp->wa_notif($pesan_customer, $customer);

            $this->db->trans_commit();

            $this->messageToAgent($detailBooking, $resi);

            $this->session->set_flashdata('message_name', "Barang sudah dikonfirmasi tiba di gudang tujuan.");
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('message_error', "Gagal konfirmasi barang tiba di gudang tujuan. Silahkan coba lagi!");
        }

        redirect('booking');
    }

    private function messageToAgent($agent, $resi)
    {
        $destination = $resi['destination'];

        $token = hash_hmac('sha256', $resi['no_resi'], "kriboexpress-kirimbro-sampaibro");
        $linkKonfirmasi = base_url("confirm/arrDestination/{$resi['no_resi']}?token=$token");

        $pesan_agent = "*Halo, {$agent['nama_agent']}!*\nKami ingin mengonfirmasi bahwa paket sedang menuju {$destination}:\n";
        $pesan_agent .= "*• Nomor Resi: {$resi['no_resi']}*\n";
        $pesan_agent .= "*• Nama Pengirim: {$resi['nama_pengirim']}*\n";
        $pesan_agent .= "*• Nama Penerima: {$resi['nama_penerima']}*\n";
        $pesan_agent .= "*• Jenis Barang: {$resi['commodity']}*\n";
        $pesan_agent .= "*• Berat Barang: {$resi['chargeable']}*\n";
        $pesan_agent .= "*• Kuantitas Barang: {$resi['qty']}*\n";
        $pesan_agent .= "*• Alamat Tujuan: {$resi['alamat_penerima']}*\n\n";
        $pesan_agent .= "*Link konfirmasi: \n $linkKonfirmasi *\n\n";
        $pesan_agent .= "Setelah dikonfirmasi, status pengiriman akan diperbarui otomatis.\n*Salam hangat,*\nTim Smesco Express\nKirim Bro"; //dibuat miring

        $this->api_whatsapp->wa_notif($pesan_agent, $agent['telepon_agent']);

        return true;
    }

    private function messageToCustomer($no_resi, $nama_tujuan)
    {
        $linkTracking = base_url("home/track");

        $pesan_customer = "*Halo, {$nama_tujuan}!*\n";
        $pesan_customer .= "Paket Anda sedang dalam perjalanan. Berikut nomor resi:\n";
        $pesan_customer .= "*-- $no_resi*\n";
        $pesan_customer .= "\nAnda dapat melacak status pengiriman di:\n $linkTracking \n\n*Salam hangat,*\nTim Smesco Express\n_Kirim Bro_"; //dibuat miring

        return $pesan_customer;
    }

    private function messageToDriver($detailBooking, $no_booking)
    {
        $no_smu = $detailBooking['awb'];
        $driver_id = $detailBooking['id_driver'];

        $driver = $this->M_Auth->getUserById($driver_id);

        // Kunci rahasia untuk hash
        $secret_key = "kriboexpress-kirimbro";

        // Buat token hash menggunakan no_smu dan driver_id
        $token = hash_hmac('sha256', $no_booking . $driver_id, $secret_key);

        // URL dengan token
        $linkKonfirmasi = base_url("confirm/pickup/$no_booking/$driver_id?token=$token");

        $pesan = "*Halo, {$driver['name']}!*\n";
        $pesan .= "Kami ingin mengingatkan bahwa anda mendapatkan tugas untuk pick-up barang customer. Berikut adalah detail barang yang harus Anda pickup:\n";
        $pesan .= "*• Nomor Booking: {$no_booking}*\n";
        $pesan .= "*• Nama Pengirim: {$detailBooking['nama_customer']}*\n";
        $pesan .= "*• Alamat Barang: {$detailBooking['alamat_pickup']}*\n";
        $pesan .= "*• Gudang Tujuan: {$detailBooking['gudang_tujuan']}*\n";
        $pesan .= "*• Kuantitas Barang: {$detailBooking['total_qty']}*\n";
        $pesan .= "*• Jenis Barang: {$detailBooking['commodity']}*\n";
        $pesan .= "*• Berat Barang: {$detailBooking['total_chargeable']}*\n";
        $pesan .= "\nSetelah paket diambil, mohon klik tautan di bawah ini untuk mengonfirmasi bahwa barang sudah dipickup dan sedang dalam perjalanan ke gudang:\n";
        $pesan .= "$linkKonfirmasi\n\n";
        $pesan .= "Terima kasih atas kerja keras Anda dalam memastikan pengiriman berjalan lancar!\n";
        $pesan .= "*Salam hangat,*\n";
        $pesan .= "Tim Smesco Express\n";
        $pesan .= "Kirim Bro"; //dibuat miring
        return $pesan;
    }


    public function arriveDestination($id)
    {

        $detailResi = $this->M_Booking->getItemAwb($id);

        $partner_id = $this->M_Booking->getBookingById($detailResi['booking_id'])['partner_id'];

        $customer_phone = $this->M_Customer->showById($partner_id);
        // print_r($customer_phone);
        // exit;
        $data = [
            'status_tracking' => '4',
        ];

        $this->db->trans_begin();

        if ($this->M_Booking->updateAwbDetail($id, $data)) {
            $this->db->trans_commit();

            $pesan_customer = $this->messageArrivalToCustomer($detailResi);

            $this->api_whatsapp->wa_notif($pesan_customer, $customer_phone);

            $this->session->set_flashdata('message_name', "Konfirmasi barang $id sudah tiba di tujuan berhasil.");
        } else {
            $this->session->set_flashdata('message_error', "Gagal konfimasi barang $id tiba di tujuan. Silahkan coba lagi!");
        }

        redirect('booking/list_detail');
    }

    public function detailResi($no_resi)
    {
        // print_r($no_resi);
        $resi = $this->M_Booking->getResi($no_resi);

        $data = [
            "title" => $no_resi,
            "segment" => "booking",
            "pages" => "pages/booking/v_detail_resi",
            "resi" => $resi,
            "customers" => $this->M_Customer->list_customer(),
            "agents" => $this->M_Agent->listAgentByDestination($resi['destination']),
            "drivers" => $this->M_Booking->list_driver(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function updateResi($no_resi)
    {
        $agent_id = $this->input->post('agent_id');

        // Memulai transaksi database
        $this->db->trans_begin();

        if ($agent_id == "__tambah__") {
            $nama_agent = trim($this->input->post('nama_agent'));
            $telepon_agent = trim($this->input->post('telepon_agent'));

            $slug_input = $nama_agent . ' ' . $telepon_agent;
            $slug = url_title($slug_input, 'dash', true);

            $cek = $this->M_Agent->is_available($slug);

            if ($cek) {
                $this->session->set_flashdata('message_error', "Agent a.n $nama_agent sudah tersedia");
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data_agent = [
                    'nama_agent' => $nama_agent,
                    'telepon_agent' => trim($this->input->post('telepon_agent')),
                    'alamat_agent' => trim($this->input->post('alamat_agent')),
                    'origin' => trim($this->input->post('destination')),
                    'slug' => $slug,
                ];

                $id_agent = $this->M_Agent->insertGetId($data_agent);
            }
        } else {
            $id_agent = $agent_id;
        }

        $data = [
            'nama_pengirim' => trim($this->input->post('nama_pengirim')),
            'telepon_pengirim' => trim($this->input->post('telepon_pengirim')),
            'alamat_pengirim' => trim($this->input->post('alamat_pengirim')),
            'nama_penerima' => trim($this->input->post('nama_penerima')),
            'telepon_penerima' => trim($this->input->post('telepon_penerima')),
            'alamat_penerima' => trim($this->input->post('alamat_penerima')),
            'commodity' => trim($this->input->post('jenis_barang')),
            'qty' => $this->convertToNumberWithComma($this->input->post('total_qty')),
            'berat_timbang' => $this->convertToNumberWithComma($this->input->post('berat_timbang')),
            'chargeable' => $this->convertToNumberWithComma($this->input->post('chargeable')),
            // 'panjang' => $this->convertToNumberWithComma($this->input->post('panjang')),
            // 'lebar' => $this->convertToNumberWithComma($this->input->post('lebar')),
            // 'tinggi' => $this->convertToNumberWithComma($this->input->post('tinggi')),
            'volume' => $this->convertToNumberWithComma($this->input->post('total_volume')),
            'origin' => trim($this->input->post('origin')),
            'destination' => trim($this->input->post('destination')),
            'price_per_kg' => ($this->input->post('harga')),
            'nominal' => $this->convertToNumberWithComma($this->input->post('nominal')),
            'gudang_tujuan' => trim($this->input->post('gudang_tujuan')),
            'agent_id' => $id_agent,
            'awb' => trim($this->input->post('awb')),
            'moda_pengiriman' => trim($this->input->post('moda_pengiriman')),
            'tanggal_berangkat' => trim($this->input->post('tanggal_berangkat')),
            'driver_pickup_id' => trim($this->input->post('driver')),
            'jadwal_pickup' => trim($this->input->post('jadwal_pickup')),
            'url_tracking' => trim($this->input->post('url_tracking')),
        ];

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // exit;

        if (!empty($data)) {
            if ($this->M_Booking->updateResi($no_resi, $data)) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message_name', 'Data resi berhasil diperbarui.!');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');
            }
            redirect('booking');
        }
    }

    public function print_resi($no_resi)
    {
        $resi = $this->M_Booking->getResi($no_resi);
        // $booking = $this->M_Booking->getBookingById($resi['booking_id']);

        $linkTracking = base_url("home/track/$no_resi");

        // Load library CIQRCode
        $this->load->library('ciqrcode');

        // Generate QR code di direktori sementara
        $tempDir = sys_get_temp_dir(); // Gunakan direktori sementara
        $filePath = $tempDir . DIRECTORY_SEPARATOR . 'temp_qrcode.png'; // Path lengkap QR code

        // Set parameter QR code
        $params = [
            'data' => $linkTracking,
            'level' => 'H', // H = High
            'size' => 10,
            'savename' => $filePath, // Simpan di direktori sementara
        ];

        // Generate QR code
        $this->ciqrcode->generate($params);

        // Cek apakah file QR code sementara ada sebelum mengambil isinya
        if (file_exists($filePath)) {
            // Baca konten file QR code sebagai base64
            $imageData = base64_encode(file_get_contents($filePath));

            // Hapus file sementara setelah digunakan
            unlink($filePath);
        } else {
            // Jika file tidak ditemukan, tulis log error
            log_message('error', 'File temp_qrcode.png tidak ditemukan.');
            $imageData = ''; // Pastikan tidak ada data kosong
        }

        // Data yang akan dikirim ke view
        $data = [
            'title_pdf' => 'Resi No. ' . $no_resi,
            'resi' => $resi,
            // 'booking' => $booking,
            'pengirim' => $resi['nama_pengirim'],
            'penerima' => $resi['nama_penerima'],
            'qr_code' => 'data:image/png;base64,' . $imageData, // Gunakan base64 image
        ];

        // HTML Mode
        // $this->load->view('pages/booking/v_print_resi', $data);

        // PDF Mode
        $file_pdf = 'Resi No. ' . $no_resi;
        $paper = 'A6';
        $orientation = "portrait";
        $html = $this->load->view('pages/booking/v_print_resi', $data, true);
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    public function print_invoice($no_booking)
    {
        $booking = $this->M_Booking->detailBooking($no_booking);
        $resi = $this->M_Booking->detailItemBooking($booking['Id']);

        // Data yang akan dikirim ke view
        $data = [
            'title_pdf' => 'Invoice No. ' . $no_booking,
            'booking' => $booking,
            'resi' => $resi,
            'pengirim' => $this->M_Customer->showById($booking['customer_id']),
        ];

        // HTML Mode
        // $this->load->view('pages/booking/v_print_invoice', $data);

        // PDF Mode
        $file_pdf = 'Resi No. ' . $no_booking;
        $paper = 'A4';
        $orientation = "portrait";
        $html = $this->load->view('pages/booking/v_print_invoice', $data, true);
        $this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
    }

    private function messageArrivalToCustomer($detailResi)
    {
        $pesan_customer = "*Halo, Sobat Smesco Express!*\n";
        $pesan_customer .= "Kami dengan senang hati menginformasikan bahwa paket Anda telah tiba di tujuan dengan selamat. Berikut adalah rincian pengirimannya:\n";
        $pesan_customer .= "*• Nomor Resi: {$detailResi['no_resi']}*\n";
        $pesan_customer .= "*• Kuantitas Barang: {$detailResi['qty']}*\n";
        $pesan_customer .= "*• Berat Barang: {$detailResi['chargeable']}*\n\n";
        $pesan_customer .= "Terima kasih telah menggunakan layanan kami. Jika ada pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi kami.\n\n";
        $pesan_customer .= "*Salam hangat,*\n";
        $pesan_customer .= "Tim Smesco Express\n";
        $pesan_customer .= "Kirim Bro"; //dibuat miring

        return $pesan_customer;
    }

    public function getPrice()
    {
        $origin = $this->input->post('origin');
        $destination = $this->input->post('destination');
        $jenis = $this->input->post('jenis_pengiriman');
        $chargeable = (float) $this->input->post('chargeable');

        // cek dulu di tabel mt_destination
        $query_zone = $this->db->where('destination_name', $destination)->get('mt_destination')->row_array();

        $zone = $query_zone['zone'];

        if ($zone) {
            $destination = $zone;
        }

        $jenis = $this->db->select('jenis')->where('city', $destination)->get('mt_pricelist')->row_array()['jenis'];
        // print_r($jenis);


        $this->db->where('jenis', $jenis);
        $this->db->where('city_origin', $origin);
        $this->db->where('city', $destination);
        $this->db->where('is_active', '1');

        if ($jenis === 'IE') {
            if ($chargeable >= 21) {
                $this->db->where('min_chargeable', '21');
            } else {
                $this->db->where('min_chargeable <=', $chargeable);
                $this->db->where('max_chargeable >=', $chargeable);
            }
        } else {
            // Untuk jenis lain, ambil sesuai range chargeable
            if ($jenis == 'IR' || $jenis == "IP") {
                $this->db->where('min_chargeable <=', $chargeable);
                $this->db->where('max_chargeable >=', $chargeable);
            }
        }

        $price = $this->db->get('mt_pricelist')->row_array();

        $harga_up = 0;
        $harga_jual = 0;
        $per_kg = 0;

        if ($price) {
            if ($jenis === 'IE') {
                if ($chargeable >= 21) {
                    $harga_up = (float) $price['total'] * $chargeable;
                    $harga_jual = (float) $price['all_in_smu'] * $chargeable;
                } else {
                    $harga_up = (float) $price['total'];
                    $harga_jual = (float) $price['all_in_smu'];
                }
            } else if ($jenis === 'IP') {
                if ($chargeable >= 31 && $chargeable <= 300) {
                    $harga_up = (float) $price['total'] * $chargeable;
                    $harga_jual = (float) $price['all_in_smu'] * $chargeable;
                } else {
                    $harga_up = (float) $price['total'];
                    $harga_jual = (float) $price['all_in_smu'];
                }
            } else {
                // Selain IE 21UP, harga tetap dikali chargeable
                $harga_up = (float) $price['total'] * $chargeable;
                $harga_jual = (float) $price['all_in_smu'] * $chargeable;
            }

            $per_kg = $price['total'];
        }

        $data = [
            'chargeable' => $chargeable,
            'per_kg' => $per_kg,
            'harga_up' => $harga_up,
            'harga_jual' => round($harga_jual),
            'jenis' => $jenis
        ];

        echo json_encode($data);
    }



    public function downloadRekapExcel()
    {
        $from = $this->input->post('date_from');
        $to = $this->input->post('date_to');
        $partner_post = $this->input->post('partner_id');
        $partner_id = ($partner_post) ? $partner_post : $this->session->userdata('partner_id');

        $revenues = $this->M_Booking->getRevenueSummary($partner_id, $from, $to);

        if ($revenues) {
            require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

            $excel = new PHPExcel();

            $excel->getProperties()->setCreator('Smesco Express')
                ->setLastModifiedBy('Smesco Express')
                ->setTitle("Revenue")
                ->setSubject("Revenue")
                ->setDescription("Revenue from " . $from . ' to ' . $to)
                ->setKeywords("Revenue");

            // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
            $style_col = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
                'borders' => ['top' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'right' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'bottom' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'left' => ['style'  => PHPExcel_Style_Border::BORDER_THIN]]
            ];

            $style_row = [
                'alignment' => ['vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
                'borders' => ['top' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'right' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'bottom' => ['style'  => PHPExcel_Style_Border::BORDER_THIN], 'left' => ['style'  => PHPExcel_Style_Border::BORDER_THIN]]
            ];

            // bagian header
            $headers = [
                'A' => "No.",
                'B' => "No. Resi.",
                'C' => "Tanggal",
                'D' => "Asal",
                'E' => "Tujuan",
                'F' => "Komoditi",
                'G' => "Koli",
                'H' => "Berat timbang",
                'I' => "Volume",
                'J' => "Chargeable",
                'K' => "Nominal Resi",
                'L' => "Fee",
                // 'M' => "Topup",
                // 'N' => "Sisa saldo"
            ];

            $sheet = $excel->setActiveSheetIndex(0);
            foreach ($headers as $columnID => $header) {
                $sheet->setCellValue($columnID . '1', $header);
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $no = 1;
            $numrow = 2;

            foreach ($revenues as $t) {
                $excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
                $excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $t->no_resi);
                $excel->setActiveSheetIndex(0)->setCellValue('C' . $numrow, format_indo_non_hari($t->created_at));
                $excel->setActiveSheetIndex(0)->setCellValue('D' . $numrow, $t->origin);
                $excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, $t->destination);
                $excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $t->commodity);
                $excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, $t->qty);
                $excel->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $t->berat_timbang);
                $excel->setActiveSheetIndex(0)->setCellValue('I' . $numrow, $t->volume);
                $excel->setActiveSheetIndex(0)->setCellValue('J' . $numrow, $t->chargeable);
                $excel->setActiveSheetIndex(0)->setCellValue('K' . $numrow, $t->nominal);
                $excel->setActiveSheetIndex(0)->setCellValue('L' . $numrow, $t->partner_fee);
                // $excel->setActiveSheetIndex(0)->setCellValue('M' . $numrow, 0);
                // $excel->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $t->sisa_saldo);

                // foreach (range('A', 'L') as $columnID) {
                //     $excel->getActiveSheet()->getStyle($columnID . $numrow)->applyFromArray($style_row);
                // }

                $no++; // Tambah 1 setiap kali looping
                $numrow++; // Tambah 1 setiap kali looping
            }

            $excel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($style_col);


            // Redirect output to a client’s web browser (Excel5)
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Rekap deposit from ' . $from . ' to ' . $to . '.xls"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $objWriter->save('php://output');

            exit;
        } else {
            $this->session->set_flashdata('message_error', 'There is no data between ' . $from . ' and ' . $to);
        }

        redirect('dashboard');
    }

    public function autocompleteCustomer()
    {
        $term = $this->input->get('term');

        $this->db->like('nama_customer', $term);
        $query = $this->db->get('customer');

        $result = $query->result_array();
        $items = [];
        foreach ($result as $row) {
            $items[] = [
                'label' => $row['nama_customer'] . ' - ' . $row['telepon_customer'],
                'value' => $row['nama_customer'],
                'nama_customer' => $row['nama_customer'],
                'alamat_customer' => $row['alamat_customer'],
                'telepon_customer' => $row['telepon_customer'],
            ];
        }
        echo json_encode($items);
    }

    public function autocompleteOrigin()
    {
        $term = $this->input->get('term');

        $this->db->like('city_origin', $term);
        $this->db->group_by('city_origin');
        $query = $this->db->get('mt_pricelist');

        $result = $query->result_array();
        $items = [];
        foreach ($result as $row) {
            $items[] = [
                'label' => $row['city_origin'],
                'value' => $row['city_origin'],
            ];
        }
        echo json_encode($items);
    }

    public function autocompleteDestination()
    {
        $term = $this->input->get('term');

        $this->db->like('destination_name', $term);
        $this->db->order_by('destination_name', 'ASC');

        $query = $this->db->get('mt_destination');

        $result = $query->result_array();
        $items = [];

        foreach ($result as $row) {
            $items[] = [
                'label' => $row['destination_name'],
                'value' => $row['destination_name'],
            ];
        }

        echo json_encode($items);
    }



    public function downloadManifestPickup()
    {
        $tanggal_pickup = $this->input->post('tanggal_pickup');
        $driver_id = $this->input->post('driver_id');

        $partners = $this->M_Partner->list_active_partner();

        if (empty($partners)) {
            $this->session->set_flashdata('message_error', 'Tidak ada partner aktif.');
            redirect('dashboard');
        }

        require_once(APPPATH . 'libraries/PHPExcel/IOFactory.php');

        $excel = new PHPExcel();

        $excel->getProperties()->setCreator('Smesco Express')
            ->setLastModifiedBy('Smesco Express')
            ->setTitle("Manifest Pickup")
            ->setSubject("Manifest Pickup")
            ->setDescription("Manifest Pickup pada tanggal " . $tanggal_pickup)
            ->setKeywords("Manifest Pickup");

        // Header tabel
        $headers = [
            'A' => "No.",
            'B' => "Nama Partner",
            'C' => "Alamat Partner",
            'D' => "No. HP",
            'E' => "No. Urut",
            'F' => "No. Resi",
            'G' => "Tanggal",
            'H' => "Asal",
            'I' => "Tujuan",
            'J' => "Komoditi",
            'K' => "Koli",
            'L' => "Berat timbang",
            'M' => "Volume",
            'N' => "Chargeable",
        ];

        $sheet = $excel->setActiveSheetIndex(0);
        foreach ($headers as $columnID => $header) {
            $sheet->setCellValue($columnID . '1', $header);
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $no = 1;
        $numrow = 2;

        foreach ($partners as $p) {
            // Ambil booking berdasarkan partner
            $manifests = $this->M_Booking->getManifestPickup($p->Id, $driver_id, $tanggal_pickup);
            $countManifests = count($manifests); // Hitung jumlah booking

            if ($countManifests > 0) {
                // Merge cell untuk partner dari baris awal ke akhir booking
                $startRow = $numrow;
                $endRow = $numrow + $countManifests - 1;
                $excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
                $excel->setActiveSheetIndex(0)->setCellValue("B{$startRow}", $p->nama_pendaftar);
                $excel->setActiveSheetIndex(0)->setCellValue("C{$startRow}", $p->alamat_lengkap);
                $excel->setActiveSheetIndex(0)->setCellValue("D{$startRow}", $p->no_handphone . ' / ' . $p->no_handphone_alternatif);
                $excel->getActiveSheet()->mergeCells("A{$startRow}:A{$endRow}");
                $excel->getActiveSheet()->mergeCells("B{$startRow}:B{$endRow}");
                $excel->getActiveSheet()->mergeCells("C{$startRow}:C{$endRow}");
                $excel->getActiveSheet()->mergeCells("D{$startRow}:D{$endRow}");

                $nomor = 1;

                foreach ($manifests as $t) {
                    $excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, $nomor++);
                    $excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $t->no_resi);
                    $excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, format_indo_non_hari($t->created_at));
                    $excel->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $t->origin);
                    $excel->setActiveSheetIndex(0)->setCellValue('I' . $numrow, $t->destination);
                    $excel->setActiveSheetIndex(0)->setCellValue('J' . $numrow, $t->commodity);
                    $excel->setActiveSheetIndex(0)->setCellValue('K' . $numrow, $t->qty);
                    $excel->setActiveSheetIndex(0)->setCellValue('L' . $numrow, $t->berat_timbang);
                    $excel->setActiveSheetIndex(0)->setCellValue('M' . $numrow, $t->volume);
                    $excel->setActiveSheetIndex(0)->setCellValue('N' . $numrow, $t->chargeable);
                    $numrow++;
                }
            } else {
                // Jika tidak ada booking, tetap tampilkan nama partner dengan "Tidak ada bookingan"
                $excel->getActiveSheet()->mergeCells("E{$numrow}:N{$numrow}");
                $excel->setActiveSheetIndex(0)->setCellValue("B{$numrow}", $p->nama_pendaftar);
                $excel->setActiveSheetIndex(0)->setCellValue("C{$numrow}", $p->alamat_lengkap);
                $excel->setActiveSheetIndex(0)->setCellValue("D{$startRow}", $p->no_handphone . ' / ' . $p->no_handphone_alternatif);
                $excel->setActiveSheetIndex(0)->setCellValue("E{$numrow}", "Tidak ada bookingan");
                $numrow++;
            }
            $no++;
        }


        // Styling header
        $excel->getActiveSheet()->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['style'  => PHPExcel_Style_Border::BORDER_THIN],
                'right' => ['style'  => PHPExcel_Style_Border::BORDER_THIN],
                'bottom' => ['style'  => PHPExcel_Style_Border::BORDER_THIN],
                'left' => ['style'  => PHPExcel_Style_Border::BORDER_THIN]
            ]
        ]);


        $styleArray = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        // Terapkan border ke seluruh tabel dari A1 hingga kolom terakhir dan baris terakhir
        $lastRow = $numrow - 1; // Baris terakhir yang berisi data
        $excel->getActiveSheet()->getStyle("A1:N{$lastRow}")->applyFromArray($styleArray);

        // Export ke Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Manifest_Pickup_' . format_indo($tanggal_pickup) . '.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}
