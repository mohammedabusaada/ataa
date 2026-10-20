<?php

namespace Database\Seeders;

use App\Enums\AllocationStatus;
use App\Enums\Gender;
use App\Enums\ProgramStatus;
use App\Enums\Role;
use App\Enums\RoundStatus;
use App\Models\AidProgram;
use App\Models\Area;
use App\Models\DistributionRound;
use App\Models\User;
use App\Models\VulnerabilityFlag;
use Database\Factories\HouseholdFactory;
use Illuminate\Database\Seeder;

class AidProgramSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', Role::Admin)->firstOrFail();
        $officers = User::where('role', Role::DistributionOfficer)->get();

        // Food parcels for large families across the whole Strip: one past round, one upcoming.
        $food = $this->createProgram($admin, [
            'name_ar' => 'طرود غذائية',
            'name_en' => 'Food parcels',
            'type' => 'food',
            'unit' => 'parcel',
            'stock_quantity' => 1000,
            'starts_on' => now()->subDays(60)->toDateString(),
            'ends_on' => now()->addDays(60)->toDateString(),
            'min_family_size' => 5,
        ], areaNames: [], flagCodes: []);

        $this->createRound($food, 1, now()->subDays(35), 'مدرسة الوكالة - جباليا', 1, RoundStatus::Closed, $admin, $officers);
        $this->createRound($food, 2, now()->addDays(5), 'مركز التوزيع - دير البلح', 1, RoundStatus::Planned, $admin, $officers);

        // Cash assistance for vulnerable households in the south.
        $cash = $this->createProgram($admin, [
            'name_ar' => 'مساعدة نقدية طارئة',
            'name_en' => 'Emergency cash assistance',
            'type' => 'cash',
            'unit' => 'ILS',
            'stock_quantity' => 60000,
            'starts_on' => now()->subDays(20)->toDateString(),
            'ends_on' => null,
            'min_family_size' => null,
        ], areaNames: ['Khan Younis City', 'Bani Suheila', 'Al-Mawasi', 'Rafah City', 'Tal al-Sultan'], flagCodes: ['disability', 'chronic_illness', 'female_headed']);

        $this->createRound($cash, 1, now()->subDays(10), 'فرع البنك - خان يونس', 500, RoundStatus::Closed, $admin, $officers);

        // Hygiene kits: still being prepared, no rounds yet.
        $this->createProgram($admin, [
            'name_ar' => 'حقائب النظافة الشخصية',
            'name_en' => 'Hygiene kits',
            'type' => 'hygiene',
            'unit' => 'kit',
            'stock_quantity' => 600,
            'starts_on' => now()->addDays(15)->toDateString(),
            'ends_on' => null,
            'min_family_size' => null,
        ], areaNames: [], flagCodes: ['displaced'], status: ProgramStatus::Draft);
    }

    private function createProgram(User $admin, array $data, array $areaNames, array $flagCodes, ProgramStatus $status = ProgramStatus::Active): AidProgram
    {
        $program = AidProgram::create($data + [
            'status' => $status,
            'created_by' => $admin->id,
        ]);

        $program->targetAreas()->sync(Area::whereIn('name_en', $areaNames)->pluck('id'));
        $program->requiredFlags()->sync(VulnerabilityFlag::whereIn('code', $flagCodes)->pluck('id'));

        return $program;
    }

    /**
     * Allocate every eligible household to the round. Past rounds get pickups recorded
     * and the collected quantity is taken out of the program's stock.
     */
    private function createRound(AidProgram $program, int $number, $date, string $location, int $quantity, RoundStatus $status, User $admin, $officers): void
    {
        $round = $program->distributionRounds()->create([
            'round_number' => $number,
            'scheduled_on' => $date->toDateString(),
            'location' => $location,
            'quantity_per_household' => $quantity,
            'status' => $status,
        ]);

        $collected = 0;

        foreach ($program->eligibleHouseholds()->get() as $household) {
            $allocation = [
                'household_id' => $household->id,
                'quantity' => $quantity,
                'assigned_by' => $admin->id,
                'status' => AllocationStatus::Scheduled,
            ];

            if ($status === RoundStatus::Closed) {
                $allocation = array_merge($allocation, $this->pickup($date, $officers));

                if ($allocation['status'] === AllocationStatus::Collected) {
                    $collected += $quantity;
                }
            }

            $round->allocations()->create($allocation);
        }

        $program->decrement('stock_quantity', $collected);
    }

    /**
     * About 90% of households collect; some send someone else to pick up for them.
     */
    private function pickup($date, $officers): array
    {
        if (fake()->boolean(10)) {
            return ['status' => AllocationStatus::NoShow];
        }

        $byProxy = fake()->boolean(15);

        return [
            'status' => AllocationStatus::Collected,
            'collected_at' => $date->copy()->setTime(fake()->numberBetween(8, 14), fake()->numberBetween(0, 59)),
            'received_by_name' => $byProxy ? HouseholdFactory::fullName(Gender::Male) : null,
            'received_by_national_id' => $byProxy ? fake()->numerify('4########') : null,
            'recorded_by' => $officers->random()->id,
        ];
    }
}
