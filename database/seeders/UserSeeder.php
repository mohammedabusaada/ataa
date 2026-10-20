<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Demo staff accounts. Every account uses the password "password".
     */
    public function run(): void
    {
        $staff = [
            ['مدير النظام', 'admin@ataa.test', Role::Admin],
            ['سامي النجار', 'field1@ataa.test', Role::FieldWorker],
            ['هبة الأسطل', 'field2@ataa.test', Role::FieldWorker],
            ['يوسف حلس', 'field3@ataa.test', Role::FieldWorker],
            ['مازن الفرا', 'officer1@ataa.test', Role::DistributionOfficer],
            ['رنا عاشور', 'officer2@ataa.test', Role::DistributionOfficer],
            ['جمال الكرد', 'auditor@ataa.test', Role::Auditor],
        ];

        foreach ($staff as [$name, $email, $role]) {
            User::factory()->role($role)->create(['name' => $name, 'email' => $email]);
        }

        User::factory()->role(Role::FieldWorker)->inactive()->create([
            'name' => 'موظف سابق',
            'email' => 'former@ataa.test',
        ]);
    }
}
