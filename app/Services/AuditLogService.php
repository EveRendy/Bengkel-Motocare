<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(string $activity, string $module, ?string $dataId = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'activity' => $activity,
                'module' => $module,
                'data_id' => $dataId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error secara diam-diam (silent fail) agar tidak mengganggu proses utama
            \Log::error('Gagal mencatat audit log: ' . $e->getMessage());
        }
    }
}