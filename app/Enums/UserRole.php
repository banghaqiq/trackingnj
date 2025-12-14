<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PETUGAS_POS = 'petugas_pos';
    case KEAMANAN = 'keamanan';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::PETUGAS_POS => 'Petugas Pos',
            self::KEAMANAN => 'Keamanan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
