<?php

namespace App\Models;

use App\Enums\HouseholdStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdStatusChange extends Model
{
    // History rows are never edited, so the table has no updated_at column.
    const UPDATED_AT = null;

    protected $fillable = [
        'from_status',
        'to_status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => HouseholdStatus::class,
            'to_status' => HouseholdStatus::class,
        ];
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
