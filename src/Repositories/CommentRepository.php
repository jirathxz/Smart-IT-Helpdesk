<?php

namespace App\Repositories;

use App\Core\Database;

class CommentRepository
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function create(int $ticketId, int $userId, string $body, ?string $imagePath = null): int
    {
        $sql = "INSERT INTO comments (ticket_id, user_id, body, image_path, created_at)
                VALUES (:ticket_id, :user_id, :body, :image_path, NOW())";

        $this->db->query($sql, [
            'ticket_id'  => $ticketId,
            'user_id'    => $userId,
            'body'       => $body,
            'image_path' => $imagePath,
        ]);

        return $this->db->lastInsertId();
    }

    public function findByTicketId(int $ticketId): array
    {
        $sql = "SELECT c.*, u.name AS user_name, u.role AS user_role, u.email AS user_email
                FROM comments c
                INNER JOIN users u ON c.user_id = u.id
                WHERE c.ticket_id = :ticket_id
                ORDER BY c.created_at ASC";

        return $this->db->query($sql, ['ticket_id' => $ticketId])->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->query("DELETE FROM comments WHERE id = :id", ['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
