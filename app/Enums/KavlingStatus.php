<?php

namespace App\Enums;

enum KavlingStatus: string
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Nonaktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AKTIF => 'success',
            self::NONAKTIF => 'gray',
        };
    }
}
