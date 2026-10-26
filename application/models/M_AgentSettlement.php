<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_AgentSettlement extends CI_Model
{
	const TAGIHAN_UNPAID  = 'UNPAID';
	const TAGIHAN_PENDING = 'PENDING';
	const TAGIHAN_PAID    = 'PAID';
	const SETTLE_ALL_OR_NOTHING = true;

	/** Semua resi yang masih berutang ke Smesco (termasuk yang customernya belum lunas). */
	public function get_unpaid_shipments($agent_id)
	{
		$date_closing = date('Y-m-d 15:00:00');
		return $this->db
			->select('id, no_resi, destination, status, payment_status, nominal_tagihan_smesco, created_at')
			->where('agent_id', (int) $agent_id)
			->where('category', 'INTERNATIONAL')
			->where('status_tagihan_smesco', self::TAGIHAN_UNPAID)
			->where('status !=', 'CANCELLED')
			->where('created_at <', $date_closing)
			->where('payment_status', 'PAID')
			->order_by('created_at', 'ASC')
			->get('shipments')
			->result();
	}

	/** Untuk card: outstanding (semua UNPAID) vs siap disetor (UNPAID + customer PAID) vs pending. */
	public function get_summary($agent_id)
	{
		$row = $this->db->query("
			SELECT
			COALESCE(SUM(CASE WHEN status_tagihan_smesco = 'UNPAID' AND payment_status = 'PAID' THEN nominal_tagihan_smesco END), 0) AS outstanding,
			COALESCE(SUM(CASE WHEN status_tagihan_smesco = 'PENDING' THEN nominal_tagihan_smesco END), 0) AS pending
			FROM shipments
			WHERE agent_id = ? AND category = 'INTERNATIONAL' AND status <> 'CANCELLED'
		", [(int) $agent_id])->row_array();

		return $row;
	}

	public function get_history($agent_id, $limit = 50)
	{
		return $this->db
			->where('agent_id', (int) $agent_id)
			->order_by('id', 'DESC')
			->limit($limit)
			->get('agent_settlements')
			->result();
	}

	/**
	 * Submit setoran. Total SELALU dihitung dari DB, bukan dari browser.
	 * Return ['status'=>bool, 'message'=>string, 'settlement_id'=>int, 'settlement_no'=>string]
	 */
	public function submit($agent_id, $user_id, array $ids, $transfer_amount, $transfer_date, $proof_file)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
		if (!$ids) {
			return ['status' => false, 'message' => 'Pilih minimal satu resi.'];
		}

		$now = date('Y-m-d H:i:s');
		$this->db->trans_begin();

		// Kunci baris + validasi syarat. Satu query = satu sumber kebenaran.
		$rows = $this->db->query("
			SELECT id, nominal_tagihan_smesco
			FROM shipments
			WHERE id IN ?
			  AND agent_id = ?
			  AND category = 'INTERNATIONAL'
			  AND status_tagihan_smesco = 'UNPAID'
			  AND payment_status = 'PAID'
			  AND status <> 'CANCELLED'
			FOR UPDATE
		", [$ids, (int) $agent_id])->result();

		if (count($rows) !== count($ids)) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Ada resi yang tidak memenuhi syarat atau sudah diproses. Muat ulang halaman.'];
		}

		if (self::SETTLE_ALL_OR_NOTHING) {
			$all = $this->db
				->where('agent_id', (int) $agent_id)->where('category', 'INTERNATIONAL')
				->where('status_tagihan_smesco', self::TAGIHAN_UNPAID)->where('payment_status', 'PAID')
				->where('status !=', 'CANCELLED')
				->count_all_results('shipments');
			if ($all !== count($ids)) {
				$this->db->trans_rollback();
				return ['status' => false, 'message' => 'Semua resi yang sudah lunas harus disetor sekaligus.'];
			}
		}

		if ($all !== count($ids)) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Daftar resi berubah sejak halaman dibuka (ada resi baru yang sudah lunas). Muat ulang halaman, lalu kirim ulang.'];
		}

		$total = 0;
		foreach ($rows as $r) {
			$total += (float) $r->nominal_tagihan_smesco;
		}
		$total = round($total, 2);

		if ($total <= 0 || abs($total - round((float) $transfer_amount, 2)) >= 0.01) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Nominal transfer (Rp ' . number_format($transfer_amount, 0, ',', '.') . ') tidak sama dengan total tagihan resi terpilih (Rp ' . number_format($total, 0, ',', '.') . ').'];
		}

		$this->db->insert('agent_settlements', [
			'settlement_no'   => 'TMP' . uniqid(),
			'agent_id'        => (int) $agent_id,
			'total_amount'    => $total,
			'transfer_amount' => $total,
			'transfer_date'   => $transfer_date,
			'proof_file'      => $proof_file,
			'status'          => 'PENDING',
			'submitted_by'    => (int) $user_id,
			'submitted_at'    => $now,
		]);
		$sid = (int) $this->db->insert_id();
		$no  = 'STL-' . date('Ym') . '-' . str_pad($sid, 5, '0', STR_PAD_LEFT);
		$this->db->where('id', $sid)->update('agent_settlements', ['settlement_no' => $no]);

		$items = [];
		foreach ($rows as $r) {
			$items[] = ['settlement_id' => $sid, 'shipment_id' => (int) $r->id, 'amount' => $r->nominal_tagihan_smesco];
		}
		$this->db->insert_batch('agent_settlement_items', $items);

		$this->db
			->where_in('id', $ids)
			->where('agent_id', (int) $agent_id)
			->where('status_tagihan_smesco', self::TAGIHAN_UNPAID)
			->update('shipments', [
				'status_tagihan_smesco' => self::TAGIHAN_PENDING,
				'settlement_id'         => $sid,
				'updated_by'            => (int) $user_id,
				'updated_at'            => $now,
			]);

		if ($this->db->affected_rows() !== count($ids) || $this->db->trans_status() === false) {
			$this->db->trans_rollback();
			return ['status' => false, 'message' => 'Setoran gagal disimpan. Coba lagi.'];
		}

		$this->db->trans_commit();
		return ['status' => true, 'message' => 'OK', 'settlement_id' => $sid, 'settlement_no' => $no];
	}

	/** Folder bukti, di luar webroot. Sama dengan yang dipakai settlement_submit(). */
	public static function proof_dir()
	{
		return dirname(FCPATH) . '/storage/settlement_proofs/';
	}

	private function _fail($msg)
	{
		$this->db->trans_rollback();
		return ['status' => false, 'message' => $msg];
	}

	private function _list_query($status, $q)
	{
		$this->db->from('agent_settlements st')->join('agents a', 'a.id = st.agent_id', 'left');

		if (in_array($status, ['PENDING', 'VERIFIED', 'REJECTED'], TRUE)) {
			$this->db->where('st.status', $status);
		}
		if ($q !== '') {
			$this->db->group_start()
				->like('st.settlement_no', $q)
				->or_like('a.name', $q)
				->or_like('a.code', $q)
				->group_end();
		}
	}

	public function count_for_finance($status, $q)
	{
		$this->_list_query($status, $q);
		return $this->db->count_all_results();
	}

	public function get_for_finance($status, $q, $limit, $offset)
	{
		$this->_list_query($status, $q);
		return $this->db
			->select('st.*, a.name AS agent_name, a.code AS agent_code,
			(SELECT COUNT(*) FROM agent_settlement_items i WHERE i.settlement_id = st.id) AS item_count', FALSE)
			->order_by('st.id', 'DESC')
			->limit((int) $limit, (int) $offset)
			->get()->result();
	}

	public function get_pending_summary()
	{
		return $this->db->query("
		SELECT COUNT(*) AS n, COALESCE(SUM(total_amount), 0) AS amount
		FROM agent_settlements WHERE status = 'PENDING'
	")->row_array();
	}

	public function get_detail($id)
	{
		return $this->db
			->select('st.*, a.name AS agent_name, a.code AS agent_code, a.phone AS agent_phone,
			u1.name AS submitted_name, u2.name AS verified_name')
			->from('agent_settlements st')
			->join('agents a', 'a.id = st.agent_id', 'left')
			->join('users u1', 'u1.id = st.submitted_by', 'left')
			->join('users u2', 'u2.id = st.verified_by', 'left')
			->where('st.id', (int) $id)
			->get()->row();
	}

	public function get_items($id)
	{
		return $this->db
			->select('i.amount, s.id, s.no_resi, s.destination, s.sender_name, s.status, s.payment_status')
			->from('agent_settlement_items i')
			->join('shipments s', 's.id = i.shipment_id', 'left')
			->where('i.settlement_id', (int) $id)
			->order_by('s.created_at', 'ASC')
			->get()->result();
	}

	/** Finance memverifikasi satu batch utuh: setoran VERIFIED, semua resi PAID. */
	public function verify($id, $user_id)
	{
		$id  = (int) $id;
		$now = date('Y-m-d H:i:s');
		$this->db->trans_begin();

		$st = $this->db->query(
			"SELECT id FROM agent_settlements WHERE id = ? AND status = 'PENDING' FOR UPDATE",
			[$id]
		)->row();
		if (!$st) {
			return $this->_fail('Setoran tidak ditemukan atau sudah diproses.');
		}

		$n = $this->db->where('settlement_id', $id)->count_all_results('agent_settlement_items');

		$this->db->where('settlement_id', $id)
			->where('status_tagihan_smesco', self::TAGIHAN_PENDING)
			->update('shipments', [
				'status_tagihan_smesco'  => self::TAGIHAN_PAID,
				'tagihan_smesco_paid_at' => $now,
				'updated_by'             => (int) $user_id,
				'updated_at'             => $now,
			]);

		if ($n === 0 || $this->db->affected_rows() !== $n) {
			return $this->_fail('Status resi dalam setoran tidak konsisten. Tidak ada yang diubah.');
		}

		$this->db->where('id', $id)->update('agent_settlements', [
			'status'      => 'VERIFIED',
			'verified_by' => (int) $user_id,
			'verified_at' => $now,
		]);

		if ($this->db->trans_status() === FALSE) {
			return $this->_fail('Verifikasi gagal disimpan.');
		}

		$this->db->trans_commit();
		return ['status' => true, 'message' => 'OK'];
	}

	/** Tolak satu batch utuh: setoran REJECTED, semua resi kembali UNPAID dan bebas dipilih lagi. */
	public function reject($id, $user_id, $note)
	{
		$id  = (int) $id;
		$now = date('Y-m-d H:i:s');
		$this->db->trans_begin();

		$st = $this->db->query(
			"SELECT id FROM agent_settlements WHERE id = ? AND status = 'PENDING' FOR UPDATE",
			[$id]
		)->row();
		if (!$st) {
			return $this->_fail('Setoran tidak ditemukan atau sudah diproses.');
		}

		$n = $this->db->where('settlement_id', $id)->count_all_results('agent_settlement_items');

		$this->db->where('settlement_id', $id)
			->where('status_tagihan_smesco', self::TAGIHAN_PENDING)
			->update('shipments', [
				'status_tagihan_smesco' => self::TAGIHAN_UNPAID,
				'settlement_id'         => NULL,
				'updated_by'            => (int) $user_id,
				'updated_at'            => $now,
			]);

		if ($n === 0 || $this->db->affected_rows() !== $n) {
			return $this->_fail('Status resi dalam setoran tidak konsisten. Tidak ada yang diubah.');
		}

		$this->db->where('id', $id)->update('agent_settlements', [
			'status'      => 'REJECTED',
			'reject_note' => $note,
			'verified_by' => (int) $user_id,
			'verified_at' => $now,
		]);

		if ($this->db->trans_status() === FALSE) {
			return $this->_fail('Penolakan gagal disimpan.');
		}

		$this->db->trans_commit();
		return ['status' => true, 'message' => 'OK'];
	}
}
