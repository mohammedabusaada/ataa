<?php

namespace App\Models;

use App\Enums\HouseholdStatus;
use App\Enums\ProgramStatus;
use Illuminate\Database\Eloquent\Builder;
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
        'status' => ProgramStatus::Draft->value,
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'status' => ProgramStatus::class,
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

    /**
     * Households that meet this program's eligibility rules. An empty rule
     * (no minimum size, no target areas, no required flags) means no restriction.
     */
    public function eligibleHouseholds(): Builder
    {
        $areaIds = $this->targetAreas()->pluck('areas.id');
        $flagIds = $this->requiredFlags()->pluck('vulnerability_flags.id');

        return Household::query()
            ->where('status', HouseholdStatus::Eligible)
            ->when($this->min_family_size, fn (Builder $query, int $min) => $query->where('family_size', '>=', $min))
            ->when($areaIds->isNotEmpty(), fn (Builder $query) => $query->whereIn('area_id', $areaIds))
            ->when($flagIds->isNotEmpty(), fn (Builder $query) => $query->whereHas(
                'vulnerabilityFlags',
                fn (Builder $flags) => $flags->whereIn('vulnerability_flags.id', $flagIds),
            ));
    }
}
