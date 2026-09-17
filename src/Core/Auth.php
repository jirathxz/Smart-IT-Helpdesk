<?php

namespace App\Core;

/**
 * Authentication and Session Management
 */
class Auth
{
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
    }

    /**
     * Attempt login with email and password
     */
    public static function login(string $email, string $password): bool
    {
        self::startSession();
        $db = Database::getInstance();

        $user = $db->fetch(
            "SELECT * FROM users WHERE email = :email LIMIT 1",
            ['email' => $email]
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                @session_regenerate_id(true);
            }
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            return true;
        }

        return false;
    }

    /**
     * Direct login for seeded/testing accounts
     */
    public static function loginById(int $userId): bool
    {
        self::startSession();
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $userId]);

        if ($user) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                @session_regenerate_id(true);
            }
            unset($user['password_hash']);
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }

    /**
     * Log the current user out
     */
    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Get authenticated user array
     */
    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Get authenticated user ID
     */
    public static function id(): ?int
    {
        self::startSession();
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Get authenticated user Role string
     */
    public static function role(): ?string
    {
        self::startSession();
        return $_SESSION['user']['role'] ?? null;
    }

    /**
     * Check if authenticated user has one of the allowed roles
     */
    public static function hasRole(string|array $roles): bool
    {
        if (!self::check()) {
            return false;
        }

        $currentRole = self::role();
        if (is_array($roles)) {
            return in_array($currentRole, $roles, true);
        }

        return $currentRole === $roles;
    }
}
