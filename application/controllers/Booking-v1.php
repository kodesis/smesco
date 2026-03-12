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
        $this->load->model(['M_Customer', 'M_Booking', 'M_Auth', 'M_Agent']);

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
        $keyword = ($this->input->post('keyword')) ? trim($this->input->post('keyword')) : (($this->session->userdata('search_booking')) ? $this->session->userdata('search_booking') : '');
        if ($keyword === null) $keyword = $this->session->userdata('search_booking');
        else $this->session->set_userdata('search_booking', $keyword);

        $config = [
            'base_url' => site_url('booking/index'),
            'total_rows' => $this->M_Booking->countBooking($keyword),
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
            "title" => "Booking",
            "page" => $page,
            "keyword" => $keyword,
            "segment" => "booking",
            "pages" => "pages/booking/v_booking",
            "bookings" => $this->M_Booking->listBookingPaginate($config["per_page"], $page, $keyword),
            "total_rows" => $config['total_rows'],
            "per_page" => $config['per_page'],
        ];

        $this->load->view('pages/index', $data);
    }

    public function create_booking()
    {
        $pages_booking = ($this->session->userdata('role_id') == '3') ? 'v_create_booking_v2' : 'v_create_booking';
        $data = [
            "title" => "Create Booking",
            "segment" => "booking",
            "pages" => "pages/booking/" . $pages_booking,
            "bookings" => $this->M_Booking->list_booking(),
            "customers" => $this->M_Customer->list_customer(),
            "drivers" => $this->M_Booking->list_driver(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function store_booking()
    {
        // Ambil nilai maksimum no_urut hari ini
        $max_num = $this->M_Booking->select_max_booking();

        $customer_id = $this->input->post('customer_id');


        // Memulai transaksi database
        $this->db->trans_begin();

        if ($customer_id == "__tambah__") {
            $nama_customer = trim($this->input->post('nama_customer'));
            $slug = url_title($nama_customer, 'dash', true);

            $cek = $this->M_Customer->is_available($slug);

            if ($cek) {
                $this->session->set_flashdata('message_error', "Customer a.n $nama_customer sudah tersedia");
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $data_customer = [
                    'nama_customer' => $nama_customer,
                    'telepon_customer' => trim($this->input->post('telepon_customer')),
                    'alamat_customer' => trim($this->input->post('alamat_customer')),
                    'slug' => $slug,
                ];

                $id_customer = $this->M_Customer->insertGetId($data_customer);
            }
        } else {
            $id_customer = $customer_id;
        }

        // Data input dari form
        $opsi_service = $this->input->post('opsi_service');
        $opsi_pickup = ($opsi_service == "door-to-door" || $opsi_service == "door-to-port") ? '1' : '0';
        $origins = $this->input->post('origin');
        $destinations = $this->input->post('destination');
        $qtys = $this->input->post('qty');
        $chargeables = $this->input->post('chargeable');
        $commodities = $this->input->post('commodity');
        $data = [];
        $alert = [];

        // Kode awal no_booking berdasarkan tanggal
        $kode = "KRX" . date('ymd');

        // Jika data origin adalah array
        if (is_array($origins)) {
            for ($i = 0; $i < count($origins); $i++) {

                if (!$max_num['max']) {
                    $bilangan = 1;
                } else {
                    $bilangan = $max_num['max'] + 1;
                }

                $no_urut = sprintf("%04d", $bilangan);
                $no_booking = $kode . $no_urut;

                $origin = trim($origins[$i]);
                $qty = trim($qtys[$i]);
                $chargeable = trim($chargeables[$i]);
                $destination = trim($destinations[$i]);
                $commodity = trim($commodities[$i]);

                $data[] = [
                    'no_urut' => $no_urut,
                    'no_booking' => $no_booking,
                    'customer_id' => $id_customer,
                    'id_driver' => ($this->session->userdata('role_id') != '3') ? $this->input->post('driver_id') : '',
                    'alamat_pickup' => trim($this->input->post('alamat_pickup')),
                    'gudang_tujuan' => ($this->session->userdata('role_id') != '3') ? trim($this->input->post('lokasi_gudang')) : '',
                    'opsi_service' => $opsi_service,
                    'opsi_pickup' => $opsi_pickup,
                    'set_pickup' => ($this->session->userdata('role_id') != '3') ? '1' : '0',
                    'origin' => $origin,
                    'destination' => $destination,
                    'total_qty' => $qty,
                    'total_chargeable' => $chargeable,
                    'commodity' => $commodity,
                    'created_by' => $this->session->userdata('user_id')
                ];

                $max_num['max'] = $bilangan;
            }

            if (!empty($data)) {
                if ($this->M_Booking->insert_batch($data)) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message_name', 'Booking berhasil ditambahkan. Silahkan lengkapi datanya!');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');
                }
                redirect('booking', $alert);
            }
        } else {
            $this->session->set_flashdata('message_error', 'Data tidak lengkap!');
            redirect('booking');
        }
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
        // Format no_urut menjadi 4 digit
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

        // echo '<pre>';
        // print_r($booking);
        // echo '</pre>';
        // exit;

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



        // $detail_booking = $this->M_Booking->detailBooking($no_booking);
        // echo '<pre>';
        // print_r($detail_booking);
        // echo '</pre>';
        // exit;

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

                // Generate QR code
                // $this->load->library('ciqrcode');
                // $config = [
                //     'cacheable' => true,
                //     'cachedir' => './assets/',
                //     'errorlog' => './assets/',
                //     'imagedir' => './assets/img/qrcode/', // Pastikan folder ini ada
                //     'quality' => true,
                //     'size' => '1024',
                //     'black' => [224, 255, 255], // warna hitam QR
                //     'white' => [70, 130, 180]  // warna putih QR
                // ];
                // $this->ciqrcode->initialize($config);

                // // Nama file QR code berdasarkan slug
                // $image_name = $slug . '.png';

                // $linkTracking = base_url("home/track");

                // // Set parameter QR code
                // $params = [
                //     'data' => $linkTracking,
                //     'level' => 'H', // H = High
                //     'size' => 10,
                //     'savename' => FCPATH . $config['imagedir'] . $image_name
                // ];
                // $this->ciqrcode->generate($params);

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

                    // notifikasi resi ke customer
                    $resi = $this->M_Booking->detailItemBooking($detailBooking['Id']);
                    $customer = $this->M_Customer->showById($detailBooking['customer_id'])['telepon_customer'];
                    $pesan_customer = $this->messageToCustomer($detailBooking, $resi);
                    $this->api_whatsapp->wa_notif($pesan_customer, $customer);

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

        $data = [
            'status_bayar' => $status
        ];

        // Lakukan pembaruan status pada database
        $this->M_Booking->updateAwbDetail($id, $data);

        if ($status == '1') {
            $message = "Resi sudah dibayar";
        } else {
            $message = "Resi belum dibayar";
        }
        // Kembalikan respon JSON
        $response = array('success' => true, 'status', 'message' => $message);
        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    function convertToNumber($formattedNumber)
    {
        $numberWithoutThousandsSeparator = str_replace('.', '', $formattedNumber);

        $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);

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
            $pesan .= "Lebih dari Sekadar Kiriman";

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

    public function confirmPickup($id)
    {
        $data = [
            'status_tracking' => '2',
        ];

        if ($this->M_Booking->updateAwbDetail($id, $data)) {
            $this->session->set_flashdata('message_name', "Konfirmasi barang $id sudah di-pickup berhasil. OTW warehouse");
        } else {
            $this->session->set_flashdata('message_error', "Gagal konfimasi pickup barang $id. Silahkan coba lagi!");
        }

        redirect('booking/list_detail');
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

    private function messageToAgent($detailBooking, $resi)
    {
        $destination = $detailBooking['destination'];

        // echo '<pre>';
        // print_r($resi);
        // echo '</pre>';
        // exit;
        foreach ($resi as $r) {
            $customer = $this->M_Customer->showById($detailBooking['customer_id'])['nama_customer'];
            $agent = $this->M_Customer->getAgentById($r->agent_id);
            $token = hash_hmac('sha256', $detailBooking['awb'] . $r->slug, "kriboexpress-kirimbro-sampaibro");
            $linkKonfirmasi = base_url("confirm/arrDestination/{$detailBooking['awb']}/$r->slug?token=$token");

            $pesan_agent = "*Halo, {$agent['nama_agent']}!*\nKami ingin mengonfirmasi bahwa paket sedang menuju {$destination}:\n";
            $pesan_agent .= "*• Nomor Resi: {$r->slug}*\n";
            $pesan_agent .= "*• Nama Pengirim: {$customer}*\n";
            $pesan_agent .= "*• Nama Penerima: {$agent['nama_agent']}*\n";
            $pesan_agent .= "*• Jenis Barang: {$detailBooking['commodity']}*\n";
            $pesan_agent .= "*• Berat Barang: {$r->chargeable}*\n";
            $pesan_agent .= "*• Kuantitas Barang: {$r->qty}*\n";
            $pesan_agent .= "*• Alamat Tujuan: {$r->kota_tujuan}*\n\n";
            $pesan_agent .= "*-- $r->slug: \n $linkKonfirmasi *\n\n";
            $pesan_agent .= "Setelah dikonfirmasi, status pengiriman akan diperbarui otomatis.\n*Salam hangat,*\nTim Smesco Express\nLebih dari Sekadar Kiriman";

            $this->api_whatsapp->wa_notif($pesan_agent, $agent['telepon_agent']);
        }
        // print_r($pesan_agent);
        // exit;
        return true;
    }

    private function messageToCustomer($detailBooking, $resi)
    {
        $linkTracking = base_url("home/track");
        $pesan_customer = "*Halo, {$detailBooking['nama_customer']}!*\n";
        $pesan_customer .= "Paket Anda sedang dalam perjalanan. Berikut nomor resi:\n";
        foreach ($resi as $r) {
            $pesan_customer .= "*-- $r->slug*\n";
        }
        $pesan_customer .= "\nAnda dapat melacak status pengiriman di:\n $linkTracking \n\n*Salam hangat,*\nTim Smesco Express\nLebih dari Sekadar Kiriman";
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
        $pesan .= "Lebih dari Sekadar Kiriman";
        return $pesan;
    }


    public function arriveDestination($id)
    {

        $detailResi = $this->M_Booking->getItemAwb($id);

        $customer_id = $this->M_Booking->getBookingById($detailResi['booking_id'])['customer_id'];

        $customer_phone = $this->M_Customer->showById($customer_id);
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

    public function print_resi($no_resi)
    {
        $resi =  $this->M_Booking->getItemAwb($no_resi);
        $booking = $this->M_Booking->getBookingById($resi['booking_id']);

        $linkTracking = base_url("home/track");

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
            'booking' => $booking,
            'pengirim' => $this->M_Customer->showById($booking['customer_id'])['nama_customer'],
            'penerima' => $this->M_Customer->showById($resi['agent_id'])['nama_customer'],
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

        // echo '<pre>';
        // print_r($booking);
        // print_r($resi);
        // echo '</pre>';
        // exit;

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
        $pesan_customer .= "*• Nomor Resi: {$detailResi['slug']}*\n";
        $pesan_customer .= "*• Kuantitas Barang: {$detailResi['qty']}*\n";
        $pesan_customer .= "*• Berat Barang: {$detailResi['chargeable']}*\n\n";
        $pesan_customer .= "Terima kasih telah menggunakan layanan kami. Jika ada pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi kami.\n\n";
        $pesan_customer .= "*Salam hangat,*\n";
        $pesan_customer .= "Tim Smesco Express\n";
        $pesan_customer .= "Lebih dari Sekadar Kiriman";

        return $pesan_customer;
    }
}
