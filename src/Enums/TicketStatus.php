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
            self::OPEN => 'bg-sky-50 text-sky-700 border-sky-200',
            self::ASSIGNED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::IN_PROGRESS => 'bg-amber-50 text-amber-700 border-amber-200',
            self::RESOLVED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::CLOSED => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
