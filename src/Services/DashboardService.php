<?php

namespace App\Services;

use App\Core\Database;

/**
 * Executive Dashboard & Analytics Service (Developer 2)
 */
class DashboardService
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Aggregate all key performance indicators and statistics
     */
    public function getStatistics(): array
    {
        return [
            'status_counts'       => $this->getStatusCounts(),
            'total_tickets'       => $this->getTotalTickets(),
            'avg_resolution_hours'=> $this->getAvgResolutionTime(),
            'avg_rating'          => $this->getAvgRating(),
            'top_technicians'     => $this->getTopTechnicians(),
            'category_breakdown'  => $this->getCategoryBreakdown(),
            'recent_logs'         => $this->getRecentStatusLogs(8),
        ];
    }

    public function getStatusCounts(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM tickets GROUP BY status";
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

    public function getTotalTickets(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as total FROM tickets");
        return (int) ($row['total'] ?? 0);
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
     * Get overall average rating score
     */
    public function getAvgRating(): float
    {
        $sql = "SELECT AVG(score) as avg_score, COUNT(*) as count FROM ratings";
        $row = $this->db->fetch($sql);
        return round((float) ($row['avg_score'] ?? 0), 1);
    }

    /**
     * Get top performing technicians ranked by completed tickets
     */
    public function getTopTechnicians(int $limit = 5): array
    {
        $sql = "SELECT u.id, u.name, u.email,
                       COUNT(t.id) as total_jobs,
                       SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as completed_jobs,
                       ROUND(AVG(r.score), 1) as avg_score
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
    public function getCategoryBreakdown(): array
    {
        $sql = "SELECT c.name, COUNT(t.id) as ticket_count 
                FROM categories c 
                LEFT JOIN tickets t ON c.id = t.category_id 
                GROUP BY c.id 
                ORDER BY ticket_count DESC";
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
}
