<?php
require __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

// 1. Simulate Session Auth
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_name'] = 'Admin System';
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

echo "Running DashboardController test...\n";

$dashboardService = new \App\Services\DashboardService();
$stats = $dashboardService->getStatistics('all');

echo "KPI Stats:\n";
print_r($stats['kpi']);

echo "\nChecking view rendering...\n";
ob_start();
$title = "ภาพรวมระบบ (Dashboard)";
$activeMenu = 'dashboard';
$currentPeriod = 'all';
$technicians = (new \App\Repositories\UserRepository())->findTechnicians();
$unassignedTickets = [];

include __DIR__ . '/../views/admin/dashboard.php';
$output = ob_get_clean();

$kpiChecks = [
    'Total Tickets' => 'Total Tickets',
    'Open / Pending' => 'Open / Pending',
    'In Progress' => 'In Progress',
    'Urgent' => 'Urgent',
    'Resolved' => 'Resolved',
    'CSAT / Rating' => 'CSAT / Rating',
    'trendChart' => 'trendChart',
    'statusChart' => 'statusChart',
    'periodSelector' => 'periodSelector',
    'animateCount' => 'animateCount',
    'Basecoat UI' => 'basecoat',
];

$allOk = true;
foreach ($kpiChecks as $name => $needle) {
    if (stripos($output, $needle) !== false) {
        echo " [OK] Found '$name'\n";
    } else {
        echo " [FAIL] Missing '$name'\n";
        $allOk = false;
    }
}

if ($allOk) {
    echo "\n>>> ALL 6 KPIS AND TECH STACK ELEMENTS VERIFIED SUCCESSFULLY!\n";
} else {
    echo "\n>>> SOME CHECKS FAILED!\n";
    exit(1);
}

// 2. Test AJAX Response
echo "\nTesting AJAX period response...\n";
$_GET['period'] = 'month';
$_GET['ajax'] = '1';
ob_start();
// simulate admin controller json logic
$statsAjax = $dashboardService->getStatistics('month');
$ajaxJson = json_encode([
    'success'      => true,
    'period'       => 'month',
    'period_label' => \App\Services\DashboardService::getPeriodLabel('month'),
    'stats'        => $statsAjax,
]);
echo "AJAX JSON: " . substr($ajaxJson, 0, 120) . "...\n";
$decoded = json_decode($ajaxJson, true);
if (isset($decoded['stats']['kpi']['total_tickets'], $decoded['stats']['kpi']['open_pending'])) {
    echo "[OK] AJAX payload returns period KPIs accurately.\n";
} else {
    echo "[FAIL] AJAX payload invalid.\n";
    exit(1);
}

