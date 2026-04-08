<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_VisitorLog extends CI_Model
{
	public function get_dashboard_stats()
	{
		$today = date('Y-m-d');
		$row   = $this->db->query("
            SELECT
                COUNT(*)                                          AS total_today,
                SUM(is_bot)                                       AS bot_today,
                SUM(is_suspicious)                               AS suspicious_today,
                COUNT(DISTINCT ip_address)                       AS unique_ip_today,
                SUM(CASE WHEN is_bot = 0 AND is_suspicious = 0
                    THEN 1 ELSE 0 END)                           AS human_today
            FROM visitor_logs
            WHERE DATE(created_at) = '{$today}'
        ")->row();

		return $row;
	}

	// Tren 7 hari: human vs bot
	public function get_trend_7days()
	{
		return $this->db->query("
            SELECT
                DATE(created_at)          AS date,
                SUM(CASE WHEN is_bot = 0 AND is_suspicious = 0 THEN 1 ELSE 0 END) AS human,
                SUM(is_bot)               AS bot,
                SUM(is_suspicious)        AS suspicious
            FROM visitor_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ")->result();
	}

	// Top 10 IP mencurigakan hari ini
	public function get_suspicious_ips($limit = 10)
	{
		return $this->db->query("
            SELECT
                ip_address,
                COUNT(*) AS hit_count,
                MAX(created_at) AS last_seen,
                MAX(user_agent) AS user_agent
            FROM visitor_logs
            WHERE is_suspicious = 1
              AND DATE(created_at) = CURDATE()
            GROUP BY ip_address
            ORDER BY hit_count DESC
            LIMIT {$limit}
        ")->result();
	}

	// Recent suspicious requests
	public function get_recent_suspicious($limit = 15)
	{
		return $this->db->query("
            SELECT *
            FROM visitor_logs
            WHERE is_suspicious = 1
            ORDER BY created_at DESC
            LIMIT {$limit}
        ")->result();
	}
}
