<?php

namespace App\Models;

use App\Enums\AllocationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = [
        'quantity',
        'received_by_name',
        'received_by_national_id',
    ];

    protected $attributes = [
        'status' => AllocationStatus::Scheduled->value,
    ];

    protected function casts(): array
    {
        return [
            'collected_at' => 'datetime',
            'status' => AllocationStatus::class,
        ];
    }

    public function distributionRound(): BelongsTo
    {
        return $this->belongsTo(DistributionRound::class);
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
