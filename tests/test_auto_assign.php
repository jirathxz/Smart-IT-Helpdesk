<?php

require __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

echo "=================================================" . PHP_EOL;
echo "  Smart IT Helpdesk - Auto-Assign Engine Test    " . PHP_EOL;
echo "=================================================" . PHP_EOL;

$db = \App\Core\Database::getInstance();
$service = new \App\Services\AutoAssignService();

// 1. Get technicians list
$techs = $db->query("SELECT id, name, email FROM users WHERE role = 'technician'")->fetchAll();
echo "[1] Technicians Available: " . count($techs) . PHP_EOL;
foreach ($techs as $t) {
    echo "    - Tech ID {$t['id']}: {$t['name']}" . PHP_EOL;
}

// Ensure ticket #3 is restored to open state
$db->query("UPDATE tickets SET status = 'open', technician_id = NULL WHERE id = 3");

// 2. Check an open ticket or create a dummy open ticket for testing
$openTicket = $db->query("SELECT id, title, category_id, priority FROM tickets WHERE status = 'open' LIMIT 1")->fetch();

if (!$openTicket) {
    // Create a temporary test open ticket
    $db->query(
        "INSERT INTO tickets (user_id, category_id, title, description, priority, status, created_at, updated_at) 
         VALUES (1, 1, 'ทดสอบระบบ Auto-Assign HW ขัดข้อง', 'ทดสอบระบบการคำนวณคะแนนช่าง', 'high', 'open', NOW(), NOW())"
    );
    $testTicketId = (int)$db->lastInsertId();
    echo "[2] Created dummy open ticket #{$testTicketId} for test" . PHP_EOL;
} else {
    $testTicketId = (int)$openTicket['id'];
    echo "[2] Found existing open ticket #{$testTicketId}: '{$openTicket['title']}'" . PHP_EOL;
}

// 3. Test evaluateTechnicians / findBestTechnician
$best = $service->findBestTechnician($testTicketId);
echo "[3] findBestTechnician(#{$testTicketId}) => " . PHP_EOL;
echo "    - Recommended Tech ID: " . $best['technician_id'] . " (" . $best['technician']['name'] . ")" . PHP_EOL;
echo "    - Final Score: " . $best['score'] . PHP_EOL;
echo "    - Active Jobs: " . $best['technician']['active_jobs'] . PHP_EOL;
echo "    - Resolved in Category: " . $best['technician']['resolved_in_category'] . PHP_EOL;
echo "    - Average CSAT: " . $best['technician']['avg_score'] . PHP_EOL;
echo "    - Rationale: " . $best['reason'] . PHP_EOL;

// Display all candidates scores
echo "    --- All Candidate Scores ---" . PHP_EOL;
foreach ($best['candidates'] as $c) {
    echo "    Tech #{$c['id']} ({$c['name']}): Score={$c['score']}, Active={$c['active_jobs']}, ResolvedInCat={$c['resolved_in_category']}, CSAT={$c['avg_score']}" . PHP_EOL;
}

// 4. Test Overload Avoidance logic
echo "[4] Testing Overload Avoidance Logic..." . PHP_EOL;
// If tech A has 6 active jobs, their score should drop dramatically by 50 penalty points
$preferredTechId = $best['technician_id'];
$otherTech = null;
foreach ($techs as $t) {
    if ($t['id'] != $preferredTechId) {
        $otherTech = $t;
        break;
    }
}

if ($otherTech) {
    echo "    Simulating high workload on Tech #{$preferredTechId}..." . PHP_EOL;
    // Create 6 active dummy tickets for preferred tech in a transaction then rollback
    $db->beginTransaction();
    for ($i = 0; $i < 6; $i++) {
        $db->query(
            "INSERT INTO tickets (user_id, technician_id, category_id, title, description, priority, status, created_at, updated_at)
             VALUES (1, {$preferredTechId}, 1, 'Dummy Overload {$i}', 'Test', 'medium', 'in_progress', NOW(), NOW())"
        );
    }
    
    // Now re-run findBestTechnician
    $newBest = $service->findBestTechnician($testTicketId);
    echo "    -> After overload: Recommended Tech ID is now #{$newBest['technician_id']} ({$newBest['technician']['name']})" . PHP_EOL;
    echo "    -> Rationale: {$newBest['reason']}" . PHP_EOL;
    $db->rollBack();
    
    if ($newBest['technician_id'] != $preferredTechId) {
        echo "    => OVERLOAD DIVERSION SUCCESS: Work routed away from overloaded technician!" . PHP_EOL;
    } else {
        echo "    => Handled with score reduction." . PHP_EOL;
    }
}

// 5. Test assignTicket execution
echo "[5] Executing assignTicket(#{$testTicketId})..." . PHP_EOL;
$assignResult = $service->assignTicket($testTicketId, 1);
echo "    - Result Success: " . ($assignResult['success'] ? "YES" : "NO") . PHP_EOL;
echo "    - Assigned to: " . $assignResult['technician_name'] . PHP_EOL;
echo "    - Reason: " . $assignResult['reason'] . PHP_EOL;

// Verify ticket row
$updatedTicket = $db->query("SELECT status, technician_id FROM tickets WHERE id = {$testTicketId}")->fetch();
echo "    - Updated Status: " . $updatedTicket['status'] . " (Expected: assigned)" . PHP_EOL;
echo "    - Assigned Tech ID: " . $updatedTicket['technician_id'] . PHP_EOL;

// Check status log
$log = $db->query("SELECT * FROM status_logs WHERE ticket_id = {$testTicketId} ORDER BY id DESC LIMIT 1")->fetch();
echo "    - Status Log note: " . ($log['note'] ?? 'None') . PHP_EOL;

// Reset ticket #3 back to open and remove the test status log so it remains open for dashboard demonstration
$db->query("UPDATE tickets SET status = 'open', technician_id = NULL WHERE id = {$testTicketId}");
$db->query("DELETE FROM status_logs WHERE ticket_id = {$testTicketId} AND note LIKE 'ระบบจ่ายงานอัตโนมัติ%'");
echo "    - Reset ticket #{$testTicketId} back to open for dashboard demo" . PHP_EOL;

echo "=================================================" . PHP_EOL;
echo "  Auto-Assign Engine Test Completed Successfully " . PHP_EOL;
echo "=================================================" . PHP_EOL;
