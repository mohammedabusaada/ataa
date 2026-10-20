<?php

namespace Database\Factories;

use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Governorate>
 */
class GovernorateFactory extends Factory
{
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 99999);

        return [
            'name_ar' => 'محافظة '.$number,
            'name_en' => 'Governorate '.$number,
        ];
    }
}
