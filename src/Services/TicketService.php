<?php

namespace App\Services;

use App\Core\Database;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;
use App\Repositories\TicketRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\CommentRepository;
use App\Repositories\RatingRepository;
use InvalidArgumentException;
use Exception;

class TicketService
{
    private TicketRepository $ticketRepo;
    private StatusLogRepository $statusLogRepo;
    private CommentRepository $commentRepo;
    private RatingRepository $ratingRepo;
    private FileUploader $uploader;
    private Database $db;

    public function __construct(
        ?TicketRepository $ticketRepo = null,
        ?StatusLogRepository $statusLogRepo = null,
        ?CommentRepository $commentRepo = null,
        ?RatingRepository $ratingRepo = null,
        ?FileUploader $uploader = null,
        ?Database $db = null
    ) {
        $this->ticketRepo = $ticketRepo ?? new TicketRepository();
        $this->statusLogRepo = $statusLogRepo ?? new StatusLogRepository();
        $this->commentRepo = $commentRepo ?? new CommentRepository();
        $this->ratingRepo = $ratingRepo ?? new RatingRepository();
        $this->uploader = $uploader ?? new FileUploader();
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Create new repair ticket with optional image attachment
     */
    public function createTicket(array $input, int $userId, ?array $file = null): int
    {
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $categoryId = (int) ($input['category_id'] ?? 0);
        $priority = $input['priority'] ?? 'medium';

        if (empty($title)) {
            throw new InvalidArgumentException('กรุณาระบุหัวข้องานซ่อม');
        }

        if (empty($description)) {
            throw new InvalidArgumentException('กรุณาระบุรายละเอียดอาการของปัญหา');
        }

        if ($categoryId <= 0) {
            throw new InvalidArgumentException('กรุณาเลือกหมวดหมู่อุปกรณ์');
        }

        // Validate priority against Enum
        if (!TicketPriority::tryFrom($priority)) {
            $priority = TicketPriority::MEDIUM->value;
        }

        $imagePath = null;
        if ($file && isset($file['tmp_name']) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploader->upload($file);
        }

        $this->db->beginTransaction();
        try {
            // 1. Create ticket
            $ticketId = $this->ticketRepo->create([
                'user_id'       => $userId,
                'category_id'   => $categoryId,
                'technician_id' => null,
                'title'         => $title,
                'description'   => $description,
                'status'        => TicketStatus::OPEN->value,
                'priority'      => $priority,
            ]);

            // 2. Add initial comment with image if attached
            if ($imagePath) {
                $this->commentRepo->create(
                    $ticketId,
                    $userId,
                    "📷 รูปภาพประกอบอาการปัญหาเมื่อเปิดตั๋ว",
                    $imagePath
                );
            }

            // 3. Log initial status
            $this->statusLogRepo->log(
                $ticketId,
                $userId,
                'none',
                TicketStatus::OPEN->value,
                'สร้างตั๋วแจ้งซ่อมเข้าระบบ'
            );

            $this->db->commit();
            return $ticketId;
        } catch (Exception $e) {
            $this->db->rollBack();
            if ($imagePath) {
                $this->uploader->delete($imagePath);
            }
            throw $e;
        }
    }

    /**
     * Get full detail bundle for a ticket: ticket data, comments, status logs, rating
     */
    public function getTicketDetail(int $ticketId): ?array
    {
        $ticket = $this->ticketRepo->find($ticketId);
        if (!$ticket) {
            return null;
        }

        $ticket['comments'] = $this->commentRepo->findByTicketId($ticketId);
        $ticket['status_logs'] = $this->statusLogRepo->findByTicketId($ticketId);
        $ticket['rating'] = $this->ratingRepo->findByTicketId($ticketId);

        return $ticket;
    }

    /**
     * Get all categories for dropdown
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY id ASC");
        return $stmt->fetchAll();
    }
}
