<?php

namespace App\Enums;

enum PeralatanKondisi: string
{
    case BAIK = 'baik';
    case PERLU_PERBAIKAN = 'perlu_perbaikan';
    case RUSAK = 'rusak';

    public function label(): string
    {
        return match ($this) {
            self::BAIK => 'Baik',
            self::PERLU_PERBAIKAN => 'Perlu Perbaikan',
            self::RUSAK => 'Rusak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BAIK => 'success',
            self::PERLU_PERBAIKAN => 'warning',
            self::RUSAK => 'error',
        };
    }
}
