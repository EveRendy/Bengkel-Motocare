<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $activeStatuses = ['terdaftar', 'menunggu', 'dikerjakan', 'selesai_servis'];

        try {
            $queueOrders = ServiceOrder::with(['vehicle'])
                ->whereNotNull('service_advisor_id')
                ->whereIn('status', $activeStatuses)
                ->orderByRaw("CASE status WHEN 'dikerjakan' THEN 1 WHEN 'menunggu' THEN 2 WHEN 'terdaftar' THEN 3 WHEN 'selesai_servis' THEN 4 ELSE 5 END")
                ->orderBy('order_date')
                ->limit(3)
                ->get();

            $queueStats = [
                'today_orders' => ServiceOrder::whereNotNull('service_advisor_id')
                    ->whereDate('created_at', today())
                    ->count(),
                'in_progress' => ServiceOrder::where('status', 'dikerjakan')->count(),
                'completed_today' => ServiceOrder::where('status', 'selesai')
                    ->whereDate('updated_at', today())
                    ->count(),
            ];
        } catch (\Throwable $exception) {
            $queueOrders = collect();
            $queueStats = [
                'today_orders' => 0,
                'in_progress' => 0,
                'completed_today' => 0,
            ];
        }

        return view('welcome', compact('queueOrders', 'queueStats'));
    }
}
