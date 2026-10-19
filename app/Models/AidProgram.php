<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AidProgram extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'unit',
        'stock_quantity',
        'starts_on',
        'ends_on',
        'min_family_size',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targetAreas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class);
    }

    public function requiredFlags(): BelongsToMany
    {
        return $this->belongsToMany(VulnerabilityFlag::class);
    }

    public function distributionRounds(): HasMany
    {
        return $this->hasMany(DistributionRound::class);
    }

    public function allocations(): HasManyThrough
    {
        return $this->hasManyThrough(Allocation::class, DistributionRound::class);
    }
}
