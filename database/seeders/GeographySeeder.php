<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GeographySeeder extends Seeder
{
    /**
     * The five governorates of the Gaza Strip and some of their main areas.
     */
    private const GOVERNORATES = [
        ['شمال غزة', 'North Gaza', [
            ['جباليا', 'Jabalia'],
            ['بيت لاهيا', 'Beit Lahia'],
            ['بيت حانون', 'Beit Hanoun'],
        ]],
        ['غزة', 'Gaza', [
            ['الرمال', 'Al-Rimal'],
            ['الشاطئ', 'Al-Shati'],
            ['الزيتون', 'Al-Zaytoun'],
            ['الشجاعية', 'Al-Shujaiya'],
            ['تل الهوى', 'Tal al-Hawa'],
        ]],
        ['دير البلح', 'Deir al-Balah', [
            ['دير البلح', 'Deir al-Balah'],
            ['النصيرات', 'Al-Nuseirat'],
            ['البريج', 'Al-Bureij'],
            ['المغازي', 'Al-Maghazi'],
            ['الزوايدة', 'Al-Zawaida'],
        ]],
        ['خان يونس', 'Khan Younis', [
            ['خان يونس البلد', 'Khan Younis City'],
            ['بني سهيلا', 'Bani Suheila'],
            ['عبسان', 'Abasan'],
            ['المواصي', 'Al-Mawasi'],
            ['خزاعة', 'Khuza\'a'],
        ]],
        ['رفح', 'Rafah', [
            ['رفح البلد', 'Rafah City'],
            ['تل السلطان', 'Tal al-Sultan'],
            ['الشابورة', 'Al-Shaboura'],
        ]],
    ];

    public function run(): void
    {
        foreach (self::GOVERNORATES as [$nameAr, $nameEn, $areas]) {
            $governorate = Governorate::create(['name_ar' => $nameAr, 'name_en' => $nameEn]);

            foreach ($areas as [$areaAr, $areaEn]) {
                $governorate->areas()->create(['name_ar' => $areaAr, 'name_en' => $areaEn]);
            }
        }
    }
}
