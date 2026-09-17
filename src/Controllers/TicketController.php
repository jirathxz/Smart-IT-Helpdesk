<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\EventDispatcher;
use App\Core\Validator;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Repositories\CategoryRepository;
use App\Repositories\CommentRepository;
use App\Repositories\RatingRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use App\Services\FileUploader;
use App\Services\TicketStatusService;

/**
 * Ticket Operations Controller
 */
class TicketController extends Controller
{
    private TicketRepository $ticketRepo;
    private CategoryRepository $categoryRepo;
    private CommentRepository $commentRepo;
    private StatusLogRepository $statusLogRepo;
    private RatingRepository $ratingRepo;
    private UserRepository $userRepo;
    private TicketStatusService $statusService;
    private FileUploader $uploader;

    public function __construct()
    {
        $this->ticketRepo = new TicketRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->commentRepo = new CommentRepository();
        $this->statusLogRepo = new StatusLogRepository();
        $this->ratingRepo = new RatingRepository();
        $this->userRepo = new UserRepository();
        $this->statusService = new TicketStatusService();
        $this->uploader = new FileUploader();
    }

    /**
     * List tickets based on user role and filters
     */
    public function index(): void
    {
        $role = Auth::role();
        $userId = Auth::id();
        $filters = $_GET;

        if ($role === 'user') {
            $filters['user_id'] = $userId;
            $title = 'รายการแจ้งซ่อมของฉัน (My Tickets)';
        } elseif ($role === 'technician') {
            // By default technician views assigned or open jobs
            if (empty($filters['view']) || $filters['view'] === 'my') {
                $filters['technician_id'] = $userId;
                $title = 'งานซ่อมที่ได้รับมอบหมาย (Assigned Jobs)';
            } else {
                $title = 'ตั๋วงานซ่อมทั้งหมด (All Tickets)';
            }
        } else {
            $title = 'จัดการตั๋วงานซ่อมทั้งหมด (Admin Ticket Management)';
        }

        $tickets = $this->ticketRepo->all($filters);
        $categories = $this->categoryRepo->all();

        $this->render('tickets/index', [
            'title'      => $title,
            'tickets'    => $tickets,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }

    /**
     * Show ticket details, timeline, and comment thread
     */
    public function show(int $id): void
    {
        $ticket = $this->ticketRepo->find($id);
        if (!$ticket) {
            $this->redirect('/tickets', null, 'ไม่พบรายการตั๋วงานซ่อมที่ระบุ');
        }

        $user = Auth::user();
        $role = $user['role'];
        $userId = $user['id'];

        // Enforce RBAC view permission
        if ($role === 'user' && (int)$ticket['user_id'] !== $userId) {
            $this->redirect('/tickets', null, 'คุณไม่มีสิทธิ์ดูงานซ่อมของผู้ใช้อื่น');
        }

        $comments = $this->commentRepo->findByTicket($id);
        $statusLogs = $this->statusLogRepo->findByTicket($id);
        $rating = $this->ratingRepo->findByTicket($id);
        $technicians = ($role === 'admin') ? $this->userRepo->findTechnicians() : [];

        $this->render('tickets/show', [
            'title'       => "#{$ticket['id']} {$ticket['title']} - Smart IT Helpdesk",
            'ticket'      => $ticket,
            'comments'    => $comments,
            'statusLogs'  => $statusLogs,
            'rating'      => $rating,
            'technicians' => $technicians,
            'currentUser' => $user,
        ]);
    }

    /**
     * Render ticket creation form
     */
    public function create(): void
    {
        $categories = $this->categoryRepo->all();
        $this->render('tickets/create', [
            'title'      => 'แจ้งซ่อมอุปกรณ์ / แจ้งปัญหาใหม่ - Smart IT Helpdesk',
            'categories' => $categories,
        ]);
    }

    /**
     * Store new ticket
     */
    public function store(): void
    {
        $validator = new Validator();
        if (!$validator->validate($_POST, [
            'title'       => 'required|min:5|max:255',
            'category_id' => 'required|integer',
            'priority'    => 'required|in:low,medium,high,urgent',
            'description' => 'required|min:10',
        ])) {
            $this->redirect('/tickets/create', null, $validator->firstError());
        }

        $imagePath = null;
        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $imagePath = $this->uploader->upload($_FILES['image']);
            } catch (\Exception $e) {
                $this->redirect('/tickets/create', null, $e->getMessage());
            }
        }

        $ticketId = $this->ticketRepo->create([
            'user_id'     => Auth::id(),
            'category_id' => (int) $_POST['category_id'],
            'title'       => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'status'      => 'open',
            'priority'    => $_POST['priority'],
        ]);

        // If image uploaded, save as initial attachment comment
        if ($imagePath) {
            $this->commentRepo->create([
                'ticket_id'  => $ticketId,
                'user_id'    => Auth::id(),
                'body'       => 'แนบรูปภาพอาการปัญหาเบื้องต้น',
                'image_path' => $imagePath,
            ]);
        }

        // Record initial status log
        $this->statusLogRepo->create([
            'ticket_id'   => $ticketId,
            'changed_by'  => Auth::id(),
            'from_status' => 'open',
            'to_status'   => 'open',
            'note'        => 'เปิดตั๋วแจ้งซ่อมใหม่ในระบบ',
        ]);

        // Dispatch ticket created event (LINE notification)
        $newTicket = $this->ticketRepo->find($ticketId);
        EventDispatcher::getInstance()->dispatch('ticket.created', $newTicket);

        $this->redirect("/tickets/{$ticketId}", "สร้างรายการแจ้งซ่อม #{$ticketId} เรียบร้อยแล้ว");
    }

    /**
     * Update ticket state via State Machine
     */
    public function updateStatus(int $id): void
    {
        $ticket = $this->ticketRepo->find($id);
        if (!$ticket) {
            $this->redirect('/tickets', null, 'ไม่พบตั๋วงานซ่อม');
        }

        $targetStatusStr = $_POST['status'] ?? '';
        try {
            $toStatus = TicketStatus::from($targetStatusStr);
        } catch (\ValueError $e) {
            $this->redirect("/tickets/{$id}", null, 'สถานะเป้าหมายไม่ถูกต้อง');
        }

        $extra = [
            'note'           => trim($_POST['note'] ?? ''),
            'technician_id'  => $_POST['technician_id'] ?? null,
            'score'          => $_POST['score'] ?? null,
            'feedback'       => trim($_POST['feedback'] ?? ''),
        ];

        // Handle image upload (e.g. required for resolution)
        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $extra['image_path'] = $this->uploader->upload($_FILES['image']);
            } catch (\Exception $e) {
                $this->redirect("/tickets/{$id}", null, $e->getMessage());
            }
        }

        try {
            $this->statusService->transition($ticket, $toStatus, Auth::user(), $extra);
            $this->redirect("/tickets/{$id}", "อัปเดตสถานะเป็น [{$toStatus->label()}] เรียบร้อยแล้ว");
        } catch (\DomainException $e) {
            $this->redirect("/tickets/{$id}", null, $e->getMessage());
        } catch (\Exception $e) {
            $this->redirect("/tickets/{$id}", null, 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Add comment / message to ticket
     */
    public function addComment(int $id): void
    {
        $ticket = $this->ticketRepo->find($id);
        if (!$ticket) {
            $this->redirect('/tickets', null, 'ไม่พบตั๋วงานซ่อม');
        }

        $body = trim($_POST['body'] ?? '');
        $imagePath = null;

        if (!empty($_FILES['image']['tmp_name'])) {
            try {
                $imagePath = $this->uploader->upload($_FILES['image']);
            } catch (\Exception $e) {
                $this->redirect("/tickets/{$id}", null, $e->getMessage());
            }
        }

        if ($body === '' && !$imagePath) {
            $this->redirect("/tickets/{$id}", null, 'กรุณาพิมพ์ข้อความหรือแนบรูปภาพ');
        }

        $this->commentRepo->create([
            'ticket_id'  => $id,
            'user_id'    => Auth::id(),
            'body'       => $body ?: 'แนบรูปภาพเพิ่มเติม',
            'image_path' => $imagePath,
        ]);

        $this->redirect("/tickets/{$id}", 'ส่งข้อความเรียบร้อยแล้ว');
    }
}
