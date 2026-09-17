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
            self::LOW => 'bg-slate-100 text-slate-700 border-slate-200',
            self::MEDIUM => 'bg-blue-50 text-blue-700 border-blue-200',
            self::HIGH => 'bg-amber-50 text-amber-700 border-amber-200',
            self::URGENT => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
        };
    }
}
