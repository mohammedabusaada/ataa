<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }
}
