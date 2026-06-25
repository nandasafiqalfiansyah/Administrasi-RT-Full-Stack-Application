<?php

namespace App\Enums;

enum ResidentStatus: string
{
    case TETAP = 'tetap';
    case KONTRAK = 'kontrak';

    public function label(): string
    {
        return match ($this) {
            self::TETAP => 'Tetap',
            self::KONTRAK => 'Kontrak',
        };
    }
}