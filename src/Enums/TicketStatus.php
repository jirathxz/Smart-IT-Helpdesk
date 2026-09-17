<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'open';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'เปิดใหม่ (Open)',
            self::ASSIGNED => 'มอบหมายแล้ว (Assigned)',
            self::IN_PROGRESS => 'กำลังดำเนินการ (In Progress)',
            self::RESOLVED => 'รอตรวจรับงาน (Resolved)',
            self::CLOSED => 'ปิดงานเสร็จสิ้น (Closed)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::OPEN => 'bg-sky-500/15 text-sky-400 border-sky-500/30',
            self::ASSIGNED => 'bg-purple-500/15 text-purple-400 border-purple-500/30',
            self::IN_PROGRESS => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
            self::RESOLVED => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
            self::CLOSED => 'bg-slate-500/15 text-slate-400 border-slate-500/30',
        };
    }
}
