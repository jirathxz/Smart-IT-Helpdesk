<?php

namespace App\Observers;

use App\Notifications\NotificationChannelInterface;
use App\Notifications\LineMessagingService;

/**
 * Ticket Event Observer for Sending Notifications
 */
class TicketObserver
{
    private NotificationChannelInterface $notifier;

    public function __construct(?NotificationChannelInterface $notifier = null)
    {
        $this->notifier = $notifier ?? new LineMessagingService();
    }

    /**
     * Handle new ticket created event
     */
    public function handleCreated(array $ticket): void
    {
        $priority = strtoupper($ticket['priority'] ?? 'MEDIUM');
        $msg = "📢 [Smart IT Helpdesk] มีงานแจ้งซ่อมใหม่!\n"
             . "🎫 รหัสตั๋ว: #{$ticket['id']}\n"
             . "📌 หัวข้อ: {$ticket['title']}\n"
             . "⚡ ความเร่งด่วน: {$priority}\n"
             . "👤 ผู้แจ้ง: " . ($ticket['user_name'] ?? 'ผู้ใช้งาน') . "\n"
             . "🔗 ดูรายละเอียด: http://localhost:8090/tickets/{$ticket['id']}";

        // Notify IT Team / Admin group
        $this->notifier->send('', $msg);
    }

    /**
     * Handle ticket status transition event
     */
    public function handleStatusChanged(array $ticket, string $fromStatus, string $toStatus): void
    {
        $targetUserId = '';
        $recipientName = '';

        if ($toStatus === 'assigned') {
            // Notify Assigned Technician
            $targetUserId = $ticket['tech_line_id'] ?? '';
            $recipientName = $ticket['technician_name'] ?? 'ช่างเทคนิค';
            $actionMsg = "คุณได้รับมอบหมายงานซ่อมใหม่";
        } elseif ($toStatus === 'resolved') {
            // Notify Ticket Owner
            $targetUserId = $ticket['user_line_id'] ?? '';
            $recipientName = $ticket['user_name'] ?? 'ผู้แจ้งซ่อม';
            $actionMsg = "งานซ่อมของท่านเสร็จสิ้นแล้ว กรุณาเข้าตรวจรับและให้คะแนน";
        } else {
            $actionMsg = "สถานะเปลี่ยนจาก [{$fromStatus}] เป็น [{$toStatus}]";
        }

        $msg = "🔔 [Smart IT Helpdesk] อัปเดตสถานะงานซ่อม\n"
             . "เรียน: {$recipientName}\n"
             . "🎫 รหัสตั๋ว: #{$ticket['id']} - {$ticket['title']}\n"
             . "สถานะใหม่: " . strtoupper($toStatus) . "\n"
             . "รายละเอียด: {$actionMsg}\n"
             . "🔗 ลิงก์: http://localhost:8090/tickets/{$ticket['id']}";

        $this->notifier->send($targetUserId, $msg);
    }
}
