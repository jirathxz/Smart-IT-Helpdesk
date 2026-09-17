<?php

namespace App\Controllers;

/**
 * Base Application Controller
 */
abstract class Controller
{
    /**
     * Render a view wrapped inside a layout
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $viewsDir = dirname(__DIR__, 2) . '/views';
        $viewFile = "{$viewsDir}/{$view}.php";

        if (!file_exists($viewFile)) {
            die("Error: View [{$view}] not found at {$viewFile}");
        }

        extract($data, EXTR_SKIP);

        // Capture view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // If layout specified, render inside layout
        if ($layout) {
            $layoutFile = "{$viewsDir}/layouts/{$layout}.php";
            if (file_exists($layoutFile)) {
                include $layoutFile;
                return;
            }
        }

        echo $content;
    }

    /**
     * Send JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a specific URL with optional flash message
     */
    protected function redirect(string $url, ?string $success = null, ?string $error = null): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($success) {
            $_SESSION['flash_success'] = $success;
        }
        if ($error) {
            $_SESSION['flash_error'] = $error;
        }

        header("Location: {$url}");
        exit;
    }
}
