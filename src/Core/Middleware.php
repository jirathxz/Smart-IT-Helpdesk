<?php

namespace App\Core;

/**
 * Request Middleware Handlers
 */
class Middleware
{
    /**
     * Ensure user is authenticated, otherwise redirect to login
     */
    public static function auth(): void
    {
        if (!Auth::check()) {
            $_SESSION['flash_error'] = 'กรุณาเข้าสู่ระบบก่อนใช้งาน';
            header('Location: /login');
            exit;
        }
    }

    /**
     * Ensure user is a guest (not authenticated)
     */
    public static function guest(): void
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Ensure user has specific role(s)
     */
    public static function role(string|array $allowedRoles): void
    {
        self::auth();

        if (!Auth::hasRole($allowedRoles)) {
            http_response_code(403);
            die("403 Forbidden: คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะสิทธิ์: " . (is_array($allowedRoles) ? implode(', ', $allowedRoles) : $allowedRoles) . ")");
        }
    }

    /**
     * Ensure POST/PUT/DELETE requests have valid CSRF token
     */
    public static function csrf(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!Csrf::validate($token)) {
                http_response_code(419);
                die("419 Page Expired: CSRF Token ไม่ถูกต้องหรือไม่พบ กรุณารีเฟรชหน้าเว็บและลองใหม่");
            }
        }
    }
}
