<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\HouseholdStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Household extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'national_id',
        'head_name',
        'head_gender',
        'head_birth_date',
        'phone',
        'email',
        'area_id',
        'address',
        'family_size',
    ];

    protected $attributes = [
        'status' => HouseholdStatus::Registered->value,
    ];

    protected function casts(): array
    {
        return [
            'head_gender' => Gender::class,
            'head_birth_date' => 'date',
            'status' => HouseholdStatus::class,
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(HouseholdDocument::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(HouseholdStatusChange::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function vulnerabilityFlags(): BelongsToMany
    {
        return $this->belongsToMany(VulnerabilityFlag::class);
    }
}
