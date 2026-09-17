<?php
require __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

echo "=================================================\n";
echo "  Testing Qualitative Executive Dashboard Features\n";
echo "=================================================\n";

$dashboardService = new \App\Services\DashboardService();
$stats = $dashboardService->getStatistics('all');

// 1. Verify 6 KPIs
echo "\n[1] Checking 6 Core KPIs:\n";
$kpis = ['total_tickets', 'open_pending', 'in_progress', 'urgent', 'resolved', 'csat_rating'];
foreach ($kpis as $key) {
    if (isset($stats['kpi'][$key])) {
        echo "  [OK] KPI '{$key}': " . $stats['kpi'][$key] . "\n";
    } else {
        echo "  [FAIL] Missing KPI '{$key}'\n";
        exit(1);
    }
}

// 2. Verify Intake vs Clearance Ratio
echo "\n[2] Checking Intake vs Clearance (Burn-down):\n";
$intake = $stats['intake_clearance'];
assert(isset($intake['intake'], $intake['clearance'], $intake['rate'], $intake['net_delta'], $intake['status']));
echo "  [OK] Intake: {$intake['intake']}, Clearance: {$intake['clearance']}, Rate: {$intake['rate']}%, Delta: {$intake['net_delta']}, Status: {$intake['status']}\n";

// 3. Verify Executive Health Summary
echo "\n[3] Checking Executive Health Summary:\n";
$exec = $stats['executive_summary'];
assert(isset($exec['level'], $exec['headline'], $exec['detail'], $exec['badge']));
echo "  [OK] Level: {$exec['level']}\n";
echo "  [OK] Headline: {$exec['headline']}\n";
echo "  [OK] Detail: {$exec['detail']}\n";

// 4. Verify Technician Qualitative Workload & Comparison
echo "\n[4] Checking Technician Qualitative Workload:\n";
$workload = $stats['technician_workload'];
assert(is_array($workload) && count($workload) > 0);
foreach ($workload as $tech) {
    echo "  - Tech: {$tech['name']}\n";
    echo "    Capacity Status: {$tech['capacity_status']} ({$tech['status_label']})\n";
    echo "    Open Jobs: {$tech['open_jobs']}, Resolved Jobs: {$tech['resolved_jobs']}\n";
    echo "    Resolution Rate: {$tech['resolution_rate']}%\n";
    echo "    CSAT: {$tech['avg_csat']} (from {$tech['rating_count']} ratings)\n";
    echo "    MTTR: " . ($tech['avg_minutes'] ? $tech['avg_minutes'] . 'm' : '—') . "\n";
    assert(isset($tech['capacity_status'], $tech['status_badge'], $tech['resolution_rate']));
}

// 5. Verify Chart Data (including Technician Comparison Bar Chart)
echo "\n[5] Checking Chart Data:\n";
$chartData = $stats['chart_data'];
assert(isset($chartData['status'], $chartData['trend'], $chartData['technicians']));
echo "  [OK] Status Chart datasets verified.\n";
echo "  [OK] Trend Chart datasets verified.\n";
echo "  [OK] Technician Comparison Chart: " . count($chartData['technicians']['labels']) . " technicians loaded.\n";

// 6. Verify Full View HTML Rendering
echo "\n[6] Checking Full View HTML Rendering:\n";
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_name'] = 'Admin System';
$_SESSION['csrf_token'] = 'test-token';

ob_start();
$title = "ภาพรวมระบบ (Executive Dashboard)";
$currentPeriod = 'all';
$technicians = (new \App\Repositories\UserRepository())->findTechnicians();
$unassignedTickets = [];

include __DIR__ . '/../views/admin/dashboard.php';
$html = ob_get_clean();

$expectedNeedles = [
    'exec-banner',
    'exec-headline',
    'intake-rate',
    'kpi-total',
    'kpi-open',
    'kpi-in-progress',
    'kpi-urgent',
    'kpi-resolved',
    'kpi-csat',
    'trendChart',
    'techChart',
    'statusChart',
    'technician-table-body',
    'Burn-down & Clearance',
];

foreach ($expectedNeedles as $needle) {
    if (strpos($html, $needle) !== false) {
        echo "  [OK] Rendered HTML contains '{$needle}'\n";
    } else {
        echo "  [FAIL] Missing '{$needle}' in HTML output\n";
        exit(1);
    }
}

echo "\n=================================================\n";
echo "  ALL QUALITATIVE DASHBOARD TESTS PASSED!\n";
echo "=================================================\n";
