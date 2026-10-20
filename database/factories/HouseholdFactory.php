<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\HouseholdStatus;
use App\Enums\Role;
use App\Models\Area;
use App\Models\Household;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Household>
 */
class HouseholdFactory extends Factory
{
    public const MALE_NAMES = [
        'محمد', 'أحمد', 'محمود', 'خالد', 'يوسف', 'إبراهيم', 'سامي', 'عمر',
        'علي', 'حسن', 'رامي', 'نضال', 'فادي', 'وائل', 'مازن', 'جمال',
    ];

    public const FEMALE_NAMES = [
        'فاطمة', 'مريم', 'آمنة', 'سعاد', 'هالة', 'رنا', 'نور', 'سمر',
        'ختام', 'إيمان', 'وفاء', 'هبة',
    ];

    public const FAMILY_NAMES = [
        'الشوا', 'أبو شمالة', 'النجار', 'الأسطل', 'حلس', 'الفرا', 'أبو عودة', 'قديح',
        'الكرد', 'المصري', 'عاشور', 'شعت', 'أبو ستة', 'البطش', 'الحايك', 'السقا',
    ];

    public const ADDRESSES = [
        'شارع صلاح الدين، قرب محطة الوقود',
        'قرب المسجد الكبير',
        'شارع الجلاء، عمارة رقم 12',
        'قرب مدرسة الوكالة الإعدادية',
        'المخيم، بلوك 5',
        'شارع البحر، خلف السوق',
        'قرب العيادة الحكومية',
        'حي الأمل، شارع 8',
    ];

    public function definition(): array
    {
        $gender = fake()->randomElement(Gender::cases());

        return [
            'national_id' => fake()->unique()->numerify('4########'),
            'head_name' => self::fullName($gender),
            'head_gender' => $gender,
            'head_birth_date' => fake()->dateTimeBetween('-80 years', '-20 years')->format('Y-m-d'),
            'phone' => fake()->randomElement(['059', '056']).fake()->numerify('#######'),
            'email' => null,
            'area_id' => Area::factory(),
            'address' => fake()->randomElement(self::ADDRESSES),
            'family_size' => fake()->numberBetween(1, 12),
            'status' => HouseholdStatus::Registered,
            'registered_by' => User::factory()->role(Role::FieldWorker),
        ];
    }

    /**
     * A four-part Arabic name: first, father, grandfather, family.
     */
    public static function fullName(Gender $gender): string
    {
        $first = fake()->randomElement($gender === Gender::Female ? self::FEMALE_NAMES : self::MALE_NAMES);

        return implode(' ', [
            $first,
            fake()->randomElement(self::MALE_NAMES),
            fake()->randomElement(self::MALE_NAMES),
            fake()->randomElement(self::FAMILY_NAMES),
        ]);
    }

    public function status(HouseholdStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
