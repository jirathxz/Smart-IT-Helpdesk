<?php

namespace App\Repositories;

use App\Core\Database;

class StatusLogRepository
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function log(
        int $ticketId,
        int $changedBy,
        string $fromStatus,
        string $toStatus,
        ?string $note = null
    ): int {
        $sql = "INSERT INTO status_logs (ticket_id, changed_by, from_status, to_status, note, created_at)
                VALUES (:ticket_id, :changed_by, :from_status, :to_status, :note, NOW())";

        $this->db->query($sql, [
            'ticket_id'   => $ticketId,
            'changed_by'  => $changedBy,
            'from_status' => $fromStatus,
            'to_status'   => $toStatus,
            'note'        => $note,
        ]);

        return $this->db->lastInsertId();
    }

    public function findByTicketId(int $ticketId): array
    {
        $sql = "SELECT sl.*, u.name AS changer_name, u.role AS changer_role
                FROM status_logs sl
                INNER JOIN users u ON sl.changed_by = u.id
                WHERE sl.ticket_id = :ticket_id
                ORDER BY sl.created_at ASC";

        return $this->db->query($sql, ['ticket_id' => $ticketId])->fetchAll();
    }
}
