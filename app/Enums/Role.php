<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case FieldWorker = 'field_worker';
    case DistributionOfficer = 'distribution_officer';
    case Auditor = 'auditor';
    
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::FieldWorker => 'Field Worker',
            self::DistributionOfficer => 'Distribution Officer',
            self::Auditor => 'Auditor',
        };
    }
}
