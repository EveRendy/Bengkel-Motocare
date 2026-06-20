<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'workshop_name' => 'MotoCare',
            'workshop_tagline' => 'Sistem Informasi Bengkel Motor',
            'workshop_address' => 'Jl. Contoh No. 1, Jakarta',
            'workshop_phone' => '081234567890',
            'workshop_email' => 'admin@motocare.com',
            'workshop_operational_hours' => 'Senin - Sabtu, 08.00 - 17.00',
            'workshop_logo_text' => 'MC',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
