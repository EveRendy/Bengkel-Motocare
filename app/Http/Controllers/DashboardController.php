<?php

namespace App\Http\Controllers;

use App\Models\MechanicJob;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Sparepart;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $cards = [];
        $recentOrders = collect();
        $quickActions = [];
        $activityTitle = 'Aktivitas Order Terbaru';
        $topServices = collect();
        $topSpareparts = collect();
        $mechanicOrderStats = collect();

        if ($user->hasRole('pelanggan')) {
            $customer = $user->customer;

            $cards = [
                ['label' => 'Kendaraan Saya', 'value' => $customer ? $customer->vehicles()->count() : 0, 'color' => 'blue'],
                ['label' => 'Booking Aktif', 'value' => $customer ? $customer->serviceOrders()->whereNotIn('status', ['selesai', 'batal'])->count() : 0, 'color' => 'orange'],
                ['label' => 'Riwayat Servis', 'value' => $customer ? $customer->serviceOrders()->count() : 0, 'color' => 'green'],
            ];

            $recentOrders = $customer
                ? $customer->serviceOrders()->with(['vehicle'])->latest()->limit(5)->get()
                : collect();
            $activityTitle = 'Riwayat Order Saya';

            $quickActions = [
                ['label' => 'Tambah Kendaraan', 'route' => 'my-vehicles.create'],
                ['label' => 'Booking Servis', 'route' => 'booking.create'],
                ['label' => 'Lihat Riwayat', 'route' => 'booking.index'],
            ];
        } elseif ($user->hasRole('mekanik')) {
            $cards = [
                ['label' => 'Pekerjaan Menunggu', 'value' => MechanicJob::where('mechanic_id', $user->id)->whereHas('serviceOrder', fn ($q) => $q->where('status', 'menunggu'))->count(), 'color' => 'yellow'],
                ['label' => 'Sedang Dikerjakan', 'value' => MechanicJob::where('mechanic_id', $user->id)->whereHas('serviceOrder', fn ($q) => $q->where('status', 'dikerjakan'))->count(), 'color' => 'orange'],
                ['label' => 'Selesai Servis', 'value' => MechanicJob::where('mechanic_id', $user->id)->whereHas('serviceOrder', fn ($q) => $q->where('status', 'selesai_servis'))->count(), 'color' => 'green'],
            ];

            // Ambil service orders melalui MechanicJob untuk konsistensi
            $recentJobs = MechanicJob::where('mechanic_id', $user->id)
                ->with(['serviceOrder.customer', 'serviceOrder.vehicle'])
                ->latest()
                ->limit(5)
                ->get();
            $recentOrders = $recentJobs->pluck('serviceOrder');

            $quickActions = [
                ['label' => 'Buka Pekerjaan Saya', 'route' => 'mechanic-jobs.index'],
            ];
        } elseif ($user->hasRole('kasir')) {
            $cards = [
                ['label' => 'Siap Dibayar', 'value' => ServiceOrder::where('status', 'selesai_servis')->count(), 'color' => 'yellow'],
                ['label' => 'Pembayaran Hari Ini', 'value' => Payment::where('status', 'lunas')->whereDate('payment_date', $today)->count(), 'color' => 'green'],
                ['label' => 'Total Hari Ini', 'value' => 'Rp ' . number_format(Payment::where('status', 'lunas')->whereDate('payment_date', $today)->sum('total_amount'), 0, ',', '.'), 'color' => 'purple'],
            ];

            $recentOrders = ServiceOrder::whereIn('status', ['selesai_servis', 'selesai'])
                ->with(['customer', 'vehicle', 'payment'])
                ->latest()
                ->limit(5)
                ->get();

            $quickActions = [
                ['label' => 'Buka Pembayaran', 'route' => 'payments.index'],
            ];
        } elseif ($user->hasRole('manager')) {
            $cards = [
                ['label' => 'Order Bulan Ini', 'value' => ServiceOrder::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(), 'color' => 'blue'],
                ['label' => 'Order Selesai', 'value' => ServiceOrder::where('status', 'selesai')->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])->count(), 'color' => 'green'],
                ['label' => 'Pendapatan Bulan Ini', 'value' => 'Rp ' . number_format(Payment::where('status', 'lunas')->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_amount'), 0, ',', '.'), 'color' => 'purple'],
                ['label' => 'Pekerjaan Berjalan', 'value' => ServiceOrder::where('status', 'dikerjakan')->count(), 'color' => 'orange'],
                ['label' => 'Sparepart Habis', 'value' => Sparepart::where('stock_status', 'habis')->count(), 'color' => 'yellow'],
            ];

            $recentOrders = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'payment'])
                ->latest()
                ->limit(5)
                ->get();

            $activityTitle = 'Monitoring Order Terbaru';

            $quickActions = [
                ['label' => 'Buka Laporan', 'route' => 'reports.index'],
                ['label' => 'Audit Log', 'route' => 'audit-logs.index'],
            ];
        } else {
            $cards = [
                ['label' => 'Order Hari Ini', 'value' => ServiceOrder::whereDate('created_at', $today)->count(), 'color' => 'blue'],
                ['label' => 'Menunggu', 'value' => ServiceOrder::where('status', 'menunggu')->count(), 'color' => 'yellow'],
                ['label' => 'Dikerjakan', 'value' => ServiceOrder::where('status', 'dikerjakan')->count(), 'color' => 'orange'],
                ['label' => 'Selesai Hari Ini', 'value' => ServiceOrder::where('status', 'selesai')->whereDate('updated_at', $today)->count(), 'color' => 'green'],
                ['label' => 'Pembayaran Hari Ini', 'value' => 'Rp ' . number_format(Payment::where('status', 'lunas')->whereDate('payment_date', $today)->sum('total_amount'), 0, ',', '.'), 'color' => 'purple'],
            ];

            $recentOrders = ServiceOrder::with(['customer', 'vehicle'])->latest()->limit(5)->get();

            if ($user->hasRole('admin') || $user->hasRole('service_advisor')) {
                $quickActions = [
                    ['label' => 'Proses Booking', 'route' => 'bookings.index'],
                    ['label' => 'Buat Order', 'route' => 'service-orders.create'],
                    ['label' => 'Data Pelanggan', 'route' => 'customers.index'],
                ];
            }
        }

        if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('service_advisor')) {
            $topServices = Service::query()
                ->select('services.id', 'services.name', 'services.service_code')
                ->selectRaw('COUNT(service_order_services.service_order_id) as total_orders')
                ->join('service_order_services', 'services.id', '=', 'service_order_services.service_id')
                ->groupBy('services.id', 'services.name', 'services.service_code')
                ->orderByDesc('total_orders')
                ->limit(5)
                ->get();

            $topSpareparts = Sparepart::query()
                ->select('spareparts.id', 'spareparts.name', 'spareparts.part_code')
                ->selectRaw('COALESCE(SUM(service_order_spareparts.quantity), 0) as total_quantity')
                ->join('service_order_spareparts', 'spareparts.id', '=', 'service_order_spareparts.sparepart_id')
                ->groupBy('spareparts.id', 'spareparts.name', 'spareparts.part_code')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            $mechanicOrderStats = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'mekanik'))
                ->withCount(['serviceOrdersAsMechanic as total_orders'])
                ->orderByDesc('total_orders')
                ->limit(5)
                ->get();
        }

        return view('dashboard.index', compact(
            'cards',
            'recentOrders',
            'quickActions',
            'activityTitle',
            'topServices',
            'topSpareparts',
            'mechanicOrderStats'
        ));
    }
}
