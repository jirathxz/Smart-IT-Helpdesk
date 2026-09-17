<?php

namespace App\Services;

use App\Core\Database;
use App\Core\EventDispatcher;
use App\Enums\TicketStatus;
use App\Repositories\CommentRepository;
use App\Repositories\RatingRepository;
use App\Repositories\StatusLogRepository;
use App\Repositories\TicketRepository;

/**
 * Ticket State Machine & Status Transition Service
 */
class TicketStatusService
{
    private Database $db;
    private TicketRepository $ticketRepo;
    private StatusLogRepository $statusLogRepo;
    private CommentRepository $commentRepo;
    private RatingRepository $ratingRepo;
    private EventDispatcher $events;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ticketRepo = new TicketRepository($this->db);
        $this->statusLogRepo = new StatusLogRepository($this->db);
        $this->commentRepo = new CommentRepository($this->db);
        $this->ratingRepo = new RatingRepository($this->db);
        $this->events = EventDispatcher::getInstance();
    }

    /**
     * Execute a state transition according to Transition Rules Matrix
     *
     * @param array $ticket Current ticket record
     * @param TicketStatus $to Target status enum
     * @param array $actor Current authenticated user
     * @param array $extra Additional data (note, image_path, score, feedback, technician_id)
     * @return bool
     * @throws \DomainException
     */
    public function transition(array $ticket, TicketStatus $to, array $actor, array $extra = []): bool
    {
        $from = TicketStatus::from($ticket['status']);

        // 1. Validate if transition is logically permitted in state graph
        $this->validateTransition($from, $to);

        // 2. Validate Role-Based permissions & required actions
        $this->validateActorAndRequirements($ticket, $from, $to, $actor, $extra);

        // 3. Perform database updates in a single atomic transaction
        $this->db->beginTransaction();
        try {
            $ticketId = (int) $ticket['id'];
            $updateData = ['status' => $to->value];

            if ($to === TicketStatus::ASSIGNED && !empty($extra['technician_id'])) {
                $updateData['technician_id'] = (int) $extra['technician_id'];
            }

            if ($to === TicketStatus::RESOLVED) {
                $updateData['resolved_at'] = date('Y-m-d H:i:s');
            }

            if ($to === TicketStatus::CLOSED) {
                $updateData['closed_at'] = date('Y-m-d H:i:s');
            }

            // If reverting from resolved back to in_progress, clear resolved_at
            if ($from === TicketStatus::RESOLVED && $to === TicketStatus::IN_PROGRESS) {
                $updateData['resolved_at'] = null;
            }

            // Update ticket
            $this->ticketRepo->update($ticketId, $updateData);

            // Create status audit log
            $logNote = $extra['note'] ?? $this->getDefaultNote($from, $to, $actor);
            $this->statusLogRepo->create([
                'ticket_id'   => $ticketId,
                'changed_by'  => $actor['id'],
                'from_status' => $from->value,
                'to_status'   => $to->value,
                'note'        => $logNote,
            ]);

            // Add comment if resolution or rejection note / photo provided
            if (!empty($extra['note']) || !empty($extra['image_path'])) {
                $this->commentRepo->create([
                    'ticket_id'  => $ticketId,
                    'user_id'    => $actor['id'],
                    'body'       => $extra['note'] ?: "เปลี่ยนสถานะเป็น " . $to->label(),
                    'image_path' => $extra['image_path'] ?? null,
                ]);
            }

            // If closing with rating, save rating record
            if ($to === TicketStatus::CLOSED && !empty($extra['score'])) {
                $this->ratingRepo->create([
                    'ticket_id' => $ticketId,
                    'score'     => (int) $extra['score'],
                    'feedback'  => $extra['feedback'] ?? null,
                ]);
            }

            $this->db->commit();

            // 4. Dispatch status changed event to observers (triggers LINE notification)
            $freshTicket = $this->ticketRepo->find($ticketId);
            $this->events->dispatch('ticket.status_changed', [
                'ticket' => $freshTicket,
                'from'   => $from->value,
                'to'     => $to->value,
                'actor'  => $actor,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * State Machine transition validation
     */
    private function validateTransition(TicketStatus $from, TicketStatus $to): void
    {
        $allowed = match ($from) {
            TicketStatus::OPEN        => [TicketStatus::ASSIGNED],
            TicketStatus::ASSIGNED    => [TicketStatus::IN_PROGRESS],
            TicketStatus::IN_PROGRESS => [TicketStatus::RESOLVED],
            TicketStatus::RESOLVED    => [TicketStatus::CLOSED, TicketStatus::IN_PROGRESS],
            TicketStatus::CLOSED      => [], // Terminal state
        };

        if (!in_array($to, $allowed, true)) {
            throw new \DomainException(
                "ไม่อนุญาตให้เปลี่ยนสถานะจาก [{$from->label()}] ไปยัง [{$to->label()}] ตามกฎ State Machine"
            );
        }
    }

    /**
     * RBAC and action requirements validation
     */
    private function validateActorAndRequirements(
        array $ticket,
        TicketStatus $from,
        TicketStatus $to,
        array $actor,
        array $extra
    ): void {
        $role = $actor['role'] ?? 'user';
        $actorId = (int) ($actor['id'] ?? 0);
        $ticketOwnerId = (int) $ticket['user_id'];
        $assignedTechId = (int) ($ticket['technician_id'] ?? 0);

        // Open -> Assigned
        if ($from === TicketStatus::OPEN && $to === TicketStatus::ASSIGNED) {
            if ($role !== 'admin') {
                throw new \DomainException('เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถมอบหมายงานได้');
            }
            if (empty($extra['technician_id'])) {
                throw new \DomainException('ต้องระบุช่างเทคนิคที่ต้องการมอบหมาย');
            }
        }

        // Assigned -> In Progress
        if ($from === TicketStatus::ASSIGNED && $to === TicketStatus::IN_PROGRESS) {
            if ($role !== 'admin' && ($role !== 'technician' || $actorId !== $assignedTechId)) {
                throw new \DomainException('เฉพาะช่างที่ได้รับมอบหมายงานนี้ หรือผู้ดูแลระบบเท่านั้นที่สามารถเริ่มงานได้');
            }
        }

        // In Progress -> Resolved
        if ($from === TicketStatus::IN_PROGRESS && $to === TicketStatus::RESOLVED) {
            if ($role !== 'admin' && ($role !== 'technician' || $actorId !== $assignedTechId)) {
                throw new \DomainException('เฉพาะช่างเจ้าของงานเท่านั้นที่สามารถบันทึกเสร็จสิ้นงานได้');
            }
            if (empty($extra['image_path'])) {
                throw new \DomainException('จำเป็นต้องแนบรูปภาพผลการซ่อมเพื่อเป็นหลักฐานในการปิดงาน');
            }
        }

        // Resolved -> Closed
        if ($from === TicketStatus::RESOLVED && $to === TicketStatus::CLOSED) {
            if ($role !== 'admin' && $actorId !== $ticketOwnerId) {
                throw new \DomainException('เฉพาะผู้แจ้งซ่อม (เจ้าของตั๋ว) เท่านั้นที่มีสิทธิ์ตรวจรับและปิดงาน');
            }
            if (empty($extra['score']) || (int)$extra['score'] < 1 || (int)$extra['score'] > 5) {
                throw new \DomainException('กรุณาประเมินความพึงพอใจ 1 - 5 ดาว');
            }
        }

        // Resolved -> In Progress (User rejects resolution)
        if ($from === TicketStatus::RESOLVED && $to === TicketStatus::IN_PROGRESS) {
            if ($role !== 'admin' && $actorId !== $ticketOwnerId) {
                throw new \DomainException('เฉพาะผู้แจ้งซ่อมเท่านั้นที่สามารถส่งงานกลับแก้ไขได้');
            }
            if (empty($extra['note']) || trim($extra['note']) === '') {
                throw new \DomainException('กรุณาระบุเหตุผลการส่งกลับแก้ไขเพื่อให้ช่างดำเนินการต่อ');
            }
        }
    }

    private function getDefaultNote(TicketStatus $from, TicketStatus $to, array $actor): string
    {
        return match ($to) {
            TicketStatus::ASSIGNED    => "มอบหมายงานให้ช่างเรียบร้อยแล้ว",
            TicketStatus::IN_PROGRESS => "ช่าง {$actor['name']} รับงานและเริ่มดำเนินการตรวจสอบ",
            TicketStatus::RESOLVED    => "ช่าง {$actor['name']} ดำเนินการซ่อมเสร็จสิ้น",
            TicketStatus::CLOSED      => "ผู้ใช้ตรวจรับงานและประเมินผลเสร็จสมบูรณ์",
            default                   => "เปลี่ยนสถานะเป็น " . $to->label()
        };
    }
}
