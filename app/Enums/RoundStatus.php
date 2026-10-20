<?php

namespace App\Enums;

enum RoundStatus: string
{
    case Planned = 'planned';
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Planned',
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }
}
