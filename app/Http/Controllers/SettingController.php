<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{
    public function edit()
    {
        Gate::authorize('manage-settings');

        $settings = Setting::group($this->defaults());

        return view('settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        $data = $request->validated();

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
