<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceOrderRequest;
use App\Http\Requests\UpdateServiceOrderRequest;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-orders');

        $query = ServiceOrder::with(['customer', 'vehicle', 'serviceAdvisor', 'mechanic']);

        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        $mechanics = User::whereHas('roles', function($q) { $q->where('name', 'mekanik'); })->where('status', 'aktif')->get();

        return view('service-orders.index', compact('orders', 'mechanics'));
    }

    public function create()
    {
        Gate::authorize('manage-orders');
        
        $customers = Customer::where('status', 'aktif')->get();
        // Untuk form sederhana, kita kirim semua vehicle aktif
        $vehicles = Vehicle::where('status', 'aktif')->get();
        $services = Service::where('status', 'aktif')->with('category')->get();
        $mechanics = User::whereHas('roles', function($q) { $q->where('name', 'mekanik'); })->where('status', 'aktif')->get();

        return view('service-orders.create', compact('customers', 'vehicles', 'services', 'mechanics'));
    }

    public function store(StoreServiceOrderRequest $request)
    {
        try {
            $data = $request->validated();
            
            $data['order_number'] = $this->generateOrderNumber();
            $data['status'] = $data['mechanic_id'] ? 'menunggu' : 'terdaftar';
            $data['service_advisor_id'] = auth()->id();
            
            // Hitung estimasi biaya dari layanan yang dipilih
            $selectedServices = Service::whereIn('id', $data['services'])->get();
            $data['estimated_cost'] = $selectedServices->sum('price');
            
            $servicesData = $data['services'];
            unset($data['services']);

            $order = ServiceOrder::create($data);

            // Attach pivot table dengan snapshot harga saat ini
            $pivotData = [];
            foreach ($selectedServices as $srv) {
                $pivotData[$srv->id] = ['price_at_time' => $srv->price];
            }
            $order->services()->attach($pivotData);

            AuditLogService::log('Pembuatan order servis', 'service_orders', $order->id);

            return redirect()->route('service-orders.index')->with('success', 'Order servis berhasil dibuat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    public function show(ServiceOrder $order)
    {
        Gate::authorize('view-order', $order);
        $order->load(['customer', 'vehicle', 'serviceAdvisor', 'mechanic', 'services']);
        return view('service-orders.show', compact('order'));
    }

    public function edit(ServiceOrder $order)
    {
        Gate::authorize('manage-orders');
        
        if (!in_array($order->status, ['terdaftar', 'menunggu'])) {
            return redirect()->route('service-orders.index')->with('error', 'Order yang sudah dikerjakan tidak dapat diedit sepenuhnya.');
        }

        $services = Service::where('status', 'aktif')->with('category')->get();
        $mechanics = User::whereHas('roles', function($q) { $q->where('name', 'mekanik'); })->where('status', 'aktif')->get();
        $selectedServiceIds = $order->services->pluck('id')->toArray();

        return view('service-orders.edit', compact('order', 'services', 'mechanics', 'selectedServiceIds'));
    }

    public function update(UpdateServiceOrderRequest $request, ServiceOrder $order)
    {
        try {
            $data = $request->validated();
            
            // Recalculate cost
            $selectedServices = Service::whereIn('id', $data['services'])->get();
            $data['estimated_cost'] = $selectedServices->sum('price');
            
            if ($data['mechanic_id'] && $order->status == 'terdaftar') {
                $data['status'] = 'menunggu';
            }

            $servicesData = $data['services'];
            unset($data['services']);

            $order->update($data);

            // Sync pivot
            $pivotData = [];
            foreach ($selectedServices as $srv) {
                $pivotData[$srv->id] = ['price_at_time' => $srv->price];
            }
            $order->services()->sync($pivotData);

            AuditLogService::log('Ubah order servis', 'service_orders', $order->id);

            return redirect()->route('service-orders.index')->with('success', 'Data order berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui order.');
        }
    }

    public function cancel(Request $request, ServiceOrder $order)
    {
        Gate::authorize('cancel-order', $order);
        
        $request->validate(['cancel_reason' => 'required|string|min:5']);

        try {
            $order->update([
                'status' => 'batal',
                'notes' => $order->notes . "\n\nAlasan Batal (Oleh " . auth()->user()->name . "): " . $request->cancel_reason
            ]);

            AuditLogService::log('Pembatalan order servis', 'service_orders', $order->id);
            return back()->with('success', 'Order dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan order.');
        }
    }

    public function assignMechanic(Request $request, ServiceOrder $order)
    {
        Gate::authorize('assign-mechanic');
        $request->validate(['mechanic_id' => 'required|exists:users,id']);

        try {
            $updateData = ['mechanic_id' => $request->mechanic_id];
            if ($order->status == 'terdaftar') {
                $updateData['status'] = 'menunggu';
            }

            $order->update($updateData);
            AuditLogService::log('Penugasan mekanik', 'service_orders', $order->id);

            return back()->with('success', 'Mekanik berhasil ditugaskan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan mekanik.');
        }
    }

    public function startService(ServiceOrder $order)
    {
        Gate::authorize('manage-orders'); // SA bisa force start

        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Status order harus Menunggu sebelum dikerjakan.');
        }

        try {
            $order->update(['status' => 'dikerjakan']);
            AuditLogService::log('Mulai pengerjaan (SA)', 'service_orders', $order->id);
            return back()->with('success', 'Status diubah menjadi Dikerjakan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update status.');
        }
    }

    private function generateOrderNumber()
    {
        $datePrefix = 'ORD-' . date('Ymd') . '-';
        $lastOrder = ServiceOrder::where('order_number', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder) {
            return $datePrefix . '0001';
        }

        $lastNumber = intval(substr($lastOrder->order_number, -4));
        return $datePrefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}