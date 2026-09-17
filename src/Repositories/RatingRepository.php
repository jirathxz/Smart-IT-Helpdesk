<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * Rating & Feedback Repository
 */
class RatingRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM ratings WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function findByTicket(int $ticketId): ?array
    {
        return $this->db->fetch("SELECT * FROM ratings WHERE ticket_id = :ticket_id LIMIT 1", ['ticket_id' => $ticketId]);
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM ratings ORDER BY created_at DESC");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO ratings (ticket_id, score, feedback, created_at)
                VALUES (:ticket_id, :score, :feedback, NOW())";

        $this->db->query($sql, [
            'ticket_id' => $data['ticket_id'],
            'score'     => max(1, min(5, (int) $data['score'])),
            'feedback'  => $data['feedback'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE ratings SET score = :score, feedback = :feedback WHERE id = :id";
        $this->db->query($sql, [
            'id'       => $id,
            'score'    => max(1, min(5, (int) $data['score'])),
            'feedback' => $data['feedback'] ?? null,
        ]);
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM ratings WHERE id = :id", ['id' => $id]);
        return true;
    }
}
