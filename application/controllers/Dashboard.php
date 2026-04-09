<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Authenticated_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_User');
		$this->load->model('M_Agent');
		$this->load->model('M_Visitorlog');
	}

	public function index()
	{
		$sess      = $this->session->userdata('user');
		$role_slug = $sess['role_slug'];

		// Tentukan view & data berdasarkan role
		switch ($role_slug) {
			case 'superadmin':
				$this->_superadmin($sess);
				break;
			case 'admin-kribo':
				$this->_admin_kribo($sess);
				break;
			case 'finance-kribo':
				$this->_finance_kribo($sess);
				break;
			case 'admin-mitra':
			case 'staff-mitra':
			case 'finance-mitra':
				$this->_mitra($sess);
				break;
			case 'tracker-mitra':
				$this->_tracker($sess);
				break;
			case 'checker': // Tambahkan Role Checker
				$this->_checker($sess);
				break;
			case 'driver': // Tambahkan Role Driver
				$this->_driver($sess);
				break;
			default:
				show_error('Role tidak dikenali.', 403);
		}
	}

	// ----------------------------------------------------------------

	private function _superadmin($sess)
	{
		$this->load->model(['M_Api', 'M_Shipment']);

		// Statistik Users & Agents
		$total_users        = $this->db->where('deleted_at IS NULL')->count_all_results('users');
		$total_agents       = $this->M_Agent->count_active();
		$total_global_users = $this->db
			->select('users.id')
			->join('roles', 'roles.id = users.role_id')
			->where('users.deleted_at IS NULL')
			->where('roles.scope', 'global')
			->count_all_results('users');
		$total_agent_users  = $total_users - $total_global_users;

		// Statistik API
		$total_api_clients = $this->db->count_all('api_clients');
		$api_hits_today    = $this->db->where('DATE(created_at)', date('Y-m-d'))->count_all_results('api_logs');
		$api_trend         = $this->db->select("DATE(created_at) as date, COUNT(id) as total")
			->from('api_logs')
			->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
			->group_by('DATE(created_at)')
			->order_by('date', 'ASC')
			->get()->result();

		// ✅ Statistik Shipment (baru)
		$shipment_stats = $this->M_Shipment->get_superadmin_stats();

		// Recent data
		$recent_agents = $this->db
			->select('a.*, r.nama_kota')
			->from('agents a')
			->join('mt_kota r', 'r.id = a.regency_id', 'left')
			->where('a.deleted_at IS NULL')
			->order_by('a.created_at', 'DESC')
			->limit(5)
			->get()->result();

		$recent_users = $this->M_User->get_paged_with_role(5, 0); // ✅ konsisten jadi 5

		$recent_logs = $this->db
			->select('l.*, u.name AS user_name')
			->from('user_activity_logs l')
			->join('users u', 'u.id = l.user_id', 'left')
			->order_by('l.created_at', 'DESC')
			->limit(10)
			->get()->result();

		// ✅ Visitor stats
		$visitor_stats       = $this->M_VisitorLog->get_dashboard_stats();
		$visitor_trend       = $this->M_VisitorLog->get_trend_7days();
		$suspicious_ips      = $this->M_VisitorLog->get_suspicious_ips();
		$recent_suspicious   = $this->M_VisitorLog->get_recent_suspicious();

		$data = [
			'title'              => 'Dashboard Superadmin',
			'total_users'        => $total_users,
			'total_agents'       => $total_agents,
			'total_api_clients'  => $total_api_clients,
			'api_hits_today'     => $api_hits_today,
			'api_trend_json'     => json_encode($api_trend),
			'total_global_users' => $total_global_users,
			'total_agent_users'  => $total_agent_users,
			'shipment_stats'     => $shipment_stats, // ✅
			'recent_agents'      => $recent_agents,
			'recent_users'       => $recent_users,
			'recent_logs'        => $recent_logs,
			'visitor_stats'     => $visitor_stats,
			'visitor_trend_json' => json_encode($visitor_trend),
			'suspicious_ips'    => $suspicious_ips,
			'recent_suspicious' => $recent_suspicious,
		];

		$this->render('app/pages/dashboard/v_superadmin', $data);
	}

	private function _admin_kribo($sess)
	{
		$this->load->model('M_Shipment');

		$total_agents   = $this->db->where('deleted_at IS NULL')->count_all_results('agents');
		$total_users    = $this->db->where('deleted_at IS NULL')->count_all_results('users');
		$agents_active  = $this->M_Agent->count_active();
		$users_inactive = $this->db->where('deleted_at IS NULL')->where('is_active', 0)->count_all_results('users');

		// ✅ Tambahan
		$shipment_stats   = $this->M_Shipment->get_kribo_stats();
		$recent_shipments = $this->M_Shipment->get_recent_shipments(8);
		$agents_list      = $this->M_Agent->get_all_with_stats();
		$recent_users     = $this->M_User->get_paged_with_role(5, 0);

		$data = [
			'title'            => 'Dashboard Admin Kribo',
			'total_agents'     => $total_agents,
			'total_users'      => $total_users,
			'agents_active'    => $agents_active,
			'users_inactive'   => $users_inactive,
			'shipment_stats'   => $shipment_stats,
			'recent_shipments' => $recent_shipments,
			'agents_list'      => $agents_list,
			'recent_users'     => $recent_users,
		];

		$this->render('app/pages/dashboard/v_admin_kribo', $data);
	}

	private function _finance_kribo($sess)
	{
		$total_agents = $this->db->where('deleted_at IS NULL')->count_all_results('agents');
		$total_users  = $this->db
			->select('users.id')
			->join('roles', 'roles.id = users.role_id')
			->where('users.deleted_at IS NULL')
			->where('roles.scope', 'agent')
			->count_all_results('users');

		$agents_list = $this->M_Agent->get_all_with_stats();

		$data = compact('total_agents', 'total_users', 'agents_list');

		$this->render('dashboard/v_finance_kribo', 'Dashboard Finance Kribo', $data);
	}

	private function _mitra($sess)
	{
		$this->load->model('M_Shipment');

		$agent_id   = $sess['agent_id'];
		$agent_info = $this->M_Agent->get_detail($agent_id);

		// ✅ Ambil nama kota hub kargo agen
		$city = $this->db
			->select('c.name')
			->from('agents a')
			->join('cities c', 'c.id = a.city_id', 'left')
			->where('a.id', $agent_id)
			->get()->row();

		$city_name = $city->name ?? NULL;

		$total_users = $this->db
			->where('agent_id', $agent_id)
			->where('deleted_at IS NULL')
			->count_all_results('users');

		$users_active = $this->db
			->where('agent_id', $agent_id)
			->where('deleted_at IS NULL')
			->where('is_active', 1)
			->count_all_results('users');

		$agent_users    = $this->M_User->get_all_with_role($agent_id);
		$shipment_stats = $this->M_Shipment->get_agent_stats($agent_id, $city_name); // ✅ pass city_name
		$current_user   = $this->current_user;

		$data = [
			'title'          => 'Dashboard — ' . ($agent_info->name ?? 'Mitra'),
			'agent_info'     => $agent_info,
			'total_users'    => $total_users,
			'users_active'   => $users_active,
			'agent_users'    => $agent_users,
			'shipment_stats' => $shipment_stats,
			'current_user'   => $current_user,
			'city_name'      => $city_name, // ✅ untuk kondisi tampil/hide tombol inbound
		];

		$this->render('app/pages/dashboard/v_mitra', $data);
	}

	private function _tracker($sess)
	{
		$agent_id   = $sess['agent_id'];
		$agent_info = $this->M_Agent->get_by_id($agent_id);

		$data = compact('agent_info');

		$this->render('dashboard/v_tracker', 'Dashboard Tracker', $data);
	}

	// --- Dashboard khusus Driver (Mobile-First) ---
	private function _driver($sess)
	{
		$title = 'Dashboard Driver';
		// Statistik jemputan hari ini
		$stats = $this->db->select("
			COUNT(*) as total_tugas,
			SUM(CASE WHEN status = 'READY_TO_PICKUP' THEN 1 ELSE 0 END) as belum_pickup,
			SUM(CASE WHEN status = 'PICKED_UP' THEN 1 ELSE 0 END) as sudah_pickup
		")->get('shipments')->row();

		$this->render('app/pages/dashboard/v_driver', compact('title', 'stats'));
	}

	// --- Dashboard khusus Checker (Warehouse-First) ---
	private function _checker($sess)
	{
		$title = 'Dashboard Checker';
		// Statistik barang yang akan datang (dari driver) vs yang sudah diterima gudang
		$stats = $this->db->select("
			COUNT(*) as total_expected,
			SUM(CASE WHEN status = 'PICKED_UP' THEN 1 ELSE 0 END) as in_transit,
			SUM(CASE WHEN status = 'RECEIVED_AT_SMESCO_WAREHOUSE' THEN 1 ELSE 0 END) as accepted
		")->get('shipments')->row();

		$this->render('app/pages/dashboard/v_checker', compact('title', 'stats'));
	}
}
