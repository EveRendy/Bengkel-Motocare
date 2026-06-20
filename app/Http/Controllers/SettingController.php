<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function edit()
    {
        Gate::authorize('manage-settings');

        $settings = Setting::group($this->defaults());

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        Gate::authorize('manage-settings');

        $data = $request->validate([
            'workshop_name' => ['required', 'string', 'max:100'],
            'workshop_tagline' => ['nullable', 'string', 'max:150'],
            'workshop_address' => ['nullable', 'string', 'max:255'],
            'workshop_phone' => ['nullable', 'string', 'max:30'],
            'workshop_email' => ['nullable', 'email', 'max:100'],
            'workshop_operational_hours' => ['nullable', 'string', 'max:120'],
            'workshop_logo_text' => ['nullable', 'string', 'max:10'],
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        AuditLogService::log('Ubah pengaturan bengkel', 'settings');

        return redirect()->route('settings.edit')->with('success', 'Pengaturan bengkel berhasil diperbarui.');
    }

    private function defaults(): array
    {
        return [
            'workshop_name' => 'MotoCare',
            'workshop_tagline' => 'Sistem Informasi Bengkel Motor',
            'workshop_address' => 'Jl. Contoh No. 1, Jakarta',
            'workshop_phone' => '081234567890',
            'workshop_email' => 'admin@motocare.com',
            'workshop_operational_hours' => 'Senin - Sabtu, 08.00 - 17.00',
            'workshop_logo_text' => 'MC',
        ];
    }
}
