<?php defined('BASEPATH') or exit('No direct script access allowed');

class Finance extends Authenticated_Controller
{
	protected $allowed_roles = ['admin-kribo', 'finance-kribo', 'superadmin'];

	public function __construct()
	{
		parent::__construct();
		$this->_check_access();

		$sess = $this->session->userdata('user');

		// Kita pinjam model Shipment karena datanya ngambil dari tabel yang sama
		$this->load->model('M_Shipment');
		$this->load->library(['pdfgenerator', 'api_whatsapp']);
	}

	// Halaman Daftar Verifikasi Pembayaran
	public function verifikasi()
	{
		// 1. Ambil Filter Pencarian
		$filters = [
			'q'     => $this->input->get('q', TRUE),
			'start' => $this->input->get('start', TRUE),
			'end'   => $this->input->get('end', TRUE),

			// --- INI KUNCINYA BRO ---
			// Kita paksakan filter ini agar yang muncul cuma yang butuh di-approve
			'payment_type'   => 'TRANSFER',
			'payment_status' => 'UPLOADED'
		];

		// 2. Hitung Total terfilter (Agent ID = NULL karena Finance lihat semua cabang)
		$total = $this->M_Shipment->count_filtered(NULL, $filters);

		// 3. Hitung Pagination
		$per_page = 15;
		$paginate = $this->paginate($total, $per_page, $filters);

		// 4. Ambil Data Paged
		$shipments = $this->M_Shipment->get_paged(
			$paginate['per_page'],
			$paginate['offset'],
			NULL, // Finance lihat semua cabang
			$filters
		);

		// 5. Hitung Ringkasan (Opsional: Total Rupiah yang Menunggu Verifikasi)
		$total_pending_amount = 0;
		foreach ($shipments as $s) {
			$total_pending_amount += $s->total_amount;
		}

		$data = array_merge([
			'title'                => 'Verifikasi Bukti Transfer',
			'shipments'            => $shipments,
			'total'                => $total,
			'filters'              => $filters,
			'total_pending_amount' => $total_pending_amount // Lempar ke view buat bikin card stat
		], $paginate);

		$this->render('app/pages/finance/verifikasi_index', $data);
	}

	public function approve_payment($no_resi)
	{
		$this->db->trans_start();

		// 1. Ambil data shipment
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();
		if (!$shipment) {
			$this->session->set_flashdata('error', 'Resi tidak ditemukan.');
			redirect('finance/verifikasi');
		}

		// 2. Tentukan status berikutnya
		// Jika ada pickup_rate_id -> kurir pusat jemput (Waiting Pickup)
		// Jika tidak ada -> barang dianggap sudah di kantor (Received at Origin)
		// $next_status = ($shipment->pickup_rate_id != NULL) ? 'READY_TO_PICKUP' : 'RECEIVED_AT_ORIGIN';
		$next_status = 'READY_TO_PICKUP';

		// 3. Update Status Shipment
		$this->db->update('shipments', [
			'payment_status' => 'PAID',
			'status'         => $next_status,
			'updated_at'     => date('Y-m-d H:i:s')
		], ['no_resi' => $no_resi]);

		// 4. Catat di Tracking (History)
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => $next_status,
			'location'    => $shipment->origin,
			'note'        => 'Pembayaran dikonfirmasi. Barang siap diproses lebih lanjut.',
			'created_by'  => $this->session->userdata('user')['id']
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) {
			// 5. KIRIM WA NOTIFIKASI KE CUSTOMER
			$this->_send_finance_wa_notif($shipment, 'APPROVE', $next_status);

			$this->session->set_flashdata('success', "Pembayaran resi $no_resi berhasil diverifikasi.");
		} else {
			$this->session->set_flashdata('error', "Gagal melakukan verifikasi.");
		}

		redirect('finance/verifikasi');
	}

	public function reject_payment($no_resi)
	{
		$this->db->trans_start();

		// 1. Ambil data shipment
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();
		if (!$shipment) {
			$this->session->set_flashdata('error', 'Resi tidak ditemukan.');
			redirect('finance/verifikasi');
		}

		// 2. Perpanjang waktu expired (Beri waktu 1 jam untuk upload ulang)
		$new_expired = date('Y-m-d H:i:s', strtotime('+1 hour'));

		// 3. Update Database
		$this->db->update('shipments', [
			'payment_status'     => 'REJECTED',
			'payment_expired_at' => $new_expired,
			'updated_at'         => date('Y-m-d H:i:s')
		], ['no_resi' => $no_resi]);

		// 4. Catat di Tracking
		$this->db->insert('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => 'BOOKED', // Status fisik tetap tertahan di BOOKED
			'location'    => $shipment->origin,
			'note'        => 'Bukti pembayaran ditolak oleh Finance. Menunggu upload ulang.',
			'created_by'  => $this->session->userdata('user')['id']
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) {
			// 5. Tembak WA Notifikasi Reject
			$this->_send_finance_wa_notif($shipment, 'REJECT');

			$this->session->set_flashdata('success', "Bukti transfer resi $no_resi berhasil DITOLAK.");
		} else {
			$this->session->set_flashdata('error', "Gagal memproses penolakan.");
		}

		redirect('finance/verifikasi');
	}

	public function test_wa()
	{
		$next_status = 'READY_TO_PICKUP';
		$no_resi = 'SMC2604160001';
		$shipment = $this->db->get_where('shipments', ['no_resi' => $no_resi])->row();
		$this->_send_finance_wa_notif($shipment, 'APPROVE', $next_status);
	}

	private function _send_finance_wa_notif($shipment, $type, $next_status = '')
	{
		// 1. Ambil Data PIC (Mitra/Agen yang bikin booking)
		// Asumsi tabel lu namanya 'users' dan kolom nomor hpnya 'phone' atau 'no_wa'
		// Sesuaikan dengan nama tabel dan kolom di database lu bro!
		$pic = $this->db->get_where('users', ['id' => $shipment->created_by])->row_array()['agent_id'];
		$agent = $this->db->get_where('agents', ['id' => $pic])->row();

		if (!$agent || empty($agent->phone)) {
			log_message('error', 'Gagal WA Notif Finance: Nomor HP PIC tidak ditemukan untuk resi ' . $shipment->no_resi);
			return; // Stop fungsi kalau nomor PIC nggak ada
		}

		$pic_phone = $agent->phone; // <-- Pastikan ini sesuai kolom DB lu
		$url = base_url('home/confirm_payment/' . $shipment->no_resi);
		$pesan = "";

		if ($type === 'APPROVE') {
			$pesan = "*SMESCO FINANCE — APPROVAL*\n\n" .
				"Halo Tim Mitra / PIC,\n" .
				"Pembayaran untuk resi *{$shipment->no_resi}* (Pengirim: {$shipment->sender_name}) telah *BERHASIL DIVERIFIKASI*.\n\n" .
				"Status Resi: *$next_status*\n" .
				"Pembayaran sudah dikonfirmasi, silakan melanjutkan proses operasional selanjutnya (Manifest / Penjemputan).\n\n" .
				"_Semangat bertugas!_ 🚀";
		} elseif ($type === 'REJECT') {
			$pesan = "*SMESCO FINANCE — REJECTED*\n\n" .
				"Halo Tim Mitra / PIC,\n" .
				"Bukti transfer untuk resi *{$shipment->no_resi}* (Pengirim: {$shipment->sender_name}) *DITOLAK* oleh pusat (Bukti tidak valid / nominal tidak sesuai).\n\n" .
				"Mohon bantu infokan ke customer untuk segera mengunggah ulang bukti transfer yang benar melalui tautan berikut:\n" .
				"🔗 $url\n\n" .
				"*(Tautan ini berlaku hingga 1 jam ke depan)*";
		}

		// 2. Eksekusi API
		if (!empty($pesan)) {
			try {
				$this->api_whatsapp->wa_notif_v2($pic_phone, $pesan);
			} catch (Exception $e) {
				log_message('error', 'Finance WA Notif Error [' . $type . ']: ' . $e->getMessage());
			}
		}
	}
}
