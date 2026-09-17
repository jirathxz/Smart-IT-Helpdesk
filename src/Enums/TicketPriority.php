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
            self::LOW => 'ความสำคัญต่ำ (Low)',
            self::MEDIUM => 'ปานกลาง (Medium)',
            self::HIGH => 'สูง (High)',
            self::URGENT => 'ด่วนที่สุด (Urgent)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::LOW => 'ต่ำ',
            self::MEDIUM => 'ปกติ',
            self::HIGH => 'สูง',
            self::URGENT => 'เร่งด่วน',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::LOW => '#94a3b8',
            self::MEDIUM => '#3b82f6',
            self::HIGH => '#f97316',
            self::URGENT => '#ef4444',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW => 'badge-priority-low',
            self::MEDIUM => 'badge-priority-medium',
            self::HIGH => 'badge-priority-high',
            self::URGENT => 'badge-priority-urgent',
        };
    }

    public function colorHex(): string
    {
        return match ($this) {
            self::LOW => '#64748b',
            self::MEDIUM => '#3b82f6',
            self::HIGH => '#f97316',
            self::URGENT => '#ef4444',
        };
    }
}
