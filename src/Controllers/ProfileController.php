<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Validator;
use App\Repositories\UserRepository;

/**
 * User Profile Controller
 * Manages user personal details, LINE notification credentials, and password security.
 */
class ProfileController extends Controller
{
    private UserRepository $userRepo;
    private Database $db;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Display the User Profile & Settings page
     */
    public function show(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->redirect('/login');
        }

        $user = $this->userRepo->find($userId);
        if (!$user) {
            $this->redirect('/logout');
        }

        // Gather role-based ticket activity stats
        $stats = [
            'total_created'  => 0,
            'active_created' => 0,
            'total_assigned' => 0,
            'active_assigned'=> 0,
            'resolved_jobs'  => 0,
            'avg_rating'     => 0.0,
            'recent_tickets' => [],
        ];

        if ($user['role'] === 'technician') {
            $techStats = $this->db->fetch(
                "SELECT 
                    COUNT(*) as total_assigned,
                    SUM(CASE WHEN status IN ('open', 'assigned', 'in_progress') THEN 1 ELSE 0 END) as active_assigned,
                    SUM(CASE WHEN status IN ('resolved', 'closed') THEN 1 ELSE 0 END) as resolved_jobs
                 FROM tickets 
                 WHERE technician_id = :id",
                ['id' => $userId]
            );
            $stats['total_assigned'] = (int) ($techStats['total_assigned'] ?? 0);
            $stats['active_assigned'] = (int) ($techStats['active_assigned'] ?? 0);
            $stats['resolved_jobs'] = (int) ($techStats['resolved_jobs'] ?? 0);

            $rateRow = $this->db->fetch(
                "SELECT AVG(r.score) as avg_score 
                 FROM ratings r 
                 JOIN tickets t ON r.ticket_id = t.id 
                 WHERE t.technician_id = :id",
                ['id' => $userId]
            );
            $stats['avg_rating'] = !empty($rateRow['avg_score']) ? round((float) $rateRow['avg_score'], 1) : 0.0;

            $stats['recent_tickets'] = $this->db->fetchAll(
                "SELECT t.id, t.title, t.status, t.priority, t.created_at, c.name as category_name
                 FROM tickets t
                 JOIN categories c ON t.category_id = c.id
                 WHERE t.technician_id = :id
                 ORDER BY t.created_at DESC
                 LIMIT 5",
                ['id' => $userId]
            );
        } else {
            $userStats = $this->db->fetch(
                "SELECT 
                    COUNT(*) as total_created,
                    SUM(CASE WHEN status IN ('open', 'assigned', 'in_progress') THEN 1 ELSE 0 END) as active_created
                 FROM tickets 
                 WHERE user_id = :id",
                ['id' => $userId]
            );
            $stats['total_created'] = (int) ($userStats['total_created'] ?? 0);
            $stats['active_created'] = (int) ($userStats['active_created'] ?? 0);

            $stats['recent_tickets'] = $this->db->fetchAll(
                "SELECT t.id, t.title, t.status, t.priority, t.created_at, c.name as category_name
                 FROM tickets t
                 JOIN categories c ON t.category_id = c.id
                 WHERE t.user_id = :id
                 ORDER BY t.created_at DESC
                 LIMIT 5",
                ['id' => $userId]
            );
        }

        $this->render('profile/show', [
            'title' => 'โปรไฟล์และความปลอดภัย - Smart IT Helpdesk',
            'user'  => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Update user personal info and LINE notification settings
     */
    public function update(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->redirect('/login');
        }

        $name = trim($_POST['name'] ?? '');
        $lineUserId = trim($_POST['line_user_id'] ?? '');

        if (mb_strlen($name) < 2) {
            $this->redirect('/profile', null, 'กรุณาระบุชื่อ-นามสกุลอย่างน้อย 2 ตัวอักษร');
        }

        $data = [
            'name'         => $name,
            'line_user_id' => !empty($lineUserId) ? $lineUserId : null,
        ];

        $this->userRepo->update($userId, $data);

        // Refresh session
        $freshUser = $this->userRepo->find($userId);
        if ($freshUser) {
            unset($freshUser['password_hash']);
            $_SESSION['user'] = $freshUser;
        }

        $this->redirect('/profile', 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว');
    }

    /**
     * Update user password
     */
    public function updatePassword(): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->redirect('/login');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            $this->redirect('/profile#security', null, 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร');
        }

        if ($newPassword !== $confirmPassword) {
            $this->redirect('/profile#security', null, 'รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน');
        }

        $user = $this->userRepo->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $this->redirect('/profile#security', null, 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userRepo->update($userId, ['password_hash' => $newHash]);

        $this->redirect('/profile#security', 'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อยแล้ว');
    }
}
