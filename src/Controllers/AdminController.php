<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\EventDispatcher;
use App\Core\Validator;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Services\DashboardService;
use App\Core\Database;

/**
 * Admin Management Controller (Developer 2)
 */
class AdminController extends Controller
{
    private DashboardService $dashboardService;
    private UserRepository $userRepo;
    private CategoryRepository $categoryRepo;
    private Database $db;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->userRepo = new UserRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Admin Executive Dashboard
     */
    public function dashboard(): void
    {
        $period = $_GET['period'] ?? 'all';
        $validPeriods = ['all', 'today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods, true)) {
            $period = 'all';
        }

        $stats = $this->dashboardService->getStatistics($period);
        $technicians = $this->userRepo->findTechnicians();

        // Support AJAX JSON response for Vanilla JS dynamic switching
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'       => true,
                'period'        => $period,
                'period_label'  => \App\Services\DashboardService::getPeriodLabel($period),
                'stats'         => $stats,
            ]);
            exit;
        }

        // Get unassigned tickets for quick assignment widget
        $unassignedTickets = $this->db->fetchAll(
            "SELECT t.*, u.name as user_name, c.name as category_name 
             FROM tickets t 
             JOIN users u ON t.user_id = u.id 
             JOIN categories c ON t.category_id = c.id 
             WHERE t.status = 'open' 
             ORDER BY CASE t.priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END, t.created_at ASC"
        );

        $this->render('admin/dashboard', [
            'title'             => 'Executive Dashboard - Smart IT Helpdesk',
            'stats'             => $stats,
            'currentPeriod'     => $period,
            'technicians'       => $technicians,
            'unassignedTickets' => $unassignedTickets,
        ]);
    }

    /**
     * Assign technician to ticket
     */
    public function assignTicket(int $ticketId): void
    {
        $techId = (int) ($_POST['technician_id'] ?? 0);
        if ($techId <= 0) {
            $this->redirect('/admin/dashboard', null, 'กรุณาเลือกช่างเทคนิคที่ต้องการมอบหมาย');
        }

        $ticket = $this->db->fetch("SELECT * FROM tickets WHERE id = :id", ['id' => $ticketId]);
        if (!$ticket) {
            $this->redirect('/admin/dashboard', null, 'ไม่พบรหัสตั๋วที่ระบุ');
        }

        $tech = $this->userRepo->find($techId);
        if (!$tech || $tech['role'] !== 'technician') {
            $this->redirect('/admin/dashboard', null, 'ผู้ใช้ที่เลือกไม่ใช่ช่างเทคนิค');
        }

        // Update ticket in transaction
        $this->db->beginTransaction();
        try {
            $this->db->query(
                "UPDATE tickets SET technician_id = :tech_id, status = 'assigned', updated_at = NOW() WHERE id = :id",
                ['tech_id' => $techId, 'id' => $ticketId]
            );

            $this->db->query(
                "INSERT INTO status_logs (ticket_id, changed_by, from_status, to_status, note, created_at) 
                 VALUES (:ticket_id, :changed_by, :from_status, 'assigned', :note, NOW())",
                [
                    'ticket_id'   => $ticketId,
                    'changed_by'  => Auth::id(),
                    'from_status' => $ticket['status'],
                    'note'        => "ผู้ดูแลระบบมอบหมายงานให้ช่าง {$tech['name']}",
                ]
            );

            $this->db->commit();

            // Prepare payload for Event
            $ticket['technician_id'] = $techId;
            $ticket['technician_name'] = $tech['name'];
            $ticket['tech_line_id'] = $tech['line_user_id'];
            EventDispatcher::getInstance()->dispatch('ticket.assigned', $ticket);

            $this->redirect("/tickets/{$ticketId}", "มอบหมายงานให้ช่าง {$tech['name']} เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->redirect('/admin/dashboard', null, "เกิดข้อผิดพลาด: " . $e->getMessage());
        }
    }

    /**
     * User Management
     */
    public function users(): void
    {
        $users = $this->userRepo->all();
        $this->render('admin/users', [
            'title' => 'จัดการผู้ใช้งาน - Smart IT Helpdesk',
            'users' => $users,
        ]);
    }

    public function storeUser(): void
    {
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'name'     => 'required|min:3',
            'email'    => 'required|email',
            'password' => 'required|min:6',
            'role'     => 'required|in:user,technician,admin',
        ])) {
            $this->redirect('/admin/users', null, $validator->firstError());
        }

        if ($this->userRepo->findByEmail($_POST['email'])) {
            $this->redirect('/admin/users', null, 'อีเมลนี้มีอยู่ในระบบแล้ว');
        }

        $this->userRepo->create([
            'name'          => trim($_POST['name']),
            'email'         => trim($_POST['email']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role'          => $_POST['role'],
            'line_user_id'  => trim($_POST['line_user_id'] ?? '') ?: null,
        ]);

        $this->redirect('/admin/users', 'สร้างผู้ใช้ใหม่สำเร็จ');
    }

    public function deleteUser(int $id): void
    {
        if ($id === Auth::id()) {
            $this->redirect('/admin/users', null, 'ไม่สามารถลบบัญชีของตนเองที่กำลังล็อกอินอยู่ได้');
        }

        $this->userRepo->delete($id);
        $this->redirect('/admin/users', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }

    /**
     * Category Management
     */
    public function categories(): void
    {
        $categories = $this->categoryRepo->withTicketCounts();
        $this->render('admin/categories', [
            'title'      => 'จัดการหมวดหมู่งานซ่อม - Smart IT Helpdesk',
            'categories' => $categories,
        ]);
    }

    public function storeCategory(): void
    {
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'name' => 'required|min:2',
        ])) {
            $this->redirect('/admin/categories', null, $validator->firstError());
        }

        $this->categoryRepo->create([
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description'] ?? '') ?: null,
        ]);

        $this->redirect('/admin/categories', 'เพิ่มหมวดหมู่งานซ่อมสำเร็จ');
    }

    public function deleteCategory(int $id): void
    {
        try {
            $this->categoryRepo->delete($id);
            $this->redirect('/admin/categories', 'ลบหมวดหมู่สำเร็จ');
        } catch (\Exception $e) {
            $this->redirect('/admin/categories', null, 'ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากมีตั๋วงานซ่อมเชื่อมโยงอยู่');
        }
    }
}
