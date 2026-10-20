<?php

namespace App\Enums;

enum AllocationStatus: string
{
    case Scheduled = 'scheduled';
    case Collected = 'collected';
    case NoShow = 'no_show';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Scheduled',
            self::Collected => 'Collected',
            self::NoShow => 'No show',
            self::Cancelled => 'Cancelled',
        };
    }
}
