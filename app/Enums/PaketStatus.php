<?php

namespace App\Enums;

enum PaketStatus: string
{
    case BELUM_DIAMBIL = 'belum_diambil';
    case DIAMBIL = 'diambil';
    case DITERIMA = 'diterima';
    case SALAH_WILAYAH = 'salah_wilayah';
    case SELESAI = 'selesai';
    case DIKEMBALIKAN = 'dikembalikan';

    public function label(): string
    {
        return match($this) {
            self::BELUM_DIAMBIL => 'Belum Diambil',
            self::DIAMBIL => 'Diambil',
            self::DITERIMA => 'Diterima',
            self::SALAH_WILAYAH => 'Salah Wilayah',
            self::SELESAI => 'Selesai',
            self::DIKEMBALIKAN => 'Dikembalikan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
