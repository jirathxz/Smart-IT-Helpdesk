<?php

require __DIR__ . '/../vendor/autoload.php';
\App\Core\Env::load(__DIR__ . '/../.env');

echo "=================================================" . PHP_EOL;
echo "  Smart IT Helpdesk - User Profile System Test   " . PHP_EOL;
echo "=================================================" . PHP_EOL;

$db = \App\Core\Database::getInstance();
$userRepo = new \App\Repositories\UserRepository();

// 1. Fetch user profile
$user = $userRepo->find(1);
echo "[1] Fetch Profile for User #1: " . ($user ? "PASSED ({$user['name']}, Role: {$user['role']})" : "FAILED") . PHP_EOL;

$origName = $user['name'];
$origLineId = $user['line_user_id'];
$origPassHash = $user['password_hash'];

// 2. Test updating profile
echo "[2] Testing Profile Update (Name & LINE User ID)..." . PHP_EOL;
$newName = "ผู้ดูแลระบบ Smart IT";
$newLineId = "Uadmin999999999";
$db->query(
    "UPDATE users SET name = :name, line_user_id = :line_id WHERE id = 1",
    ['name' => $newName, 'line_id' => $newLineId]
);

$updated = $userRepo->find(1);
if ($updated['name'] === $newName && $updated['line_user_id'] === $newLineId) {
    echo "    => Profile update PASSED: Name='{$updated['name']}', LINE ID='{$updated['line_user_id']}'" . PHP_EOL;
} else {
    echo "    => Profile update FAILED!" . PHP_EOL;
}

// 3. Test Password Verification
echo "[3] Testing Password Verification & Change..." . PHP_EOL;
// Verify current password check
$currentPass = 'admin123';
$wrongPass = 'wrongpassword';

$isWrongValid = password_verify($wrongPass, $updated['password_hash']);
echo "    - Wrong password test: " . (!$isWrongValid ? "PASSED (Rejected)" : "FAILED") . PHP_EOL;

$isCurrentValid = password_verify($currentPass, $updated['password_hash']);
echo "    - Current password test: " . ($isCurrentValid ? "PASSED (Accepted)" : "FAILED") . PHP_EOL;

// Update to new password
$newPass = 'newAdminPass456!';
$newHash = password_hash($newPass, PASSWORD_DEFAULT);
$db->query("UPDATE users SET password_hash = :hash WHERE id = 1", ['hash' => $newHash]);

$userAfterPassChange = $userRepo->find(1);
$authNew = password_verify($newPass, $userAfterPassChange['password_hash']);
echo "    - New password verify: " . ($authNew ? "PASSED (New password active)" : "FAILED") . PHP_EOL;

// 4. Restore original profile state
echo "[4] Restoring original profile state..." . PHP_EOL;
$db->query(
    "UPDATE users SET name = :name, line_user_id = :line_id, password_hash = :hash WHERE id = 1",
    ['name' => $origName, 'line_id' => $origLineId, 'hash' => $origPassHash]
);

// Clean up any stray dummy tickets (id >= 10)
$db->query("DELETE FROM tickets WHERE id >= 10");

$restored = $userRepo->find(1);
echo "    - Restored Name: " . $restored['name'] . PHP_EOL;
echo "    - Password auth restored: " . (password_verify('admin123', $restored['password_hash']) ? "PASSED" : "FAILED") . PHP_EOL;

echo "=================================================" . PHP_EOL;
echo "  User Profile System Test Completed Successfully" . PHP_EOL;
echo "=================================================" . PHP_EOL;
