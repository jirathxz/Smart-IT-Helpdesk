<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class TicketRepository implements RepositoryInterface
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT t.*, 
                       u.name AS user_name, u.email AS user_email,
                       tech.name AS technician_name, tech.email AS technician_email,
                       c.name AS category_name,
                       r.score AS rating_score, r.feedback AS rating_feedback, r.created_at AS rated_at
                FROM tickets t
                INNER JOIN users u ON t.user_id = u.id
                INNER JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE t.id = :id
                LIMIT 1";

        $stmt = $this->db->query($sql, ['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function all(): array
    {
        $sql = "SELECT t.*, 
                       u.name AS user_name, 
                       tech.name AS technician_name, 
                       c.name AS category_name,
                       r.score AS rating_score
                FROM tickets t
                INNER JOIN users u ON t.user_id = u.id
                INNER JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                ORDER BY t.created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO tickets (user_id, category_id, technician_id, title, description, status, priority, created_at, updated_at)
                VALUES (:user_id, :category_id, :technician_id, :title, :description, :status, :priority, NOW(), NOW())";

        $params = [
            'user_id'       => $data['user_id'],
            'category_id'   => $data['category_id'],
            'technician_id' => $data['technician_id'] ?? null,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'status'        => $data['status'] ?? 'open',
            'priority'      => $data['priority'] ?? 'medium',
        ];

        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $val) {
            $fields[] = "`{$key}` = :{$key}";
            $params[$key] = $val;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE tickets SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->query("DELETE FROM tickets WHERE id = :id", ['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function findByUser(int $userId, array $filters = []): array
    {
        $sql = "SELECT t.*, 
                       u.name AS user_name, 
                       tech.name AS technician_name, 
                       c.name AS category_name,
                       r.score AS rating_score
                FROM tickets t
                INNER JOIN users u ON t.user_id = u.id
                INNER JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE t.user_id = :user_id";

        $params = ['user_id' => $userId];

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (t.title LIKE :kw_title OR t.description LIKE :kw_desc)";
            $params['kw_title'] = '%' . $filters['keyword'] . '%';
            $params['kw_desc'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= " ORDER BY t.created_at DESC";

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function findByTechnician(int $techId, array $filters = []): array
    {
        $sql = "SELECT t.*, 
                       u.name AS user_name, 
                       tech.name AS technician_name, 
                       c.name AS category_name,
                       r.score AS rating_score
                FROM tickets t
                INNER JOIN users u ON t.user_id = u.id
                INNER JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE (t.technician_id = :tech_id OR (t.status = 'open' AND t.technician_id IS NULL))";

        $params = ['tech_id' => $techId];

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (t.title LIKE :kw_title OR t.description LIKE :kw_desc)";
            $params['kw_title'] = '%' . $filters['keyword'] . '%';
            $params['kw_desc'] = '%' . $filters['keyword'] . '%';
        }

        $sql .= " ORDER BY CASE t.priority 
                            WHEN 'urgent' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            WHEN 'low' THEN 4 
                           END, t.created_at DESC";

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function search(string $keyword = '', array $filters = []): array
    {
        $sql = "SELECT t.*, 
                       u.name AS user_name, 
                       tech.name AS technician_name, 
                       c.name AS category_name,
                       r.score AS rating_score
                FROM tickets t
                INNER JOIN users u ON t.user_id = u.id
                INNER JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                LEFT JOIN ratings r ON t.id = r.ticket_id
                WHERE 1=1";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (t.title LIKE :kw_title OR t.description LIKE :kw_desc OR u.name LIKE :kw_user)";
            $params['kw_title'] = '%' . $keyword . '%';
            $params['kw_desc'] = '%' . $keyword . '%';
            $params['kw_user'] = '%' . $keyword . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        $sql .= " ORDER BY t.created_at DESC";

        return $this->db->query($sql, $params)->fetchAll();
    }

    public function updateStatus(int $id, string $status, ?string $resolvedAt = null, ?string $closedAt = null): bool
    {
        $sql = "UPDATE tickets SET status = :status, updated_at = NOW()";
        $params = ['id' => $id, 'status' => $status];

        if ($resolvedAt !== null) {
            $sql .= ", resolved_at = :resolved_at";
            $params['resolved_at'] = $resolvedAt;
        }

        if ($closedAt !== null) {
            $sql .= ", closed_at = :closed_at";
            $params['closed_at'] = $closedAt;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    public function assignTechnician(int $id, int $technicianId): bool
    {
        $sql = "UPDATE tickets SET technician_id = :tech_id, status = 'assigned', updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->query($sql, ['id' => $id, 'tech_id' => $technicianId]);
        return $stmt->rowCount() > 0;
    }

    public function getCounts(?int $userId = null, ?int $techId = null): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) AS count_open,
                    SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) AS count_assigned,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS count_in_progress,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS count_resolved,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) AS count_closed
                FROM tickets WHERE 1=1";

        $params = [];
        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        } elseif ($techId !== null) {
            $sql .= " AND (technician_id = :tech_id OR status = 'open')";
            $params['tech_id'] = $techId;
        }

        $res = $this->db->query($sql, $params)->fetch();
        return $res ?: [
            'total' => 0, 'count_open' => 0, 'count_assigned' => 0,
            'count_in_progress' => 0, 'count_resolved' => 0, 'count_closed' => 0
        ];
    }
}
