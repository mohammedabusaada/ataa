<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributionRound extends Model
{
    protected $fillable = [
        'round_number',
        'scheduled_on',
        'location',
        'quantity_per_household',
    ];

    protected $attributes = [
        'status' => 'planned',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_on' => 'date',
        ];
    }

    public function aidProgram(): BelongsTo
    {
        return $this->belongsTo(AidProgram::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }
}
