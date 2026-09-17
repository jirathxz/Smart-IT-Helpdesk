<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * Ticket Data Access Repository
 */
class TicketRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT t.*, 
                       u.name as user_name, u.email as user_email, u.line_user_id as user_line_id,
                       c.name as category_name,
                       tech.name as technician_name, tech.email as technician_email, tech.line_user_id as tech_line_id
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                WHERE t.id = :id
                LIMIT 1";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function all(array $filters = []): array
    {
        $sql = "SELECT t.*, 
                       u.name as user_name,
                       c.name as category_name,
                       tech.name as technician_name
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                JOIN categories c ON t.category_id = c.id
                LEFT JOIN users tech ON t.technician_id = tech.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND t.priority = :priority";
            $params['priority'] = $filters['priority'];
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = :cat_id";
            $params['cat_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND t.user_id = :user_id";
            $params['user_id'] = (int) $filters['user_id'];
        }

        if (!empty($filters['technician_id'])) {
            $sql .= " AND t.technician_id = :tech_id";
            $params['tech_id'] = (int) $filters['technician_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE :s1 OR t.description LIKE :s2 OR u.name LIKE :s3)";
            $keyword = '%' . $filters['search'] . '%';
            $params['s1'] = $keyword;
            $params['s2'] = $keyword;
            $params['s3'] = $keyword;
        }

        $sql .= " ORDER BY 
                    CASE t.status 
                        WHEN 'open' THEN 1 
                        WHEN 'assigned' THEN 2 
                        WHEN 'in_progress' THEN 3 
                        WHEN 'resolved' THEN 4 
                        ELSE 5 
                    END,
                    CASE t.priority 
                        WHEN 'urgent' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        ELSE 4 
                    END,
                    t.created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function findByUser(int $userId): array
    {
        return $this->all(['user_id' => $userId]);
    }

    public function findByTechnician(int $techId): array
    {
        return $this->all(['technician_id' => $techId]);
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO tickets (user_id, category_id, technician_id, title, description, status, priority, created_at, updated_at)
                VALUES (:user_id, :category_id, :technician_id, :title, :description, :status, :priority, NOW(), NOW())";

        $this->db->query($sql, [
            'user_id'       => $data['user_id'],
            'category_id'   => $data['category_id'],
            'technician_id' => $data['technician_id'] ?? null,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'status'        => $data['status'] ?? 'open',
            'priority'      => $data['priority'] ?? 'medium',
        ]);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach (['category_id', 'technician_id', 'title', 'description', 'status', 'priority', 'resolved_at', 'closed_at'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "{$col} = :{$col}";
                $params[$col] = $data[$col];
            }
        }

        $fields[] = "updated_at = NOW()";

        $sql = "UPDATE tickets SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->query($sql, $params);
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM tickets WHERE id = :id", ['id' => $id]);
        return true;
    }
}
