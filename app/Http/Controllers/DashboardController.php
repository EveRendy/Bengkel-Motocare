<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data placeholder/dummy
        $data = [
            'totalOrdersToday' => 0,
            'waitingVehicles' => 0,
            'workingVehicles' => 0,
            'completedToday' => 0,
            'totalPaymentsToday' => 0,
        ];

        return view('dashboard.index', $data);
    }
}