<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Confirm extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation', 'Api_Whatsapp']);
        $this->load->helper('string');
        $this->load->model(['M_Auth', 'M_Booking', 'M_Customer']);
        $this->load->helper('date');
    }

    public function pickup()
    {
        $no_booking = $this->uri->segment(3);
        $driver_id = $this->uri->segment(4);
        $token = $_GET['token'];
        $secret_key = "kriboexpress-kirimbro";

        // Generate token hash ulang dan cocokkan dengan yang ada di URL
        $valid_token = hash_hmac('sha256', $no_booking . $driver_id, $secret_key);

        $cek = $this->M_Booking->is_available($no_booking);

        $booking = $this->M_Booking->detailBooking($no_booking);

        if ($cek) {
            if ($booking['confirm_pickup'] == '1') {
                $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Link expired.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>');
                $status = '0';
            } else {
                if ($token === $valid_token) {
                    $this->session->set_flashdata('message_name', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Link valid.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
                    $status = '1';
                } else {
                    $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Invalid token.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
                    $status = '0';
                }
            }
        } else {
            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					No. Booking tidak ditemukan.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>');
            $status = '0';
        }

        $data = [
            'no_booking' => $booking['no_booking'],
            'title' => 'Confirm pickup',
            'pages' => 'pages/confirm/v_pickup',
            'status' => $status,
        ];

        $this->load->view('pages/auth/index', $data);
    }

    public function finishPickup()
    {
        $no_booking = $this->input->post('kode_booking');

        // $detailBooking = $this->M_Booking->detailBooking($no_booking);
        // print_r($detailBooking);
        // exit;

        $photo = $_FILES['file_upload']['name']; // Nama file 
        // Mendapatkan extension
        $pathInfo = pathinfo($photo);
        $extension = $pathInfo['extension']; // Extension file
        $newPhotoFileName = $no_booking . '.' . $extension;

        $config = [
            'upload_path' => FCPATH . 'assets/photo-pickup/',
            'allowed_types' => 'JPG|jpg|JPEG|jpeg',
            'overwrite' => TRUE,
            'max_size' => '1200',
            'file_name' => $newPhotoFileName,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_upload')) {

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal konfirmasi pickup. Silahkan coba lagi! ' . $this->upload->display_errors() . ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');

            redirect('confirm/failed');
        } else {
            // data sales order
            $detailBooking = $this->M_Booking->detailBooking($no_booking);

            $data_resi = [
                'status_tracking' => '2',
            ];

            $this->db->trans_begin();

            $this->M_Booking->updateAwbDetailByBookingId($detailBooking['Id'], $data_resi);

            $data_booking = [
                'confirm_pickup' => '1',
            ];

            if ($this->M_Booking->updateBooking($detailBooking['Id'], $data_booking)) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message_name', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Barang sudah di-pickup. Menuju gudang.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
            } else {
                $this->db->trans_rollback();

                $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal konfirmasi pickup. Silahkan coba lagi!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
            }

            redirect('confirm/success');
        }
    }

    public function success()
    {
        $data = [
            'title' => 'Pickup confirmed',
            'pages' => 'pages/confirm/v_message',
            'status' => 'success',
        ];

        $this->load->view('pages/auth/index', $data);
    }

    public function failed()
    {
        $data = [
            'title' => 'Failed confirm pickup',
            'pages' => 'pages/confirm/v_message',
            'status' => 'success',
        ];

        $this->load->view('pages/auth/index', $data);
    }

    public function arrDestination()
    {
        $no_resi = $this->uri->segment(3);
        $token = $_GET['token'];
        $secret_key = "kriboexpress-kirimbro-sampaibro";

        // Generate token hash ulang dan cocokkan dengan yang ada di URL
        $valid_token = hash_hmac('sha256', $no_resi, $secret_key);

        $cek = $this->M_Booking->cekResi($no_resi);

        $detail = $this->M_Booking->getResi($no_resi);

        if ($cek) {
            if ($detail['status_tracking'] == '3') {
                if ($token === $valid_token) {
                    $this->session->set_flashdata('message_name', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Valid link.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
                    $status = '1';
                } else {
                    $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Invalid link.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');
                    $status = '0';
                }
            } else {
                $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Link expired.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>');
                $status = '0';
            }
        } else {
            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
					Resi not found.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>');
            $status = '0';
        }

        $data = [
            'resi' => $detail,
            'agent' => $this->M_Customer->getAgentById($detail['agent_id'])['nama_agent'],
            'title' => 'Confirm arrival',
            'pages' => 'pages/confirm/v_arrival',
            'status' => $status,
            'commodity' => $detail['commodity']
        ];

        // print_r($data);
        // exit;

        $this->load->view('pages/auth/index', $data);
    }

    public function finishDelivery()
    {
        $resi = $this->input->post('resi');

        $detailResi = $this->M_Booking->getResi($resi);

        // print_r($detailResi);
        // exit;


        $photo = $_FILES['file_upload']['name']; // Nama file 

        // Ambil extension
        $pathInfo = pathinfo($photo);
        $extension = $pathInfo['extension']; // Extension file
        $newPhotoFileName = $resi . '.' . $extension;

        $config = [
            'upload_path' => FCPATH . 'assets/photo-delivered/',
            'allowed_types' => 'JPG|jpg|JPEG|jpeg',
            'overwrite' => TRUE,
            'max_size' => '1200',
            'file_name' => $newPhotoFileName,
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_upload')) {

            $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal konfirmasi pickup. Silahkan coba lagi! ' . $this->upload->display_errors() . ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');

            redirect('confirm/failed');
        } else {
            $data_resi = [
                'status_tracking' => '4',
                'confirm_arrival' => '1',
            ];

            $this->db->trans_begin();

            if ($this->M_Booking->updateResi($resi, $data_resi)) {
                $this->db->trans_commit();

                $pesan_pengirim = $this->messageToCustomer($detailResi);
                $pesan_penerima = $this->messageToCustomer($detailResi);

                $this->api_whatsapp->wa_notif($pesan_pengirim, $detailResi['telepon_pengirim']);
                $this->api_whatsapp->wa_notif($pesan_penerima, $detailResi['telepon_penerima']);

                $this->session->set_flashdata('message_name', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Resi dengan nomor ' . $resi . ' telah dikonfirmasi tiba di kota tujuan.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');

                redirect('confirm/success');
            } else {
                $this->db->trans_rollback();

                $this->session->set_flashdata('message_name', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal konfirmasi resi tiba di tujuan. Silahkan coba lagi!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>');

                redirect('confirm/failed');
            }
        }
    }

    private function messageToCustomer($detailResi)
    {
        $pesan_customer = "*Halo, Sobat Smesco Express!*\n";
        $pesan_customer .= "Kami dengan senang hati menginformasikan bahwa paket Anda telah tiba di tujuan dengan selamat. Berikut adalah rincian pengirimannya:\n";
        $pesan_customer .= "*• Nomor Resi: {$detailResi['no_resi']}*\n";
        $pesan_customer .= "*• Kuantitas Barang: {$detailResi['qty']}*\n";
        $pesan_customer .= "*• Berat Barang: {$detailResi['chargeable']}*\n\n";
        $pesan_customer .= "Terima kasih telah menggunakan layanan kami. Jika ada pertanyaan atau membutuhkan bantuan lebih lanjut, jangan ragu untuk menghubungi kami.\n\n";
        $pesan_customer .= "*Salam hangat,*\n";
        $pesan_customer .= "Tim Smesco Express\n";
        $pesan_customer .= "_Kirim Bro_";

        return $pesan_customer;
    }
}
