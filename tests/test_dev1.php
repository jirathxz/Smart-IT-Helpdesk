<?php
require_once __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

use App\Core\Database;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use App\Repositories\TicketRepository;
use App\Repositories\CommentRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\RatingRepository;
use App\Services\TicketService;
use App\Services\TicketStatusService;
use App\Services\CommentService;
use App\Services\FileUploader;

echo "========================================================\n";
echo " Running Unit & Integration Tests for Developer 1 Scope \n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $testName) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$testName}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$testName}\n";
        $failed++;
    }
}

// Test 1: Enums
echo "1. Testing Enums...\n";
assertTest(TicketStatus::OPEN->label() === 'รอดำเนินการ (Open)', 'TicketStatus::OPEN label is correct');
assertTest(TicketStatus::CLOSED->stepIndex() === 5, 'TicketStatus::CLOSED stepIndex is 5');
assertTest(TicketPriority::URGENT->label() === 'ด่วนที่สุด (Urgent)', 'TicketPriority::URGENT label is correct');

// Test 2: Database Connection & Repositories
echo "\n2. Testing Database & Repositories...\n";
$db = Database::getInstance();
assertTest($db->getPdo() instanceof PDO, 'Database PDO instance connected');

$ticketRepo = new TicketRepository($db);
$ticket = $ticketRepo->find(1);
assertTest($ticket !== null && isset($ticket['title']), 'TicketRepository::find(1) retrieved sample ticket');

$userTickets = $ticketRepo->findByUser(4);
assertTest(is_array($userTickets), 'TicketRepository::findByUser(4) returned array');

// Test 3: Ticket Creation via TicketService
echo "\n3. Testing TicketService Creation...\n";
$ticketService = new TicketService();
$newTicketId = $ticketService->createTicket([
    'title'       => 'ทดสอบระบบตั๋ว Developer 1 Unit Test',
    'description' => 'รายละเอียดทดสอบอาการเสียในห้องปฏิบัติการ IT',
    'category_id' => 1,
    'priority'    => 'high',
], 4);

assertTest($newTicketId > 0, "TicketService::createTicket created ticket #{$newTicketId}");
$createdTicket = $ticketRepo->find($newTicketId);
assertTest($createdTicket['status'] === 'open', 'New ticket initial status is open');

// Test 4: State Machine - TicketStatusService
echo "\n4. Testing State Machine (TicketStatusService)...\n";
$statusService = new TicketStatusService();

$admin = ['id' => 1, 'name' => 'Admin', 'role' => 'admin'];
$tech = ['id' => 2, 'name' => 'Somchai', 'role' => 'technician'];
$user = ['id' => 4, 'name' => 'Somying', 'role' => 'user'];

// 4.1 Invalid Transition test (open -> closed should fail)
$invalidCaught = false;
try {
    $statusService->transition($newTicketId, 'closed', $user, ['score' => 5]);
} catch (InvalidArgumentException $e) {
    $invalidCaught = true;
}
assertTest($invalidCaught, 'State Machine blocked illegal transition (open -> closed)');

// 4.2 Valid Transition: open -> assigned by Admin
$statusService->transition($newTicketId, 'assigned', $admin, [
    'technician_id' => 2,
    'note' => 'จ่ายงานทดสอบให้ช่างสมชาย'
]);
$t1 = $ticketRepo->find($newTicketId);
assertTest($t1['status'] === 'assigned' && (int)$t1['technician_id'] === 2, 'Transitioned open -> assigned');

// 4.3 Unauthorized check: User cannot accept job
$unauthCaught = false;
try {
    $statusService->transition($newTicketId, 'in_progress', $user);
} catch (Exception $e) {
    $unauthCaught = true;
}
assertTest($unauthCaught, 'Role check blocked user from starting technician work');

// 4.4 Valid Transition: assigned -> in_progress by assigned tech
$statusService->transition($newTicketId, 'in_progress', $tech, ['note' => 'ช่างเริ่มงานแล้ว']);
$t2 = $ticketRepo->find($newTicketId);
assertTest($t2['status'] === 'in_progress', 'Transitioned assigned -> in_progress');

// 4.5 Valid Transition: in_progress -> resolved by tech
$statusService->transition($newTicketId, 'resolved', $tech, ['note' => 'ซ่อมเสร็จเรียบร้อย ทดสอบผ่าน']);
$t3 = $ticketRepo->find($newTicketId);
assertTest($t3['status'] === 'resolved' && !empty($t3['resolved_at']), 'Transitioned in_progress -> resolved with resolved_at');

// 4.6 Valid Transition: resolved -> closed by user with 5-star rating
$statusService->transition($newTicketId, 'closed', $user, [
    'score' => 5,
    'feedback' => 'บริการดีเยี่ยม รวดเร็วมากครับ'
]);
$t4 = $ticketRepo->find($newTicketId);
assertTest($t4['status'] === 'closed' && !empty($t4['closed_at']), 'Transitioned resolved -> closed with closed_at');

// Test 5: Rating & Comments
echo "\n5. Testing Ratings & Comments...\n";
$ratingRepo = new RatingRepository();
$rating = $ratingRepo->findByTicketId($newTicketId);
assertTest($rating !== null && (int)$rating['score'] === 5, 'Rating recorded 5 stars successfully');

$commentService = new CommentService();
$comm = $commentService->addComment($newTicketId, 4, 'ขอบคุณทีมช่างมากครับ');
assertTest($comm['id'] > 0, 'CommentService::addComment added comment successfully');

$statusLogRepo = new StatusLogRepository();
$logs = $statusLogRepo->findByTicketId($newTicketId);
assertTest(count($logs) >= 5, 'Audit trail logged full lifecycle (initial, assigned, in_progress, resolved, closed)');

echo "\n========================================================\n";
echo " Test Summary: {$passed} Passed, {$failed} Failed\n";
echo "========================================================\n";

if ($failed > 0) {
    exit(1);
}
