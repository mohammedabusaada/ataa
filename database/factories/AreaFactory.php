<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Area>
 */
class AreaFactory extends Factory
{
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 99999);

        return [
            'governorate_id' => Governorate::factory(),
            'name_ar' => 'منطقة '.$number,
            'name_en' => 'Area '.$number,
        ];
    }
}
