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

	public function approve_payment()
	{
		if ($this->input->method() !== 'post') {
			show_404();
		}

		$no_resi = trim((string) $this->input->post('no_resi', TRUE));
		if ($no_resi === '') {
			$this->session->set_flashdata('error', 'No. resi tidak valid.');
			redirect('finance/verifikasi');
		}
		
		$sess = $this->session->userdata('user');
		$now  = date('Y-m-d H:i:s');

		$shipment = $this->db
			->select('s.*, a.is_kecamatan')
			->from('shipments s')
			->join('agents a', 'a.id = s.agent_id', 'left')
			->where('s.no_resi', $no_resi)
			->get()->row();

		if (!$shipment) {
			$this->session->set_flashdata('error', 'Resi tidak ditemukan.');
			redirect('finance/verifikasi');
		}
		if (($shipment->is_kecamatan ?? '0') === '1') {
			$this->session->set_flashdata('error', 'Pembayaran shipment kecamatan dikonfirmasi oleh agennya.');
			redirect('finance/verifikasi');
		}
		if ($shipment->status !== 'BOOKED') {
			$this->session->set_flashdata('error', "Resi $no_resi berstatus {$shipment->status}, tidak bisa diverifikasi.");
			redirect('finance/verifikasi');
		}

		$next_status = 'READY_TO_PICKUP';

		$this->db->trans_start();

		$this->db->where('id', $shipment->id)->where('status', 'BOOKED')->update('shipments', [
			'payment_status'       => 'PAID',
			'status'               => $next_status,
			'payment_confirmed_by' => (int) $sess['id'],
			'payment_confirmed_at' => $now,
			'updated_by'           => (int) $sess['id'],
			'updated_at'           => $now,
		]);

		if ($this->db->affected_rows() !== 1) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', 'Resi sudah diproses sebelumnya.');
			redirect('finance/verifikasi');
		}

		$this->db->insert('shipment_tracking', [
			'shipment_id' => $shipment->id,
			'status'      => $next_status,
			'location'    => $shipment->origin,
			'note'        => 'Pembayaran dikonfirmasi. Barang siap diproses lebih lanjut.',
			'created_by'  => (int) $sess['id'],
			'created_at'  => $now,
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('error', 'Gagal melakukan verifikasi.');
			redirect('finance/verifikasi');
		}

		$this->_send_finance_wa_notif($shipment, 'APPROVE', $next_status);
		$this->session->set_flashdata('success', "Pembayaran resi $no_resi berhasil diverifikasi.");
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

	// ================= SETORAN AGEN KECAMATAN =================

	/** Semua role di $allowed_roles boleh melihat. Hanya role ini yang boleh verifikasi/tolak. */
	protected $settlement_verifiers = ['finance-kribo', 'superadmin'];

	private function _can_verify_settlement()
	{
		$sess = $this->session->userdata('user');
		return in_array($sess['role_slug'] ?? '', $this->settlement_verifiers, TRUE);
	}

	private function _require_verifier()
	{
		if (!$this->_can_verify_settlement()) {
			show_error('Akses tidak diizinkan.', 403);
		}
	}

	public function settlements()
	{
		$this->load->model('M_AgentSettlement');

		$status = $this->input->get('status', TRUE);
		$status = in_array($status, ['PENDING', 'VERIFIED', 'REJECTED', 'ALL'], TRUE) ? $status : 'PENDING';
		$q      = trim((string) $this->input->get('q', TRUE));

		$total    = $this->M_AgentSettlement->count_for_finance($status, $q);
		$paginate = $this->paginate($total, 15, ['status' => $status, 'q' => $q]);

		$this->render('app/pages/finance/settlement_index', array_merge([
			'title'       => 'Setoran Agen Kecamatan',
			'rows'        => $this->M_AgentSettlement->get_for_finance($status, $q, $paginate['per_page'], $paginate['offset']),
			'total'       => $total,
			'filters'     => ['status' => $status, 'q' => $q],
			'pending_sum' => $this->M_AgentSettlement->get_pending_summary(),
		], $paginate));
	}

	public function settlement_detail($id = NULL)
	{
		if ($id === NULL || !ctype_digit((string) $id)) {
			show_404();
		}
		$this->load->model('M_AgentSettlement');

		$st = $this->M_AgentSettlement->get_detail((int) $id);
		if (!$st) {
			show_404();
		}

		$this->render('app/pages/finance/settlement_detail', [
			'title'      => 'Setoran ' . $st->settlement_no,
			'st'         => $st,
			'items'      => $this->M_AgentSettlement->get_items((int) $id),
			'can_verify' => $this->_can_verify_settlement(),
		]);
	}

	public function settlement_verify()
	{
		$this->_require_verifier();
		if ($this->input->method() !== 'post') {
			show_404();
		}
		$this->load->model('M_AgentSettlement');

		$id   = (int) $this->input->post('id');
		$sess = $this->session->userdata('user');

		$res = $this->M_AgentSettlement->verify($id, (int) $sess['id']);
		if (empty($res['status'])) {
			$this->session->set_flashdata('error', $res['message']);
			redirect('finance/settlement_detail/' . $id);
		}

		$st = $this->M_AgentSettlement->get_detail($id);
		$this->_notify_settlement($st, 'VERIFIED');
		$this->session->set_flashdata('success', 'Setoran ' . html_escape($st->settlement_no) . ' diverifikasi. Semua resi di dalamnya sekarang LUNAS ke Smesco.');
		redirect('finance/settlement_detail/' . $id);
	}

	public function settlement_reject()
	{
		$this->_require_verifier();
		if ($this->input->method() !== 'post') {
			show_404();
		}
		$this->load->model('M_AgentSettlement');

		$id   = (int) $this->input->post('id');
		$note = trim((string) $this->input->post('reject_note', TRUE));
		$sess = $this->session->userdata('user');

		if (mb_strlen($note) < 5) {
			$this->session->set_flashdata('error', 'Alasan penolakan wajib diisi (minimal 5 karakter).');
			redirect('finance/settlement_detail/' . $id);
		}

		$res = $this->M_AgentSettlement->reject($id, (int) $sess['id'], $note);
		if (empty($res['status'])) {
			$this->session->set_flashdata('error', $res['message']);
			redirect('finance/settlement_detail/' . $id);
		}

		$st = $this->M_AgentSettlement->get_detail($id);
		$this->_notify_settlement($st, 'REJECTED', $note);
		$this->session->set_flashdata('success', 'Setoran ' . html_escape($st->settlement_no) . ' ditolak. Resi dikembalikan ke agen.');
		redirect('finance/settlement_detail/' . $id);
	}

	/** Penyaji bukti. File ada di luar webroot, jadi hanya lewat sini, dan hanya untuk role finance. */
	public function settlement_proof($id = NULL)
	{
		if ($id === NULL || !ctype_digit((string) $id)) {
			show_404();
		}
		$this->load->model('M_AgentSettlement');

		$st = $this->M_AgentSettlement->get_detail((int) $id);
		if (!$st) {
			show_404();
		}

		$file  = basename((string) $st->proof_file);   // buang path apa pun
		$path  = M_AgentSettlement::proof_dir() . $file;
		$ext   = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		$mimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];

		if (!isset($mimes[$ext]) || !is_file($path)) {
			show_404();
		}

		$this->output
			->set_content_type($mimes[$ext])
			->set_header('X-Content-Type-Options: nosniff')
			->set_header('Cache-Control: private, no-store')
			->set_header('Content-Disposition: inline; filename="bukti-' . $st->settlement_no . '.' . $ext . '"')
			->set_output(file_get_contents($path));
	}

	private function _notify_settlement($st, $type, $note = '')
	{
		if (!$st || empty($st->agent_phone)) {
			log_message('error', 'WA setoran: nomor agen tidak ditemukan untuk ' . ($st->settlement_no ?? '?'));
			return;
		}

		$total = 'Rp ' . number_format((float) $st->total_amount, 0, ',', '.');

		if ($type === 'VERIFIED') {
			$pesan = "*SMESCO FINANCE — SETORAN DIVERIFIKASI*\n\n" .
				"Setoran *{$st->settlement_no}* sebesar *$total* telah *DIVERIFIKASI*.\n" .
				"Semua resi di dalamnya sekarang berstatus lunas ke Smesco.";
		} else {
			$pesan = "*SMESCO FINANCE — SETORAN DITOLAK*\n\n" .
				"Setoran *{$st->settlement_no}* sebesar *$total* *DITOLAK*.\n" .
				"Alasan: $note\n\n" .
				"Resi sudah dikembalikan ke daftar setoran. Silakan kirim ulang dengan bukti transfer yang benar.";
		}

		try {
			$this->api_whatsapp->wa_notif_v2($st->agent_phone, $pesan);
		} catch (Exception $e) {
			log_message('error', 'WA setoran error [' . $type . ']: ' . $e->getMessage());
		}
	}
}
