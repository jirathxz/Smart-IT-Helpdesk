<?php

namespace App\Services;

use App\Core\Database;
use App\Enums\TicketStatus;
use App\Repositories\TicketRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\RatingRepository;
use App\Repositories\CommentRepository;
use Exception;
use InvalidArgumentException;

class TicketStatusService
{
    private TicketRepository $ticketRepo;
    private StatusLogRepository $statusLogRepo;
    private RatingRepository $ratingRepo;
    private CommentRepository $commentRepo;
    private Database $db;

    public function __construct(
        ?TicketRepository $ticketRepo = null,
        ?StatusLogRepository $statusLogRepo = null,
        ?RatingRepository $ratingRepo = null,
        ?CommentRepository $commentRepo = null,
        ?Database $db = null
    ) {
        $this->ticketRepo = $ticketRepo ?? new TicketRepository();
        $this->statusLogRepo = $statusLogRepo ?? new StatusLogRepository();
        $this->ratingRepo = $ratingRepo ?? new RatingRepository();
        $this->commentRepo = $commentRepo ?? new CommentRepository();
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Transition ticket state following strict State Machine rules
     *
     * @param int $ticketId
     * @param string $toStatus
     * @param array $actor Current logged-in user ['id', 'role', 'name']
     * @param array $payload Extra params: 'note', 'technician_id', 'image_path', 'score', 'feedback'
     * @return bool
     * @throws Exception
     */
    public function transition(int $ticketId, string $toStatus, array $actor, array $payload = []): bool
    {
        $ticket = $this->ticketRepo->find($ticketId);
        if (!$ticket) {
            throw new InvalidArgumentException("ไม่พบตั๋วแจ้งซ่อมหมายเลข #{$ticketId}");
        }

        $fromStatus = $ticket['status'];
        $actorId = (int) $actor['id'];
        $actorRole = $actor['role'] ?? 'user';

        // 1. Validate State Transition
        $this->validateTransition($fromStatus, $toStatus);

        // 2. Check Role & Ownership Permissions
        $this->checkPermission($ticket, $toStatus, $actor);

        // 3. Process Transition inside DB Transaction
        $this->db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            $note = trim($payload['note'] ?? '');

            switch ($toStatus) {
                case TicketStatus::ASSIGNED->value:
                    $techId = (int) ($payload['technician_id'] ?? 0);
                    if ($techId <= 0) {
                        throw new InvalidArgumentException('กรุณาระบุช่างผู้รับผิดชอบ');
                    }
                    $this->ticketRepo->assignTechnician($ticketId, $techId);
                    $note = $note ?: "ผู้ดูแลระบบมอบหมายงานให้ช่างรหัส #{$techId}";
                    break;

                case TicketStatus::IN_PROGRESS->value:
                    if ($fromStatus === TicketStatus::RESOLVED->value) {
                        // Rework flow: User rejected repair
                        if (empty($note)) {
                            throw new InvalidArgumentException('กรุณาระบุเหตุผลในการส่งกลับแก้ไข');
                        }
                        $this->ticketRepo->updateStatus($ticketId, $toStatus, null, null);
                        $this->commentRepo->create(
                            $ticketId,
                            $actorId,
                            "⚠️ ผู้แจ้งขอส่งกลับแก้ไข: " . $note
                        );
                    } else {
                        // Normal tech start work
                        $this->ticketRepo->updateStatus($ticketId, $toStatus);
                        $note = $note ?: 'ช่างกดยืนยันรับงานและเริ่มดำเนินการตรวจสอบ';
                    }
                    break;

                case TicketStatus::RESOLVED->value:
                    if (empty($note)) {
                        throw new InvalidArgumentException('กรุณาระบุสรุปรายละเอียดการแก้ไขปัญหา');
                    }
                    $this->ticketRepo->updateStatus($ticketId, $toStatus, $now, null);

                    $imagePath = $payload['image_path'] ?? null;
                    $this->commentRepo->create(
                        $ticketId,
                        $actorId,
                        "🔧 สรุปผลการซ่อม: " . $note,
                        $imagePath
                    );
                    break;

                case TicketStatus::CLOSED->value:
                    $score = (int) ($payload['score'] ?? 0);
                    if ($score < 1 || $score > 5) {
                        throw new InvalidArgumentException('กรุณาให้คะแนนประเมินความพึงพอใจ 1 - 5 ดาว');
                    }
                    $feedback = trim($payload['feedback'] ?? '');

                    $this->ticketRepo->updateStatus($ticketId, $toStatus, null, $now);
                    $this->ratingRepo->create($ticketId, $score, $feedback);

                    $stars = str_repeat('★', $score) . str_repeat('☆', 5 - $score);
                    $this->commentRepo->create(
                        $ticketId,
                        $actorId,
                        "⭐ ผู้แจ้งตรวจรับงานเรียบร้อย ({$stars} {$score}/5)\nข้อคิดเห็น: " . ($feedback ?: 'ไม่มี')
                    );
                    $note = $note ?: "ตรวจรับงานและประเมิน {$score} ดาว";
                    break;

                default:
                    throw new InvalidArgumentException("สถานะเป้าหมาย '{$toStatus}' ไม่ถูกต้อง");
            }

            // Write to Audit Trail (status_logs)
            $this->statusLogRepo->log(
                $ticketId,
                $actorId,
                $fromStatus,
                $toStatus,
                $note
            );

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Validate allowable state transitions
     */
    private function validateTransition(string $from, string $to): void
    {
        $validTransitions = [
            TicketStatus::OPEN->value => [TicketStatus::ASSIGNED->value],
            TicketStatus::ASSIGNED->value => [TicketStatus::IN_PROGRESS->value],
            TicketStatus::IN_PROGRESS->value => [TicketStatus::RESOLVED->value],
            TicketStatus::RESOLVED->value => [TicketStatus::CLOSED->value, TicketStatus::IN_PROGRESS->value],
            TicketStatus::CLOSED->value => [], // Terminal state
        ];

        $allowed = $validTransitions[$from] ?? [];
        if (!in_array($to, $allowed, true)) {
            throw new InvalidArgumentException(
                "ไม่สามารถเปลี่ยนสถานะจาก '" . TicketStatus::from($from)->label() . "' ไปเป็น '" . TicketStatus::from($to)->label() . "' ได้ (ผิดขั้นตอน State Machine)"
            );
        }
    }

    /**
     * Enforce strict Role-Based Access Control (RBAC) & Ownership
     */
    private function checkPermission(array $ticket, string $toStatus, array $actor): void
    {
        $role = $actor['role'] ?? 'user';
        $actorId = (int) $actor['id'];
        $ownerId = (int) $ticket['user_id'];
        $assignedTechId = (int) ($ticket['technician_id'] ?? 0);

        // Admin has super-privileges to assign technician
        if ($role === 'admin') {
            return;
        }

        switch ($toStatus) {
            case TicketStatus::ASSIGNED->value:
                if ($role !== 'admin') {
                    throw new Exception('เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถมอบหมายงานให้ช่างได้');
                }
                break;

            case TicketStatus::IN_PROGRESS->value:
                if ($ticket['status'] === TicketStatus::RESOLVED->value) {
                    // Reopening ticket: only ticket owner can request rework
                    if ($actorId !== $ownerId) {
                        throw new Exception('เฉพาะเจ้าของตั๋วแจ้งซ่อมเท่านั้นที่สามารถส่งงานกลับแก้ไขได้');
                    }
                } else {
                    // Accepting job: must be technician and assigned technician
                    if ($role !== 'technician') {
                        throw new Exception('เฉพาะเจ้าหน้าที่ช่างเท่านั้นที่สามารถกดรับงานซ่อมได้');
                    }
                    if ($assignedTechId !== 0 && $assignedTechId !== $actorId) {
                        throw new Exception('คุณไม่ใช่ช่างที่ได้รับมอบหมายงานนี้');
                    }
                }
                break;

            case TicketStatus::RESOLVED->value:
                if ($role !== 'technician') {
                    throw new Exception('เฉพาะช่างผู้รับผิดชอบงานเท่านั้นที่สามารถกดปิดงานซ่อมได้');
                }
                if ($assignedTechId !== 0 && $assignedTechId !== $actorId) {
                    throw new Exception('คุณไม่ใช่ช่างที่ได้รับมอบหมายงานนี้');
                }
                break;

            case TicketStatus::CLOSED->value:
                if ($actorId !== $ownerId) {
                    throw new Exception('เฉพาะผู้แจ้งซ่อม (Ticket Owner) เท่านั้นที่สามารถตรวจรับงานและให้คะแนนได้');
                }
                break;
        }
    }
}
