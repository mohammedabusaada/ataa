<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\HouseholdStatus;
use App\Enums\Role;
use App\Models\Area;
use App\Models\Household;
use App\Models\User;
use App\Models\VulnerabilityFlag;
use Illuminate\Database\Seeder;

class HouseholdSeeder extends Seeder
{
    private const COUNT = 150;

    private const ELIGIBLE_NOTES = [
        'تمت الزيارة الميدانية والبيانات مطابقة.',
        'تم التحقق من عدد الأفراد ووثائق الهوية.',
        'الأسرة بدون مصدر دخل ثابت، مستوفية للشروط.',
    ];

    private const NOT_ELIGIBLE_NOTES = [
        'تبين وجود مصدر دخل ثابت للأسرة.',
        'عدد الأفراد غير مطابق لما تم تسجيله.',
        'الأسرة مسجلة لدى جهة أخرى بنفس رقم الهوية لرب الأسرة.',
    ];

    public function run(): void
    {
        $areaIds = Area::pluck('id');
        $fieldWorkers = User::where('role', Role::FieldWorker)->where('is_active', true)->get();
        $flags = VulnerabilityFlag::pluck('id', 'code');

        for ($i = 0; $i < self::COUNT; $i++) {
            $registeredAt = now()->subDays(fake()->numberBetween(20, 150))->setTime(fake()->numberBetween(8, 15), fake()->numberBetween(0, 59));
            $registrar = $fieldWorkers->random();
            $finalStatus = $this->randomFinalStatus();

            $household = Household::factory()
                ->status($finalStatus)
                ->create([
                    'area_id' => $areaIds->random(),
                    'registered_by' => $registrar->id,
                    'created_at' => $registeredAt,
                    'updated_at' => $registeredAt,
                ]);

            $household->vulnerabilityFlags()->sync($this->flagsFor($household, $flags));

            $this->recordHistory($household, $finalStatus, $registrar, $fieldWorkers);
        }
    }

    /**
     * Roughly: 60% eligible, 15% under verification, 15% just registered, 10% not eligible.
     */
    private function randomFinalStatus(): HouseholdStatus
    {
        $roll = fake()->numberBetween(1, 100);

        return match (true) {
            $roll <= 60 => HouseholdStatus::Eligible,
            $roll <= 75 => HouseholdStatus::UnderVerification,
            $roll <= 90 => HouseholdStatus::Registered,
            default => HouseholdStatus::NotEligible,
        };
    }

    /**
     * Flags that match the household's own data, plus up to two random ones.
     */
    private function flagsFor(Household $household, $flags): array
    {
        $codes = [];

        if ($household->head_gender === Gender::Female) {
            $codes[] = 'female_headed';
        }

        if ($household->head_birth_date->age >= 60) {
            $codes[] = 'elderly';
        }

        $others = ['disability', 'chronic_illness', 'displaced', 'orphans'];
        $codes = array_merge($codes, fake()->randomElements($others, fake()->numberBetween(0, 2)));

        return $flags->only($codes)->values()->all();
    }

    /**
     * Write the status history that leads to the household's current status.
     */
    private function recordHistory(Household $household, HouseholdStatus $finalStatus, User $registrar, $fieldWorkers): void
    {
        $at = $household->created_at->copy();

        $household->statusChanges()->create([
            'from_status' => null,
            'to_status' => HouseholdStatus::Registered,
            'notes' => null,
            'changed_by' => $registrar->id,
            'created_at' => $at,
        ]);

        if ($finalStatus === HouseholdStatus::Registered) {
            return;
        }

        $verifier = $fieldWorkers->random();
        $at = $at->copy()->addDays(fake()->numberBetween(1, 5));

        $household->statusChanges()->create([
            'from_status' => HouseholdStatus::Registered,
            'to_status' => HouseholdStatus::UnderVerification,
            'notes' => 'تم تحديد موعد للزيارة الميدانية.',
            'changed_by' => $verifier->id,
            'created_at' => $at,
        ]);

        if ($finalStatus === HouseholdStatus::UnderVerification) {
            return;
        }

        $household->statusChanges()->create([
            'from_status' => HouseholdStatus::UnderVerification,
            'to_status' => $finalStatus,
            'notes' => fake()->randomElement(
                $finalStatus === HouseholdStatus::Eligible ? self::ELIGIBLE_NOTES : self::NOT_ELIGIBLE_NOTES
            ),
            'changed_by' => $verifier->id,
            'created_at' => $at->copy()->addDays(fake()->numberBetween(2, 10)),
        ]);
    }
}
