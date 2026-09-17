<?php

namespace App\Core;

/**
 * HTTP Router with Parameter Extraction and Middleware Execution
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function patch(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $middleware);
    }

    public function delete(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middleware): void
    {
        // Normalize path
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        // Convert {param} to named regex pattern
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'regex'      => $regex,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Match current request and dispatch handler
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Support HTTP Method spoofing via _method POST field
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'], true)) {
                $requestMethod = $spoofed;
            }
        }

        // Extract URI path without query string
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri = '/' . trim($uri, '/');
        if ($uri === '//') {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                // Extract only named parameters
                $params = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                // Execute Route Middleware
                foreach ($route['middleware'] as $mw) {
                    if (is_callable($mw)) {
                        call_user_func($mw);
                    }
                }

                // Dispatch handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func_array($handler, $params);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$class, $method] = $handler;
                    if (class_exists($class)) {
                        $controller = new $class();
                        if (method_exists($controller, $method)) {
                            call_user_func_array([$controller, $method], $params);
                            return;
                        }
                    }
                }
            }
        }

        // If no route matched
        http_response_code(404);
        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['error' => '404 Not Found', 'path' => $uri]);
        } else {
            echo '<div style="font-family: sans-serif; text-align: center; padding: 50px;">';
            echo '<h1 style="color: #ef4444;">404 - Not Found</h1>';
            echo '<p>ไม่พบหน้าที่ท่านต้องการ: ' . htmlspecialchars($uri) . '</p>';
            echo '<a href="/" style="color: #3b82f6;">กลับสู่หน้าหลัก</a>';
            echo '</div>';
        }
    }
}
