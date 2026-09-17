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
            self::USER => 'bg-blue-50 text-blue-700 border-blue-200',
            self::TECHNICIAN => 'bg-amber-50 text-amber-700 border-amber-200',
            self::ADMIN => 'bg-purple-50 text-purple-700 border-purple-200',
        };
    }
}
