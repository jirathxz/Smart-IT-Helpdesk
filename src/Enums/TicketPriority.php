<?php

namespace App\Enums;

enum TicketPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'ต่ำ (Low)',
            self::MEDIUM => 'ปานกลาง (Medium)',
            self::HIGH => 'สูง (High)',
            self::URGENT => 'เร่งด่วนที่สุด (Urgent)',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::LOW => 'bg-slate-500/20 text-slate-300 border-slate-500/30',
            self::MEDIUM => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
            self::HIGH => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
            self::URGENT => 'bg-rose-500/20 text-rose-300 border-rose-500/30 animate-pulse',
        };
    }
}
