<?php

namespace App\Enums;

enum PaketStatus: string
{
    case DITERIMA = 'diterima';
    case DIPROSES = 'diproses';
    case DIANTAR = 'diantar';
    case SELESAI = 'selesai';
    case DIKEMBALIKAN = 'dikembalikan';

    public function label(): string
    {
        return match($this) {
            self::DITERIMA => 'Diterima',
            self::DIPROSES => 'Diproses',
            self::DIANTAR => 'Diantar',
            self::SELESAI => 'Selesai',
            self::DIKEMBALIKAN => 'Dikembalikan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
