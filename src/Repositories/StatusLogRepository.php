<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * Status Log Audit Trail Repository
 */
class StatusLogRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM status_logs WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function findByTicket(int $ticketId): array
    {
        $sql = "SELECT l.*, u.name as changed_by_name, u.role as changed_by_role 
                FROM status_logs l
                JOIN users u ON l.changed_by = u.id
                WHERE l.ticket_id = :ticket_id
                ORDER BY l.created_at ASC";

        return $this->db->fetchAll($sql, ['ticket_id' => $ticketId]);
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM status_logs ORDER BY created_at DESC");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO status_logs (ticket_id, changed_by, from_status, to_status, note, created_at)
                VALUES (:ticket_id, :changed_by, :from_status, :to_status, :note, NOW())";

        $this->db->query($sql, [
            'ticket_id'   => $data['ticket_id'],
            'changed_by'  => $data['changed_by'],
            'from_status' => $data['from_status'],
            'to_status'   => $data['to_status'],
            'note'        => $data['note'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return false; // Audit logs should not be modified
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM status_logs WHERE id = :id", ['id' => $id]);
        return true;
    }
}
