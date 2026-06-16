<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $totalOrdersToday = ServiceOrder::whereDate('created_at', $today)->count();
        $waitingVehicles = ServiceOrder::where('status', 'menunggu')->count();
        $workingVehicles = ServiceOrder::where('status', 'dikerjakan')->count();
        $completedToday = ServiceOrder::where('status', 'selesai')->whereDate('updated_at', $today)->count();
        
        // Asumsi belum ada tabel Payment di phase ini, maka total payment = 0 dulu.
        $totalPaymentsToday = 0; 

        $recentOrders = ServiceOrder::with(['customer', 'vehicle'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalOrdersToday',
            'waitingVehicles',
            'workingVehicles',
            'completedToday',
            'totalPaymentsToday',
            'recentOrders'
        ));
    }
}