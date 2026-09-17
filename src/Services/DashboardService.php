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

        $intakeClearance = $this->getIntakeClearanceRatio($where);
        $technicianWorkload = $this->getTechnicianWorkload($period);

        $stats = [
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
            'intake_clearance'     => $intakeClearance,
            'status_counts'        => $this->getStatusCounts($where),
            'total_tickets'        => $totalTickets,
            'avg_resolution_hours' => $avgHours,
            'avg_rating'           => $this->getAvgRating($where),
            'recent_tickets'       => $this->getRecentTickets(6),
            'technician_workload'  => $technicianWorkload,
            'top_technicians'      => $technicianWorkload,
            'category_breakdown'   => $this->getCategoryBreakdown($where),
            'recent_logs'          => $this->getRecentStatusLogs(8),
            'chart_data'           => $this->getChartData($period),
        ];

        $stats['executive_summary'] = $this->getExecutiveSummary($stats);
        return $stats;
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
     * Ticket Intake vs Clearance Ratio (Burn-down Analysis)
     */
    public function getIntakeClearanceRatio(string $where = '1=1'): array
    {
        $totalNew = $this->getTotalTickets($where);
        $resolved = $this->getResolvedTickets($where);

        $rate = $totalNew > 0 ? round(($resolved / $totalNew) * 100) : ($resolved > 0 ? 100 : 0);
        $netDelta = $totalNew - $resolved;

        if ($rate >= 100) {
            $status = 'healthy';
            $label = 'ลดงานค้างสำเร็จ (Backlog shrinking)';
            $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $dot = 'bg-emerald-500';
            $icon = 'fa-arrow-trend-down';
        } elseif ($rate >= 80) {
            $status = 'stable';
            $label = 'ปริมาณงานสมดุล (Stable intake)';
            $badge = 'bg-sky-50 text-sky-700 border-sky-200';
            $dot = 'bg-sky-500';
            $icon = 'fa-arrows-left-right';
        } else {
            $status = 'warning';
            $label = 'งานคั่งค้างสะสม (Backlog accumulating)';
            $badge = 'bg-amber-50 text-amber-700 border-amber-200';
            $dot = 'bg-amber-500';
            $icon = 'fa-arrow-trend-up';
        }

        return [
            'intake'        => $totalNew,
            'clearance'     => $resolved,
            'rate'          => $rate,
            'net_delta'     => $netDelta,
            'status'        => $status,
            'status_label'  => $label,
            'badge'         => $badge,
            'dot'           => $dot,
            'icon'          => $icon,
        ];
    }

    /**
     * Generate automated, friendly Executive Health Summary message for Admin
     */
    public function getExecutiveSummary(array $stats): array
    {
        $kpi = $stats['kpi'] ?? [];
        $intake = $stats['intake_clearance'] ?? [];
        $workload = $stats['technician_workload'] ?? [];
        $urgent = (int) ($kpi['urgent'] ?? 0);
        $totalOpen = (int) ($kpi['open_pending'] ?? 0);

        $overloadedTechs = array_filter($workload, fn($t) => $t['capacity_status'] === 'overloaded');
        $availableTechs = array_filter($workload, fn($t) => $t['capacity_status'] === 'available');

        $headlines = [];
        if ($urgent > 0) {
            $headlines[] = "พบตั๋วเร่งด่วน {$urgent} รายการที่ต้องเร่งดำเนินการ";
            $healthLevel = 'warning';
        } elseif (!empty($overloadedTechs)) {
            $headlines[] = "มีช่างเทคนิค " . count($overloadedTechs) . " ท่านที่มีภาระงานค่อนข้างสูง";
            $healthLevel = 'attention';
        } else {
            $healthLevel = 'optimal';
            $headlines[] = "ระบบงานอยู่ในเกณฑ์ปกติ ทีมช่างพร้อมรองรับงานใหม่";
        }

        $detail = "อัตราการเคลียร์ตั๋วเทียบตั๋วใหม่ {$intake['rate']}% • ตั๋วค้างในคิวทั้งหมด {$totalOpen} รายการ • ช่างพร้อมรับงาน " . count($availableTechs) . " ท่าน";

        return [
            'level'     => $healthLevel,
            'headline'  => implode(' • ', $headlines),
            'detail'    => $detail,
            'badge'     => match($healthLevel) {
                'optimal'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'attention' => 'bg-amber-50 text-amber-700 border-amber-200',
                default     => 'bg-rose-50 text-rose-700 border-rose-200',
            },
            'dot'       => match($healthLevel) {
                'optimal'   => 'bg-emerald-500',
                'attention' => 'bg-amber-500',
                default     => 'bg-rose-500',
            }
        ];
    }

    /**
     * Technician workload distribution with qualitative metrics (CSAT, Capacity Status, Resolution Rate)
     */
    public function getTechnicianWorkload(string $period = 'all'): array
    {
        $where = $this->buildPeriodWhere($period, 't');

        $sql = "SELECT u.id, u.name, u.email,
                       COUNT(t.id) as total_jobs,
                       SUM(CASE WHEN t.status IN ('open', 'assigned', 'in_progress') THEN 1 ELSE 0 END) as open_jobs,
                       SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as resolved_jobs,
                       SUM(CASE WHEN t.priority = 'urgent' AND t.status IN ('open', 'assigned', 'in_progress') THEN 1 ELSE 0 END) as urgent_open_jobs,
                       ROUND(AVG(CASE WHEN t.status IN ('resolved', 'closed') AND t.resolved_at IS NOT NULL 
                                      THEN TIMESTAMPDIFF(MINUTE, t.created_at, t.resolved_at) END)) as avg_minutes,
                       ROUND(AVG(r.score), 1) as avg_csat,
                       COUNT(r.id) as rating_count
                FROM users u
                LEFT JOIN tickets t ON u.id = t.technician_id AND {$where}
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE u.role = 'technician'
                GROUP BY u.id
                ORDER BY open_jobs DESC, total_jobs DESC";
        $rows = $this->db->fetchAll($sql);

        return array_map(function ($tech) {
            $total = (int) $tech['total_jobs'];
            $open = (int) $tech['open_jobs'];
            $resolved = (int) $tech['resolved_jobs'];
            $urgent = (int) $tech['urgent_open_jobs'];
            $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
            $csat = !empty($tech['avg_csat']) ? (float) $tech['avg_csat'] : 0.0;

            // Capacity Status for Admin
            if ($open >= 8 || $urgent > 0) {
                $statusKey = 'overloaded';
                $statusLabel = 'งานล้นมือ / มีงานด่วน';
                $statusBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                $statusDot = 'bg-rose-500';
            } elseif ($open >= 4) {
                $statusKey = 'heavy';
                $statusLabel = 'งานหนาแน่น';
                $statusBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                $statusDot = 'bg-amber-500';
            } elseif ($open >= 2) {
                $statusKey = 'balanced';
                $statusLabel = 'กำลังดี (Balanced)';
                $statusBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                $statusDot = 'bg-blue-500';
            } else {
                $statusKey = 'available';
                $statusLabel = 'พร้อมรับงาน (Available)';
                $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                $statusDot = 'bg-emerald-500';
            }

            return array_merge($tech, [
                'total_jobs'        => $total,
                'open_jobs'         => $open,
                'resolved_jobs'     => $resolved,
                'urgent_open_jobs'  => $urgent,
                'resolution_rate'   => $rate,
                'avg_csat'          => $csat,
                'rating_count'      => (int) $tech['rating_count'],
                'capacity_status'   => $statusKey,
                'status_label'      => $statusLabel,
                'status_badge'      => $statusBadge,
                'status_dot'        => $statusDot,
            ]);
        }, $rows);
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

        $techWorkload = $this->getTechnicianWorkload($period);
        $techNames = array_column($techWorkload, 'name');
        $techAssigned = array_map(fn($t) => (int)$t['total_jobs'], $techWorkload);
        $techResolved = array_map(fn($t) => (int)$t['resolved_jobs'], $techWorkload);
        $techActive = array_map(fn($t) => (int)$t['open_jobs'], $techWorkload);

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
            'technicians' => [
                'labels'   => $techNames,
                'assigned' => $techAssigned,
                'resolved' => $techResolved,
                'active'   => $techActive,
            ],
            'categories' => [
                'labels' => array_column($cats, 'name'),
                'data'   => array_map(fn($c) => (int)$c['ticket_count'], $cats),
            ]
        ];
    }
}
