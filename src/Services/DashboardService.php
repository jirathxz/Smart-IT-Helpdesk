<?php

namespace App\Services;

use App\Core\Database;

/**
 * Executive Dashboard & Analytics Service (Developer 2)
 * High-density operational metrics for enterprise IT Service Management.
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
            'today' => 'Today',
            'week'  => 'Last 7 days',
            'month' => 'Last 30 days',
            'year'  => 'This year',
            default => 'All time',
        };
    }

    /**
     * Aggregate all operational statistics and KPIs
     */
    public function getStatistics(string $period = 'all'): array
    {
        $where = $this->buildPeriodWhere($period);

        $totalTickets = $this->getTotalTickets($where);
        $openPending = $this->getOpenPendingTickets($where);
        $inProgress = $this->getInProgressTickets($where);
        $resolved = $this->getResolvedTickets($where);
        $urgent = $this->getUrgentTickets($where);
        $avgHours = $this->getAvgResolutionTime();

        $resolutionRate = $totalTickets > 0 ? round(($resolved / $totalTickets) * 100) : 0;
        $avgResolutionFormatted = $avgHours > 0 ? ($avgHours < 1 ? round($avgHours * 60) . 'm' : round($avgHours) . 'h') : '—';

        return [
            'period'               => $period,
            'period_label'         => self::getPeriodLabel($period),
            'kpi' => [
                'total_tickets'    => $totalTickets,
                'open_pending'     => $openPending,
                'open_tickets'     => $openPending,
                'in_progress'      => $inProgress,
                'resolved'         => $resolved,
                'urgent'           => $urgent,
                'csat_rating'      => $this->getAvgRating($where),
                'resolution_rate'  => $resolutionRate,
                'avg_resolution'   => $avgResolutionFormatted,
                'avg_rating'       => $this->getAvgRating($where),
            ],
            'status_counts'        => $this->getStatusCounts($where),
            'total_tickets'        => $totalTickets,
            'avg_resolution_hours' => $avgHours,
            'avg_rating'           => $this->getAvgRating($where),
            'recent_tickets'       => $this->getRecentTickets(6),
            'technician_workload'  => $this->getTechnicianWorkload(),
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

    public function getOpenPendingTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status IN ('open', 'assigned') AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    public function getInProgressTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status = 'in_progress' AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    public function getUrgentTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.priority = 'urgent' AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    public function getResolvedTickets(string $where = '1=1'): int
    {
        $sql = "SELECT COUNT(*) as total FROM tickets t WHERE t.status IN ('resolved', 'closed') AND {$where}";
        $row = $this->db->fetch($sql);
        return (int) ($row['total'] ?? 0);
    }

    public function getAvgRating(string $where = '1=1'): float
    {
        $sql = "SELECT AVG(r.score) as avg_score, COUNT(r.id) as count 
                FROM ratings r 
                JOIN tickets t ON r.ticket_id = t.id 
                WHERE {$where}";
        $row = $this->db->fetch($sql);
        $score = (float) ($row['avg_score'] ?? 0);
        
        if ($score <= 0.0) {
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

    public function getAvgResolutionTime(): float
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours 
                FROM tickets 
                WHERE resolved_at IS NOT NULL";
        $row = $this->db->fetch($sql);
        return round((float) ($row['avg_hours'] ?? 0), 1);
    }

    /**
     * Recent tickets for Main Content operational table
     */
    public function getRecentTickets(int $limit = 6): array
    {
        $sql = "SELECT t.*, u.name as user_name, c.name as category_name, tech.name as tech_name
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                ORDER BY CASE WHEN t.status = 'open' THEN 1 WHEN t.status = 'assigned' THEN 2 ELSE 3 END, t.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Technician workload distribution
     */
    public function getTechnicianWorkload(): array
    {
        $sql = "SELECT u.id, u.name, u.email,
                       COUNT(t.id) as total_jobs,
                       SUM(CASE WHEN t.status IN ('open', 'assigned', 'in_progress') THEN 1 ELSE 0 END) as open_jobs,
                       SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as resolved_jobs,
                       ROUND(AVG(CASE WHEN t.status IN ('resolved', 'closed') AND t.resolved_at IS NOT NULL 
                                      THEN TIMESTAMPDIFF(MINUTE, t.created_at, t.resolved_at) END)) as avg_minutes
                FROM users u
                LEFT JOIN tickets t ON u.id = t.technician_id
                WHERE u.role = 'technician'
                GROUP BY u.id
                ORDER BY open_jobs DESC, total_jobs DESC";
        return $this->db->fetchAll($sql);
    }

    public function getTopTechnicians(int $limit = 5): array
    {
        return $this->getTechnicianWorkload();
    }

    public function getCategoryBreakdown(string $where = '1=1'): array
    {
        $sql = "SELECT c.id, c.name, COUNT(t.id) as ticket_count 
                FROM categories c 
                LEFT JOIN tickets t ON c.id = t.category_id AND {$where}
                GROUP BY c.id 
                ORDER BY ticket_count DESC, c.id ASC";
        return $this->db->fetchAll($sql);
    }

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

    public function getChartData(string $period = 'all'): array
    {
        $where = $this->buildPeriodWhere($period);
        $statusCounts = $this->getStatusCounts($where);

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $label = ($i === 0) ? 'Today' : date('d/m', strtotime("-{$i} days"));
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
                'labels' => ['Open', 'Assigned', 'In Progress', 'Resolved', 'Closed'],
                'data'   => [
                    $statusCounts['open'] ?? 0,
                    $statusCounts['assigned'] ?? 0,
                    $statusCounts['in_progress'] ?? 0,
                    $statusCounts['resolved'] ?? 0,
                    $statusCounts['closed'] ?? 0,
                ],
                'colors' => ['#0284c7', '#9333ea', '#d97706', '#16a34a', '#64748b'],
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
