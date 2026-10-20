<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic demo data.
     */
    public function run(): void
    {
        $this->call([
            GeographySeeder::class,
            VulnerabilityFlagSeeder::class,
            UserSeeder::class,
            HouseholdSeeder::class,
            AidProgramSeeder::class,
        ]);
    }
}
