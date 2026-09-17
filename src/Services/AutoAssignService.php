<?php

namespace App\Services;

use App\Core\Auth;
use App\Core\Database;
use App\Core\EventDispatcher;
use App\Repositories\UserRepository;

/**
 * Smart Auto-Assignment Service
 * Distributes tickets to technicians based on category specialization,
 * current active workload balancing, overload avoidance, and customer satisfaction (CSAT).
 */
class AutoAssignService
{
    private Database $db;
    private UserRepository $userRepo;

    public function __construct(?Database $db = null, ?UserRepository $userRepo = null)
    {
        $this->db = $db ?? Database::getInstance();
        $this->userRepo = $userRepo ?? new UserRepository($this->db);
    }

    /**
     * Determine the best technician for a given ticket using weighted scoring.
     *
     * @param int $ticketId
     * @return array [
     *   'technician_id' => int,
     *   'technician_name' => string,
     *   'technician' => array,
     *   'score' => float,
     *   'reason' => string,
     *   'factors' => array,
     *   'candidates' => array
     * ]
     * @throws \RuntimeException if no technicians exist
     */
    public function findBestTechnician(int $ticketId): array
    {
        $ticket = $this->db->fetch(
            "SELECT t.*, c.name as category_name 
             FROM tickets t 
             JOIN categories c ON t.category_id = c.id 
             WHERE t.id = :id",
            ['id' => $ticketId]
        );

        if (!$ticket) {
            throw new \RuntimeException("ไม่พบตั๋วหมายเลข #{$ticketId}");
        }

        $technicians = $this->userRepo->findTechnicians();
        if (empty($technicians)) {
            throw new \RuntimeException("ไม่พบช่างเทคนิคในระบบสำหรับจ่ายงาน");
        }

        $candidates = [];

        foreach ($technicians as $tech) {
            $techId = (int) $tech['id'];

            // 1. Current active workload
            $workloadRow = $this->db->fetch(
                "SELECT 
                    COUNT(*) as active_jobs,
                    SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_jobs
                 FROM tickets 
                 WHERE technician_id = :tech_id 
                   AND status IN ('open', 'assigned', 'in_progress')",
                ['tech_id' => $techId]
            );
            $activeJobs = (int) ($workloadRow['active_jobs'] ?? 0);
            $urgentJobs = (int) ($workloadRow['urgent_jobs'] ?? 0);

            // 2. Category specialization: how many tickets has this tech resolved in this category?
            $catRow = $this->db->fetch(
                "SELECT COUNT(*) as resolved_in_category 
                 FROM tickets 
                 WHERE technician_id = :tech_id 
                   AND category_id = :cat_id 
                   AND status IN ('resolved', 'closed')",
                ['tech_id' => $techId, 'cat_id' => $ticket['category_id']]
            );
            $resolvedInCategory = (int) ($catRow['resolved_in_category'] ?? 0);

            // 3. Average CSAT rating
            $ratingRow = $this->db->fetch(
                "SELECT AVG(r.score) as avg_score, COUNT(r.id) as rating_count 
                 FROM ratings r 
                 JOIN tickets t ON r.ticket_id = t.id 
                 WHERE t.technician_id = :tech_id",
                ['tech_id' => $techId]
            );
            $avgScore = !empty($ratingRow['avg_score']) ? (float) $ratingRow['avg_score'] : 4.0; // neutral baseline

            // 4. Overload detection
            // A technician is considered overloaded if they have >= 5 active jobs or active urgent jobs
            $isOverloaded = ($activeJobs >= 5 || $urgentJobs > 0);

            // 5. Scoring Algorithm
            // Base score: fewer active jobs = higher score
            $score = 100 - ($activeJobs * 15);

            // Bonus for category experience
            $categoryBonus = min(30, $resolvedInCategory * 10);
            $score += $categoryBonus;

            // CSAT bonus
            $score += ($avgScore * 5);

            // Penalty if overloaded
            $overloadPenalty = 0;
            if ($isOverloaded) {
                $overloadPenalty = 50 + ($activeJobs * 5);
                $score -= $overloadPenalty;
            }

            $candidates[] = [
                'id'                   => $techId,
                'name'                 => $tech['name'],
                'email'                => $tech['email'],
                'line_user_id'         => $tech['line_user_id'],
                'active_jobs'          => $activeJobs,
                'urgent_jobs'          => $urgentJobs,
                'resolved_in_category' => $resolvedInCategory,
                'avg_score'            => $avgScore,
                'is_overloaded'        => $isOverloaded,
                'score'                => $score,
            ];
        }

        // Sort descending by score; if tied, sort by fewer active jobs
        usort($candidates, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return $a['active_jobs'] <=> $b['active_jobs'];
            }
            return ($b['score'] <=> $a['score']);
        });

        $best = $candidates[0];

        // Compose detailed rationale in Thai
        $reasons = [];
        if ($best['active_jobs'] === 0) {
            $reasons[] = "ว่างพร้อมรับงานทันที (0 งานค้าง)";
        } else {
            $reasons[] = "ภาระงานปัจจุบันเหมาะสม ({$best['active_jobs']} งานค้าง)";
        }

        if ($best['resolved_in_category'] > 0) {
            $reasons[] = "เชี่ยวชาญหมวดหมู่ '{$ticket['category_name']}' (เคยปิดงานสำเร็จ {$best['resolved_in_category']} ครั้ง)";
        }

        if ($best['avg_score'] >= 4.5) {
            $reasons[] = "คะแนนประเมินความพึงพอใจสูง (" . number_format($best['avg_score'], 1) . "★)";
        }

        $reasonText = implode(', ', $reasons);

        return [
            'technician_id'   => $best['id'],
            'technician_name' => $best['name'],
            'technician'      => $best,
            'score'           => $best['score'],
            'reason'          => $reasonText,
            'candidates'      => $candidates,
        ];
    }

    /**
     * Execute auto-assignment for a single ticket.
     *
     * @param int $ticketId
     * @param int|null $adminId
     * @return array
     */
    public function assignTicket(int $ticketId, ?int $adminId = null): array
    {
        $adminId = $adminId ?? Auth::id() ?? 1;

        $decision = $this->findBestTechnician($ticketId);
        $techId = $decision['technician_id'];
        $techName = $decision['technician_name'];
        $reason = $decision['reason'];

        $ticket = $this->db->fetch("SELECT * FROM tickets WHERE id = :id", ['id' => $ticketId]);
        if (!$ticket) {
            throw new \RuntimeException("ไม่พบตั๋วหมายเลข #{$ticketId}");
        }

        if ($ticket['status'] !== 'open') {
            throw new \RuntimeException("ตั๋วหมายเลข #{$ticketId} ได้รับการจ่ายงานหรืออยู่ระหว่างดำเนินการแล้ว");
        }

        $this->db->beginTransaction();
        try {
            $this->db->query(
                "UPDATE tickets SET technician_id = :tech_id, status = 'assigned', updated_at = NOW() WHERE id = :id",
                ['tech_id' => $techId, 'id' => $ticketId]
            );

            $note = "ระบบจ่ายงานอัตโนมัติ (Smart Auto-Assign): มอบหมายให้ช่าง {$techName} [{$reason}]";

            $this->db->query(
                "INSERT INTO status_logs (ticket_id, changed_by, from_status, to_status, note, created_at) 
                 VALUES (:ticket_id, :changed_by, 'open', 'assigned', :note, NOW())",
                [
                    'ticket_id'   => $ticketId,
                    'changed_by'  => $adminId,
                    'note'        => $note,
                ]
            );

            $this->db->commit();

            // Dispatch event for LINE notification
            $ticket['technician_id'] = $techId;
            $ticket['technician_name'] = $techName;
            $ticket['tech_line_id'] = $decision['technician']['line_user_id'];
            EventDispatcher::getInstance()->dispatch('ticket.assigned', $ticket);

            return [
                'success'         => true,
                'ticket_id'       => $ticketId,
                'technician_id'   => $techId,
                'technician_name' => $techName,
                'reason'          => $reason,
                'note'            => $note,
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Batch auto-assign all open tickets, ordered by priority (urgent -> high -> medium -> low).
     * Re-evaluates workload after each assignment to ensure balanced distribution.
     *
     * @param int|null $adminId
     * @return array
     */
    public function assignAllOpenTickets(?int $adminId = null): array
    {
        $openTickets = $this->db->fetchAll(
            "SELECT id, title, priority, category_id 
             FROM tickets 
             WHERE status = 'open' 
             ORDER BY CASE priority 
                 WHEN 'urgent' THEN 1 
                 WHEN 'high' THEN 2 
                 WHEN 'medium' THEN 3 
                 ELSE 4 
             END, created_at ASC"
        );

        if (empty($openTickets)) {
            return [
                'total'        => 0,
                'assigned'     => 0,
                'assignments'  => [],
                'summary_text' => 'ไม่มีตั๋วงานสถานะ Open ที่รอจ่ายงานในระบบ',
            ];
        }

        $assignments = [];
        $techCounts = [];

        foreach ($openTickets as $ticket) {
            try {
                $result = $this->assignTicket((int) $ticket['id'], $adminId);
                $assignments[] = $result;
                $tName = $result['technician_name'];
                $techCounts[$tName] = ($techCounts[$tName] ?? 0) + 1;
            } catch (\Exception $e) {
                // If a single ticket fails, continue with others
                continue;
            }
        }

        $breakdownParts = [];
        foreach ($techCounts as $name => $count) {
            $breakdownParts[] = "{$name} {$count} งาน";
        }

        $summaryText = "จ่ายงานอัตโนมัติสำเร็จทั้งหมด " . count($assignments) . " รายการ";
        if (!empty($breakdownParts)) {
            $summaryText .= " (" . implode(', ', $breakdownParts) . ")";
        }

        return [
            'total'        => count($openTickets),
            'assigned'     => count($assignments),
            'assignments'  => $assignments,
            'summary_text' => $summaryText,
        ];
    }
}
