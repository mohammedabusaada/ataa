<?php

namespace App\Models;

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

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
