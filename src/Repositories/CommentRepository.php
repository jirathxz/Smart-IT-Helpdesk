<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * Comment Data Access Repository
 */
class CommentRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM comments WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function findByTicket(int $ticketId): array
    {
        $sql = "SELECT c.*, u.name as user_name, u.role as user_role 
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.ticket_id = :ticket_id
                ORDER BY c.created_at ASC";

        return $this->db->fetchAll($sql, ['ticket_id' => $ticketId]);
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM comments ORDER BY created_at DESC");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO comments (ticket_id, user_id, body, image_path, created_at)
                VALUES (:ticket_id, :user_id, :body, :image_path, NOW())";

        $this->db->query($sql, [
            'ticket_id'  => $data['ticket_id'],
            'user_id'    => $data['user_id'],
            'body'       => $data['body'],
            'image_path' => $data['image_path'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE comments SET body = :body WHERE id = :id";
        $this->db->query($sql, ['id' => $id, 'body' => $data['body']]);
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM comments WHERE id = :id", ['id' => $id]);
        return true;
    }
}
