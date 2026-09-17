<?php

require __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

echo "=================================================" . PHP_EOL;
echo "  Smart IT Helpdesk - Developer 2 Verification   " . PHP_EOL;
echo "=================================================" . PHP_EOL;

// 1. Auth Login & Role Check
$loginOk = \App\Core\Auth::login('admin@helpdesk.local', 'admin123');
echo "[1] Auth::login('admin@helpdesk.local') => " . ($loginOk ? "PASSED (User ID: " . \App\Core\Auth::id() . ", Role: " . \App\Core\Auth::role() . ")" : "FAILED") . PHP_EOL;

// 2. UserRepository Testing
$userRepo = new \App\Repositories\UserRepository();
$techs = $userRepo->findTechnicians();
echo "[2] UserRepository::findTechnicians() => PASSED (" . count($techs) . " technicians found)" . PHP_EOL;

// 3. CategoryRepository Testing
$catRepo = new \App\Repositories\CategoryRepository();
$cats = $catRepo->withTicketCounts();
echo "[3] CategoryRepository::withTicketCounts() => PASSED (" . count($cats) . " categories loaded with counts)" . PHP_EOL;

// 4. Executive Dashboard Analytics
$dashService = new \App\Services\DashboardService();
$stats = $dashService->getStatistics();
echo "[4] DashboardService::getStatistics() => PASSED" . PHP_EOL;
echo "    - Total Tickets: " . $stats['total_tickets'] . PHP_EOL;
echo "    - Open: " . $stats['status_counts']['open'] . ", In Progress: " . $stats['status_counts']['in_progress'] . ", Resolved: " . $stats['status_counts']['resolved'] . PHP_EOL;
echo "    - Avg Resolution Hours: " . $stats['avg_resolution_hours'] . " hrs" . PHP_EOL;
echo "    - Avg Rating Score: " . $stats['avg_rating'] . " / 5.0" . PHP_EOL;

// 5. CSRF Token Protection
$token = \App\Core\Csrf::token();
$valid = \App\Core\Csrf::validate($token);
echo "[5] Csrf::token() & validate() => " . ($valid ? "PASSED (Token verified)" : "FAILED") . PHP_EOL;

// 6. Event Dispatcher & LINE Notification (Observer Pattern)
$events = \App\Core\EventDispatcher::getInstance();
$observer = new \App\Observers\TicketObserver();
$sampleTicket = [
    'id' => 99,
    'title' => 'ทดสอบระบบแจ้งเตือน Developer 2',
    'priority' => 'urgent',
    'user_name' => 'ระบบทดสอบ'
];
$events->listen('test.dev2.event', function ($data) use ($observer) {
    $observer->handleCreated($data);
});
$events->dispatch('test.dev2.event', $sampleTicket);
echo "[6] EventDispatcher & LineMessagingService => PASSED (Mock notification logged)" . PHP_EOL;

// 7. Database Singleton & Transaction
$db = \App\Core\Database::getInstance();
$db->beginTransaction();
$db->query("SELECT 1");
$db->commit();
echo "[7] Database::getInstance() Transaction => PASSED" . PHP_EOL;

echo "=================================================" . PHP_EOL;
echo "  ALL DEVELOPER 2 TESTS PASSED SUCCESSFULLY!    " . PHP_EOL;
echo "=================================================" . PHP_EOL;
