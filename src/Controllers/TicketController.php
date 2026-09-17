<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\EventDispatcher;
use App\Core\Database;
use App\Repositories\TicketRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\RatingRepository;
use App\Services\TicketService;
use App\Services\TicketStatusService;
use App\Services\FileUploader;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;

/**
 * Ticket Lifecycle & Operations Controller (Developer 1)
 */
class TicketController extends Controller
{
    private TicketRepository $ticketRepo;
    private CategoryRepository $categoryRepo;
    private CommentRepository $commentRepo;
    private StatusLogRepository $statusLogRepo;
    private RatingRepository $ratingRepo;
    private TicketStatusService $statusService;
    private FileUploader $fileUploader;

    public function __construct()
    {
        $this->ticketRepo = new TicketRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->commentRepo = new CommentRepository();
        $this->statusLogRepo = new StatusLogRepository();
        $this->ratingRepo = new RatingRepository();
        $this->statusService = new TicketStatusService();
        $this->fileUploader = new FileUploader();
    }

    /**
     * List tickets according to user role and filters
     */
    public function index(): void
    {
        $user = Auth::user();
        $role = Auth::role();
        $userId = Auth::id();

        $statusFilter = $_GET['status'] ?? '';
        $priorityFilter = $_GET['priority'] ?? '';
        $keyword = trim($_GET['keyword'] ?? '');
        $view = $_GET['view'] ?? '';

        $filters = [
            'status'   => $statusFilter,
            'priority' => $priorityFilter,
            'keyword'  => $keyword,
        ];

        if ($role === 'technician') {
            if ($view === 'all') {
                $allTickets = $this->ticketRepo->search($keyword, $filters);
                $counts = $this->ticketRepo->getCounts();
                $pageSubTitle = "ภาพรวมงานแจ้งซ่อมทั้งหมดในระบบ";
            } else {
                $allTickets = $this->ticketRepo->findByTechnician($userId, $filters);
                $counts = $this->ticketRepo->getCounts(null, $userId);
                $pageSubTitle = "รายการงานซ่อมที่ได้รับมอบหมาย";
            }
        } elseif ($role === 'admin') {
            $allTickets = $this->ticketRepo->search($keyword, $filters);
            $counts = $this->ticketRepo->getCounts();
            $pageSubTitle = "จัดการ ติดตาม และตรวจสอบสถานะงานแจ้งซ่อม";
        } else {
            $allTickets = $this->ticketRepo->findByUser($userId, $filters);
            $counts = $this->ticketRepo->getCounts($userId);
            $pageSubTitle = "ติดตามสถานะงานแจ้งซ่อมของท่าน";
        }

        // 4 Clean Enterprise Metrics
        $totalCount = (int) $counts['total'];
        $pendingCount = (int) ($counts['count_open'] + $counts['count_assigned']);
        $inProgressCount = (int) $counts['count_in_progress'];
        $resolvedCount = (int) $counts['count_resolved'];

        // Pagination
        $perPage = 10;
        $totalFiltered = count($allTickets);
        $totalPages = max(1, (int) ceil($totalFiltered / $perPage));
        $currentPage = max(1, min((int) ($_GET['page'] ?? 1), $totalPages));
        $offset = ($currentPage - 1) * $perPage;
        $tickets = array_slice($allTickets, $offset, $perPage);

        $this->render('tickets/index', [
            'title'             => 'งานแจ้งซ่อม - Smart IT Helpdesk',
            'tickets'           => $tickets,
            'totalFiltered'     => $totalFiltered,
            'currentPage'       => $currentPage,
            'totalPages'        => $totalPages,
            'perPage'           => $perPage,
            'totalCount'        => $totalCount,
            'pendingCount'      => $pendingCount,
            'inProgressCount'   => $inProgressCount,
            'resolvedCount'     => $resolvedCount,
            'statusFilter'      => $statusFilter,
            'priorityFilter'    => $priorityFilter,
            'keyword'           => $keyword,
            'view'              => $view,
            'pageSubTitle'      => $pageSubTitle,
            'role'              => $role,
        ]);
    }

    /**
     * Show ticket creation form
     */
    public function create(): void
    {
        $db = Database::getInstance();
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name ASC");

        $this->render('tickets/create', [
            'title'      => 'แจ้งซ่อมใหม่ - Smart IT Helpdesk',
            'categories' => $categories,
            'csrfToken'  => Csrf::token(),
        ]);
    }

    /**
     * Store new ticket
     */
    public function store(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/tickets/create', null, 'ความปลอดภัยไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        $userId = Auth::id();
        $title = trim($_POST['title'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $priority = $_POST['priority'] ?? 'medium';
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($categoryId) || empty($description)) {
            $this->redirect('/tickets/create', null, 'กรุณากรอกข้อมูลที่มีเครื่องหมาย * ให้ครบถ้วน');
        }

        if (!in_array($priority, ['low', 'medium', 'high', 'urgent'], true)) {
            $priority = 'medium';
        }

        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            try {
                $imagePath = $this->fileUploader->upload($_FILES['image']);
            } catch (\Exception $e) {
                $this->redirect('/tickets/create', null, 'การอัปโหลดไฟล์ล้มเหลว: ' . $e->getMessage());
            }
        }

        $ticketId = $this->ticketRepo->create([
            'user_id'     => $userId,
            'category_id' => $categoryId,
            'title'       => $title,
            'description' => $description,
            'priority'    => $priority,
            'status'      => 'open',
        ]);

        // If an image was attached on ticket creation, record as initial comment
        if ($imagePath) {
            $this->commentRepo->create(
                $ticketId,
                $userId,
                'แนบรูปภาพประกอบอาการขัดข้องตอนแจ้งซ่อม',
                $imagePath
            );
        }

        // Audit Trail log
        $this->statusLogRepo->log($ticketId, $userId, null, 'open', 'สร้างใบแจ้งซ่อมใหม่');

        // Dispatch Event for Notifications (LINE Messaging Observer)
        $newTicket = $this->ticketRepo->find($ticketId);
        EventDispatcher::getInstance()->dispatch('ticket.created', $newTicket);

        $this->redirect('/tickets', 'ส่งใบแจ้งซ่อม #' . str_pad((string)$ticketId, 4, '0', STR_PAD_LEFT) . ' เข้าระบบเรียบร้อยแล้ว');
    }

    /**
     * Show ticket detail view
     */
    public function show(int $id): void
    {
        $ticket = $this->ticketRepo->find($id);
        if (!$ticket) {
            $this->redirect('/tickets', null, 'ไม่พบข้อมูลตั๋วแจ้งซ่อมดังกล่าว');
        }

        $role = Auth::role();
        $userId = Auth::id();

        // RBAC check: regular user can only view their own tickets
        if ($role === 'user' && (int)$ticket['user_id'] !== $userId) {
            $this->redirect('/tickets', null, 'ท่านไม่มีสิทธิ์เข้าถึงรายละเอียดงานซ่อมนี้');
        }

        $comments = $this->commentRepo->findByTicketId($id);
        $statusLogs = $this->statusLogRepo->findByTicketId($id);
        $rating = $this->ratingRepo->findByTicketId($id);

        $this->render('tickets/show', [
            'title'      => 'ใบแจ้งซ่อม #TK-' . str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT) . ' - Smart IT Helpdesk',
            'ticket'     => $ticket,
            'comments'   => $comments,
            'statusLogs' => $statusLogs,
            'rating'     => $rating,
            'role'       => $role,
            'userId'     => $userId,
            'csrfToken'  => Csrf::token(),
        ]);
    }

    /**
     * Update ticket status (Technician accept/resolve, User close/rework)
     */
    public function updateStatus(int $id): void
    {
        $token = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($token)) {
            $this->redirect('/tickets/' . $id, null, 'ความปลอดภัยไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        $ticket = $this->ticketRepo->find($id);
        if (!$ticket) {
            $this->redirect('/tickets', null, 'ไม่พบตั๋วแจ้งซ่อม');
        }

        $toStatus = $_POST['to_status'] ?? '';
        $user = Auth::user();

        $payload = $_POST;
        if (!empty($_FILES['image']['name'])) {
            try {
                $payload['image_path'] = $this->fileUploader->upload($_FILES['image']);
            } catch (\Exception $e) {
                $this->redirect('/tickets/' . $id, null, 'อัปโหลดภาพหลักฐานล้มเหลว: ' . $e->getMessage());
            }
        }

        try {
            $this->statusService->transition($id, $toStatus, $user, $payload);
        } catch (\Exception $e) {
            $this->redirect('/tickets/' . $id, null, $e->getMessage());
        }

        // Trigger Event Dispatcher for Notifications (LINE Messaging Observer)
        $updatedTicket = $this->ticketRepo->find($id);
        EventDispatcher::getInstance()->dispatch('ticket.status_changed', [
            'ticket' => $updatedTicket,
            'from'   => $ticket['status'],
            'to'     => $toStatus,
        ]);

        $this->redirect('/tickets/' . $id, 'อัปเดตสถานะงานเรียบร้อยแล้ว');
    }

    /**
     * Add comment to ticket
     */
    public function addComment(int $id): void
    {
        $token = $_POST['_csrf_token'] ?? $_POST['csrf_token'] ?? '';
        if (!Csrf::validate($token)) {
            $this->redirect('/tickets/' . $id, null, 'ความปลอดภัยไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง');
        }

        $body = trim($_POST['body'] ?? '');
        if (empty($body)) {
            $this->redirect('/tickets/' . $id . '#comments', null, 'กรุณาพิมพ์ข้อความที่ต้องการส่ง');
        }

        $imagePath = null;
        if (!empty($_FILES['comment_image']['name'])) {
            try {
                $imagePath = $this->fileUploader->upload($_FILES['comment_image']);
            } catch (\Exception $e) {
                $this->redirect('/tickets/' . $id . '#comments', null, 'อัปโหลดภาพล้มเหลว: ' . $e->getMessage());
            }
        }

        $this->commentRepo->create(
            $id,
            Auth::id(),
            $body,
            $imagePath
        );

        $this->redirect('/tickets/' . $id . '#comments', 'ส่งข้อความเรียบร้อยแล้ว');
    }

    /**
     * Direct rating handler
     */
    public function rate(int $id): void
    {
        $this->updateStatus($id);
    }
}
