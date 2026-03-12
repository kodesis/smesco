<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Delivery extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'pagination']);
        $this->load->helper(['string', 'url', 'date']);
        $this->load->model(['M_Customer', 'M_Booking', 'M_Delivery']);

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
        $data = [
            "title" => "Delivery",
            "segment" => "delivery",
            "pages" => "pages/delivery/v_delivery",
            "deliveries" => $this->M_Delivery->list_delivery(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function create_booking()
    {
        $data = [
            "title" => "Create Booking",
            "segment" => "booking",
            "pages" => "pages/booking/v_create_booking",
            "bookings" => $this->M_Booking->list_booking(),
            "customers" => $this->M_Customer->list_customer(),
        ];

        $this->load->view('pages/index', $data);
    }

    public function store_booking()
    {
        $awbs = $this->input->post('awb');
        $origins = $this->input->post('origin');
        $destinations = $this->input->post('destination');
        $commodities = $this->input->post('commodity');

        $data = [];

        $alert = [];

        if (is_array($awbs)) {
            for ($i = 0; $i < count($awbs); $i++) {
                $awb = trim($awbs[$i]);
                $origin = trim($origins[$i]);
                $destination = trim($destinations[$i]);
                $commodity = trim($commodities[$i]);

                $is_available = $this->M_Booking->is_available($awb);

                if (!$is_available) {
                    $data[] = [
                        'customer_id' => $this->input->post('customer_id'),
                        'awb' => $awb,
                        'origin' => $origin,
                        'destination' => $destination,
                        'commodity' => $commodity,
                        'created_by' => $this->session->userdata('user_id')
                    ];
                } else {
                    $alert[] = [
                        'message' => 'AWB ' . $awb . ' sudah ada sebelumnya.'
                    ];
                }
            }

            if (!empty($data)) {
                if ($this->M_Booking->insert_batch($data)) {
                    $this->session->set_flashdata('message_name', 'AWB berhasil ditambahkan. Silahkan lengkapi datanya!');
                } else {
                    $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');
                }
                redirect('booking', $alert);
            }
        } else {
            $this->session->set_flashdata('message_error', 'Silahkan isi awb!');
            redirect('booking');
        }
    }

    public function detailBooking($id)
    {
        $awb = $this->M_Booking->detailAwb($id);
        $data = [
            "title" => $id,
            "segment" => "booking",
            "pages" => "pages/booking/v_detail_booking",
            "bookings" => $this->M_Booking->list_booking(),
            "customers" => $this->M_Customer->list_customer(),
            "awb" => $awb,
            "details" => $this->M_Booking->detailItemAwb($awb['Id']),
        ];

        $this->load->view('pages/index', $data);
    }

    public function simpanDetailAwb($id)
    {
        // insert detail
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
        $nomor_uruts = $this->input->post('nomor_urut');
        $qtys = $this->input->post('qty');
        $chargeables = $this->input->post('chargeable');
        $nominals = $this->input->post('nominal');
        $addresss = $this->input->post('address');

        $awb_num = $this->input->post('awb_num');
        $total_qty = $this->input->post('total_qty');
        $total_chargeable = $this->input->post('total_chargeable');
        $subtotal = $this->input->post('subtotal');

        $detail = [];

        $data_booking = [
            'total_qty' => $total_qty,
            'total_chargeable' => $total_chargeable,
            'subtotal' => $subtotal,
        ];

        if (is_array($nomor_uruts)) {

            $this->M_Booking->delete_detail_awb($id);

            for ($i = 0; $i < count($nomor_uruts); $i++) {
                $nomor_urut = trim($nomor_uruts[$i]);
                $qty = trim($this->convertToNumber($qtys[$i]));
                $chargeable = trim($this->convertToNumber($chargeables[$i]));
                $nominal = trim($this->convertToNumber($nominals[$i]));
                $address = trim($addresss[$i]);

                $slug = $awb_num . '-' . $nomor_urut;

                if ($qty) {
                    $detail[] = [
                        'no_urut' => $nomor_urut,
                        'awb_id' => $id,
                        'slug' => $slug,
                        'qty' => $qty,
                        'chargeable' => $chargeable,
                        'nominal' => $nominal,
                        'alamat_pickup' => $address,
                        'created_by' => $this->session->userdata('user_id')
                    ];
                }
            }

            if (!empty($detail)) {
                if ($this->M_Booking->insert_batch_detail_awb($detail)) {

                    $this->M_Booking->updateBooking($id, $data_booking);

                    $this->session->set_flashdata('message_name', 'Detail AWB berhasil ditambahkan.');
                } else {
                    $this->session->set_flashdata('message_error', 'Gagal input. Silahkan ulangi lagi!');
                }
                redirect('booking');
            }
        } else {
            $this->session->set_flashdata('message_error', 'Silahkan isi awb!');
            redirect('booking');
        }
    }
    function convertToNumber($formattedNumber)
    {
        // Mengganti titik sebagai pemisah ribuan dengan string kosong
        $numberWithoutThousandsSeparator = str_replace('.', '', $formattedNumber);

        // Mengganti koma sebagai pemisah desimal dengan titik
        $standardNumber = str_replace(',', '.', $numberWithoutThousandsSeparator);

        // Mengonversi string ke float
        return (float) $standardNumber;
    }
}
