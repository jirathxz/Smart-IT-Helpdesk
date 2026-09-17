<?php

namespace App\Services;

use App\Core\Database;

/**
 * Executive Dashboard & Analytics Service (Developer 2)
 * Calculates real-time KPI metrics, period filtering, and Chart.js datasets.
 */
class DashboardService
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Build SQL WHERE condition based on selected time period
     */
    public function buildPeriodWhere(string $period, string $tableAlias = 't'): string
    {
        $prefix = $tableAlias ? "{$tableAlias}." : "";
        return match ($period) {
            'today' => "DATE({$prefix}created_at) = CURDATE()",
            'week'  => "{$prefix}created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
            'month' => "{$prefix}created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            'year'  => "{$prefix}created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)",
            default => "1=1",
        };
    }

    /**
     * Human readable period label
     */
    public static function getPeriodLabel(string $period): string
    {
        return match ($period) {
            'today' => 'วันนี้ (Today)',
            'week'  => '7 วันล่าสุด (Last 7 Days)',
            'month' => 'เดือนนี้ (This Month)',
            'year'  => 'ปีนี้ (This Year)',
            default => 'ทั้งหมด (All Time)',
        };
    }

    /**
     * Aggregate all key performance indicators and statistics
     */
    public function getStatistics(string $period = 'all'): array
    {
        $where = $this->buildPeriodWhere($period);

        return [
            'period'               => $period,
            'period_label'         => self::getPeriodLabel($period),
            'kpi' => [
                'total_tickets'    => $this->getTotalTickets($where),
                'open_pending'     => $this->getOpenPendingTickets($where),
                'in_progress'      => $this->getInProgressTickets($where),
                'urgent'           => $this->getUrgentTickets($where),
                'resolved'         => $this->getResolvedTickets($where),
                'csat_rating'      => $this->getAvgRating($where),
            ],
            'status_counts'        => $this->getStatusCounts($where),
            'total_tickets'        => $this->getTotalTickets($where),
            'avg_resolution_hours' => $this->getAvgResolutionTime(),
            'avg_rating'           => $this->getAvgRating($where),
            'top_technicians'      => $this->getTopTechnicians(),
            'category_breakdown'   => $this->getCategoryBreakdown($where),
            'recent_logs'          => $this->getRecentStatusLogs(8),
            'chart_data'           => $this->getChartData($period),
        ];
    }

    public function getTotalTickets(string $where = '1=1'): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tickets t WHERE {$where}");
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Count Open or Assigned tickets (งานที่ยังไม่ได้แก้)
     */
    public function getOpenPendingTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status IN ('open', 'assigned') AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Count In Progress tickets (งานที่ช่างกำลังดำเนินการ)
     */
    public function getInProgressTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status = 'in_progress' AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Count Urgent priority tickets (Ticket ระดับ Urgent)
     */
    public function getUrgentTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.priority = 'urgent' AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Count Resolved or Closed tickets (งานที่แก้ไขแล้ว)
     */
    public function getResolvedTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status IN ('resolved', 'closed') AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Calculate average CSAT rating score
     */
    public function getAvgRating(string $where = '1=1'): float
    {
        $sql = "SELECT AVG(r.score) as avg_score, COUNT(r.id) as count 
                FROM ratings r 
                JOIN tickets t ON r.ticket_id = t.id 
                WHERE {$where}";
        $row = $this->db->fetch($sql);
        $score = (float) ($row['avg_score'] ?? 0);
        
        if ($score <= 0.0) {
            // Fallback to overall system average score
            $overall = $this->db->fetch("SELECT AVG(score) as avg_score FROM ratings");
            $score = (float) ($overall['avg_score'] ?? 5.0);
        }

        return round($score, 1);
    }

    public function getStatusCounts(string $where = '1=1'): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM tickets t WHERE {$where} GROUP BY status";
        $rows = $this->db->fetchAll($sql);

        $counts = [
            'open'        => 0,
            'assigned'    => 0,
            'in_progress' => 0,
            'resolved'    => 0,
            'closed'      => 0,
        ];

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }

        return $counts;
    }

    /**
     * Calculate average resolution time in hours for resolved/closed tickets
     */
    public function getAvgResolutionTime(): float
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours 
                FROM tickets 
                WHERE resolved_at IS NOT NULL";
        $row = $this->db->fetch($sql);
        return round((float) ($row['avg_hours'] ?? 0), 1);
    }

    /**
     * Get top performing technicians ranked by completed tickets
     */
    public function getTopTechnicians(int $limit = 5): array
    {
        $sql = "SELECT u.id, u.name, u.email,
                       COUNT(t.id) as total_jobs,
                       SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as completed_jobs,
                       ROUND(COALESCE(AVG(r.score), 5.0), 1) as avg_score
                FROM users u
                LEFT JOIN tickets t ON u.id = t.technician_id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE u.role = 'technician'
                GROUP BY u.id
                ORDER BY completed_jobs DESC, total_jobs DESC
                LIMIT :limit";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get ticket distribution across categories
     */
    public function getCategoryBreakdown(string $where = '1=1'): array
    {
        $sql = "SELECT c.id, c.name, COUNT(t.id) as ticket_count 
                FROM categories c 
                LEFT JOIN tickets t ON c.id = t.category_id AND {$where}
                GROUP BY c.id 
                ORDER BY ticket_count DESC, c.id ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get recent status transition logs
     */
    public function getRecentStatusLogs(int $limit = 10): array
    {
        $sql = "SELECT l.*, u.name as changed_by_name, u.role as changed_by_role, t.title as ticket_title 
                FROM status_logs l
                JOIN users u ON l.changed_by = u.id
                JOIN tickets t ON l.ticket_id = t.id
                ORDER BY l.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Prepare rich chart datasets for Chart.js
     */
    public function getChartData(string $period = 'all'): array
    {
        $where = $this->buildPeriodWhere($period);
        $statusCounts = $this->getStatusCounts($where);

        // 7 Days Trend
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $label = ($i === 0) ? 'วันนี้' : date('d/m', strtotime("-{$i} days"));
            $days[$d] = [
                'label'    => $label,
                'created'  => 0,
                'resolved' => 0,
            ];
        }

        $createdSql = "SELECT DATE(created_at) as d, COUNT(*) as c 
                       FROM tickets 
                       WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                       GROUP BY DATE(created_at)";
        foreach ($this->db->fetchAll($createdSql) as $row) {
            if (isset($days[$row['d']])) {
                $days[$row['d']]['created'] = (int) $row['c'];
            }
        }

        $resolvedSql = "SELECT DATE(resolved_at) as d, COUNT(*) as c 
                        FROM tickets 
                        WHERE resolved_at IS NOT NULL AND resolved_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                        GROUP BY DATE(resolved_at)";
        foreach ($this->db->fetchAll($resolvedSql) as $row) {
            if (isset($days[$row['d']])) {
                $days[$row['d']]['resolved'] = (int) $row['c'];
            }
        }

        $cats = $this->getCategoryBreakdown($where);

        return [
            'status' => [
                'labels' => ['เปิดใหม่ (Open)', 'จ่ายงานแล้ว (Assigned)', 'กำลังซ่อม (In Progress)', 'ซ่อมเสร็จ (Resolved)', 'ปิดงานแล้ว (Closed)'],
                'data'   => [
                    $statusCounts['open'] ?? 0,
                    $statusCounts['assigned'] ?? 0,
                    $statusCounts['in_progress'] ?? 0,
                    $statusCounts['resolved'] ?? 0,
                    $statusCounts['closed'] ?? 0,
                ],
                'colors' => ['#38bdf8', '#a855f7', '#f59e0b', '#10b981', '#64748b'],
            ],
            'trend' => [
                'labels'   => array_column($days, 'label'),
                'created'  => array_column($days, 'created'),
                'resolved' => array_column($days, 'resolved'),
            ],
            'categories' => [
                'labels' => array_column($cats, 'name'),
                'data'   => array_map(fn($c) => (int)$c['ticket_count'], $cats),
            ]
        ];
    }
}
