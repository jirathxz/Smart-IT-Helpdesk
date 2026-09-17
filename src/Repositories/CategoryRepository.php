<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * Category Data Access Repository (Developer 2)
 */
class CategoryRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM categories WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name ASC");
    }

    public function withTicketCounts(): array
    {
        $sql = "SELECT c.*, COUNT(t.id) as ticket_count 
                FROM categories c 
                LEFT JOIN tickets t ON c.id = t.category_id 
                GROUP BY c.id 
                ORDER BY ticket_count DESC, c.name ASC";
        return $this->db->fetchAll($sql);
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
        $this->db->query($sql, [
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
        $this->db->query($sql, [
            'id'          => $id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM categories WHERE id = :id", ['id' => $id]);
        return true;
    }
}
