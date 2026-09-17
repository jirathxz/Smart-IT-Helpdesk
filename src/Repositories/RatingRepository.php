<?php

namespace App\Repositories;

use App\Core\Database;

class RatingRepository
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function create(int $ticketId, int $score, ?string $feedback = null): int
    {
        $sql = "INSERT INTO ratings (ticket_id, score, feedback, created_at)
                VALUES (:ticket_id, :score, :feedback, NOW())";

        $this->db->query($sql, [
            'ticket_id' => $ticketId,
            'score'     => max(1, min(5, $score)),
            'feedback'  => $feedback,
        ]);

        return $this->db->lastInsertId();
    }

    public function findByTicketId(int $ticketId): ?array
    {
        $sql = "SELECT * FROM ratings WHERE ticket_id = :ticket_id LIMIT 1";
        $stmt = $this->db->query($sql, ['ticket_id' => $ticketId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAverageScore(): float
    {
        $sql = "SELECT AVG(score) AS avg_score FROM ratings";
        $res = $this->db->query($sql)->fetch();
        return $res && $res['avg_score'] ? (float) round($res['avg_score'], 1) : 0.0;
    }
}
