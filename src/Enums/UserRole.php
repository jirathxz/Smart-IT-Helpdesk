<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case TECHNICIAN = 'technician';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'ผู้ใช้งานทั่วไป (User)',
            self::TECHNICIAN => 'ช่างเทคนิค (Technician)',
            self::ADMIN => 'ผู้ดูแลระบบ (Admin)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::USER => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            self::TECHNICIAN => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
            self::ADMIN => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
        };
    }
}
