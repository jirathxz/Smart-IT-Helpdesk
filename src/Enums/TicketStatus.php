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
            self::OPEN => 'รอดำเนินการ (Open)',
            self::ASSIGNED => 'มอบหมายช่างแล้ว (Assigned)',
            self::IN_PROGRESS => 'กำลังดำเนินการซ่อม (In Progress)',
            self::RESOLVED => 'ซ่อมเสร็จแล้ว (Resolved)',
            self::CLOSED => 'ตรวจรับและปิดงาน (Closed)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::OPEN => 'รอดำเนินการ',
            self::ASSIGNED => 'มอบหมายแล้ว',
            self::IN_PROGRESS => 'กำลังดำเนินการ',
            self::RESOLVED => 'รอตรวจรับ',
            self::CLOSED => 'ปิดงานแล้ว',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPEN => 'badge-warning',
            self::ASSIGNED => 'badge-info',
            self::IN_PROGRESS => 'badge-primary',
            self::RESOLVED => 'badge-purple',
            self::CLOSED => 'badge-success',
        };
    }

    public function colorHex(): string
    {
        return match ($this) {
            self::OPEN => '#f59e0b',
            self::ASSIGNED => '#0ea5e9',
            self::IN_PROGRESS => '#3b82f6',
            self::RESOLVED => '#a855f7',
            self::CLOSED => '#10b981',
        };
    }

    public function stepIndex(): int
    {
        return match ($this) {
            self::OPEN => 1,
            self::ASSIGNED => 2,
            self::IN_PROGRESS => 3,
            self::RESOLVED => 4,
            self::CLOSED => 5,
        };
    }
}
