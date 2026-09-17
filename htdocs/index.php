<?php

/**
 * Smart IT Helpdesk & Notification System
 * Application Front Controller
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load Composer Autoloader
$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Composer autoloader not found. Please run 'composer dump-autoload'.");
}
require_once $autoloadPath;

// Load Environment Configuration
\App\Core\Env::load(dirname(__DIR__) . '/.env');

// Register Event Listeners (Observer Pattern)
$events = \App\Core\EventDispatcher::getInstance();
$ticketObserver = new \App\Observers\TicketObserver();

$events->listen('ticket.created', function ($ticket) use ($ticketObserver) {
    $ticketObserver->handleCreated($ticket);
});

$events->listen('ticket.assigned', function ($ticket) use ($ticketObserver) {
    $ticketObserver->handleStatusChanged($ticket, 'open', 'assigned');
});

$events->listen('ticket.status_changed', function ($payload) use ($ticketObserver) {
    $ticketObserver->handleStatusChanged($payload['ticket'], $payload['from'], $payload['to']);
});

// Initialize Router
$router = new \App\Core\Router();

// Middleware shortcuts
$authMw = [\App\Core\Middleware::class, 'auth'];
$guestMw = [\App\Core\Middleware::class, 'guest'];
$csrfMw = [\App\Core\Middleware::class, 'csrf'];
$adminMw = [function () { \App\Core\Middleware::role('admin'); }];

// --- Public / Authentication Routes ---
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin'], [$guestMw]);
$router->post('/login', [\App\Controllers\AuthController::class, 'login'], [$csrfMw]);
$router->get('/register', [\App\Controllers\AuthController::class, 'showRegister'], [$guestMw]);
$router->post('/register', [\App\Controllers\AuthController::class, 'register'], [$csrfMw]);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/quick-login/{id}', [\App\Controllers\AuthController::class, 'quickLogin']);

// --- Home Route ---
$router->get('/', function () {
    if (!\App\Core\Auth::check()) {
        header('Location: /login');
        exit;
    }
    if (\App\Core\Auth::hasRole('admin')) {
        header('Location: /admin/dashboard');
        exit;
    }
    header('Location: /tickets');
    exit;
});

// --- Ticket Routes (Assigned to Developer 1 - Ticket Lifecycle & Operations) ---
// Note: When Developer 1 merges their code, TicketController will automatically handle these routes with zero conflicts.
if (class_exists(\App\Controllers\TicketController::class)) {
    $router->get('/tickets', [\App\Controllers\TicketController::class, 'index'], [$authMw]);
    $router->get('/tickets/create', [\App\Controllers\TicketController::class, 'create'], [$authMw]);
    $router->post('/tickets', [\App\Controllers\TicketController::class, 'store'], [$authMw, $csrfMw]);
    $router->get('/tickets/{id}', [\App\Controllers\TicketController::class, 'show'], [$authMw]);
    $router->post('/tickets/{id}/status', [\App\Controllers\TicketController::class, 'updateStatus'], [$authMw, $csrfMw]);
    $router->post('/tickets/{id}/comment', [\App\Controllers\TicketController::class, 'addComment'], [$authMw, $csrfMw]);
    $router->post('/tickets/{id}/comments', [\App\Controllers\TicketController::class, 'addComment'], [$authMw, $csrfMw]);
    $router->post('/tickets/{id}/rate', [\App\Controllers\TicketController::class, 'rate'], [$authMw, $csrfMw]);
} else {
    // Waiting for Developer 1 implementation to be merged
    $pendingDev1Handler = function () {
        $anonymousController = new class extends \App\Controllers\Controller {
            public function displayPending(): void {
                $this->render('placeholder/dev1_pending', [
                    'title' => 'รอการผสานโค้ดจาก Developer 1 - Smart IT Helpdesk',
                ]);
            }
        };
        $anonymousController->displayPending();
    };
    $router->get('/tickets', $pendingDev1Handler, [$authMw]);
    $router->get('/tickets/create', $pendingDev1Handler, [$authMw]);
    $router->get('/tickets/{id}', $pendingDev1Handler, [$authMw]);
}

// --- User Profile Routes ---
$router->get('/profile', [\App\Controllers\ProfileController::class, 'show'], [$authMw]);
$router->post('/profile', [\App\Controllers\ProfileController::class, 'update'], [$authMw, $csrfMw]);
$router->post('/profile/password', [\App\Controllers\ProfileController::class, 'updatePassword'], [$authMw, $csrfMw]);

// --- Admin Routes ---
$router->get('/admin/dashboard', [\App\Controllers\AdminController::class, 'dashboard'], [$adminMw]);
$router->post('/admin/tickets/{id}/assign', [\App\Controllers\AdminController::class, 'assignTicket'], [$adminMw, $csrfMw]);
$router->post('/admin/tickets/{id}/auto-assign', [\App\Controllers\AdminController::class, 'autoAssignTicket'], [$adminMw, $csrfMw]);
$router->post('/admin/tickets/auto-assign-all', [\App\Controllers\AdminController::class, 'autoAssignAll'], [$adminMw, $csrfMw]);
$router->get('/admin/users', [\App\Controllers\AdminController::class, 'users'], [$adminMw]);
$router->post('/admin/users', [\App\Controllers\AdminController::class, 'storeUser'], [$adminMw, $csrfMw]);
$router->post('/admin/users/{id}/delete', [\App\Controllers\AdminController::class, 'deleteUser'], [$adminMw, $csrfMw]);
$router->get('/admin/categories', [\App\Controllers\AdminController::class, 'categories'], [$adminMw]);
$router->post('/admin/categories', [\App\Controllers\AdminController::class, 'storeCategory'], [$adminMw, $csrfMw]);
$router->post('/admin/categories/{id}/delete', [\App\Controllers\AdminController::class, 'deleteCategory'], [$adminMw, $csrfMw]);

// --- Secure Image Serving Route ---
$router->get('/uploads/{filename}', [\App\Controllers\ImageController::class, 'serve']);
$router->get('/storage/uploads/{filename}', [\App\Controllers\ImageController::class, 'serve']);

// Dispatch Current Request
$router->dispatch();
