<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (php_sapi_name() !== 'cli') exit('No direct script access allowed');
		$this->load->database();
		$this->load->model('Shipment_model'); // sesuaikan nama model yang punya sync_vendor_tracking
	}

	public function sync_vendor_tracking($limit = 50)
	{
		$lock = fopen(APPPATH . 'cache/cron_sync_tracking.lock', 'c');
		if (!flock($lock, LOCK_EX | LOCK_NB)) {
			echo date('c') . " skip: run sebelumnya masih jalan\n";
			return;
		}

		$rows = $this->db->select('id')
			->from('shipments')
			->where_in('vendor', ['TLX', 'CHOIR EXPRESS'])
			->where('vendor_connote IS NOT NULL', null, false)
			->where('vendor_connote !=', '')
			->where('(status IS NULL OR status != "DELIVERED")', null, false)
			->order_by('tracking_synced_at', 'ASC') // NULL duluan di MySQL
			->limit((int) $limit)
			->get()->result_array();

		$ok = $fail = 0;
		foreach ($rows as $r) {
			try {
				$this->Shipment_model->sync_vendor_tracking($r['id']);
				$ok++;
			} catch (\Throwable $e) {
				$fail++;
				log_message('error', "cron sync tracking #{$r['id']}: " . $e->getMessage());
			}
			// tandai sudah dicek, termasuk kalau vendor return kosong/gagal
			$this->db->where('id', $r['id'])
				->update('shipments', ['tracking_synced_at' => date('Y-m-d H:i:s')]);
		}

		echo date('c') . " done ok=$ok fail=$fail total=" . count($rows) . "\n";
		flock($lock, LOCK_UN);
	}

	public function auto_expire()
	{
		$now = date('Y-m-d H:i:s');

		$this->db->where('status', 'waiting_payment')
			->where('expired_at <', $now)
			->update('shipments', ['status' => 'expired']);

		echo "Expired check done at " . $now . "\n";
	}
}
