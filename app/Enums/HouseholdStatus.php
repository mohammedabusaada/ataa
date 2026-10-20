<?php

namespace App\Enums;

enum HouseholdStatus: string
{
    case Registered = 'registered';
    case UnderVerification = 'under_verification';
    case Eligible = 'eligible';
    case NotEligible = 'not_eligible';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registered',
            self::UnderVerification => 'Under verification',
            self::Eligible => 'Eligible',
            self::NotEligible => 'Not eligible',
        };
    }
}
