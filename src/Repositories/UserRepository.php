<?php

namespace App\Repositories;

use App\Core\Database;

/**
 * User Data Access Repository (Developer 2)
 */
class UserRepository implements RepositoryInterface
{
    protected Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
    }

    public function all(): array
    {
        return $this->db->fetchAll("SELECT id, name, email, role, line_user_id, created_at FROM users ORDER BY id ASC");
    }

    public function findTechnicians(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, email, line_user_id FROM users WHERE role = 'technician' ORDER BY name ASC"
        );
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO users (name, email, password_hash, role, line_user_id, created_at) 
                VALUES (:name, :email, :password_hash, :role, :line_user_id, NOW())";
        
        $this->db->query($sql, [
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
            'role'          => $data['role'] ?? 'user',
            'line_user_id'  => $data['line_user_id'] ?? null,
        ]);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        foreach (['name', 'email', 'role', 'line_user_id'] as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "{$col} = :{$col}";
                $params[$col] = $data[$col];
            }
        }

        if (!empty($data['password_hash'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = $data['password_hash'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->query($sql, $params);
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM users WHERE id = :id", ['id' => $id]);
        return true;
    }

    public function countByRole(string $role): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM users WHERE role = :role", ['role' => $role]);
        return (int) ($row['cnt'] ?? 0);
    }
}
