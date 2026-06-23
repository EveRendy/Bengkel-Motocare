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

            // For walk-in customers, set customer_id and vehicle_id to null
            if ($data['customer_type'] === 'walkin') {
                $data['customer_id'] = null;
                $data['vehicle_id'] = null;
            }
            unset($data['customer_type']);
            
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

            // Jika ada mechanic_id, buat MechanicJob
            if (!empty($data['mechanic_id'])) {
                \App\Models\MechanicJob::create([
                    'service_order_id' => $order->id,
                    'mechanic_id' => $data['mechanic_id'],
                    'started_at' => null,
                    'finished_at' => null,
                ]);
            }

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

            // Jika ada mechanic_id, buat atau update MechanicJob
            if (!empty($data['mechanic_id'])) {
                $job = \App\Models\MechanicJob::where('service_order_id', $order->id)->first();
                if (!$job) {
                    \App\Models\MechanicJob::create([
                        'service_order_id' => $order->id,
                        'mechanic_id' => $data['mechanic_id'],
                        'started_at' => null,
                        'finished_at' => null,
                    ]);
                } else {
                    $job->update(['mechanic_id' => $data['mechanic_id']]);
                }
            }

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

            // Delete associated MechanicJob if exists
            $mechanicJob = \App\Models\MechanicJob::where('service_order_id', $order->id)->first();
            if ($mechanicJob) {
                $mechanicJob->delete();
            }

            AuditLogService::log('Pembatalan order servis', 'service_orders', $order->id);
            return back()->with('success', 'Order dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan order.');
        }
    }

    public function assignMechanic(Request $request, \App\Models\ServiceOrder $order)
    {
        Gate::authorize('assign-mechanic');
        $request->validate(['mechanic_id' => 'required|exists:users,id']);

        if (!in_array($order->status, ['terdaftar', 'menunggu'])) {
            return back()->with('error', 'Hanya order berstatus Terdaftar atau Menunggu yang bisa di-assign mekanik.');
        }

        try {
            $updateData = ['mechanic_id' => $request->mechanic_id];
            if ($order->status == 'terdaftar') {
                $updateData['status'] = 'menunggu';
            }

            $order->update($updateData);

            // AUTO-CREATE atau UPDATE MechanicJob
            $job = \App\Models\MechanicJob::where('service_order_id', $order->id)->first();
            if (!$job) {
                \App\Models\MechanicJob::create([
                    'service_order_id' => $order->id,
                    'mechanic_id' => $request->mechanic_id,
                    'started_at' => null,
                    'finished_at' => null,
                ]);
            } else {
                // Jika ganti mekanik
                $job->update(['mechanic_id' => $request->mechanic_id]);
            }

            \App\Services\AuditLogService::log('Penugasan mekanik', 'service_orders', $order->id);

            return back()->with('success', 'Mekanik berhasil ditugaskan. Menunggu mekanik memulai pengerjaan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menugaskan mekanik: ' . $e->getMessage());
        }
    }

    public function startService(\App\Models\ServiceOrder $order)
    {
        Gate::authorize('manage-orders');

        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Status order harus Menunggu sebelum dikerjakan.');
        }
        
        if (!$order->mechanic_id) {
            return back()->with('error', 'Order belum ditugaskan ke mekanik.');
        }

        try {
            $order->update(['status' => 'dikerjakan']);
            
            // MechanicJob seharusnya sudah ada dari saat assign, tapi jaga-jaga kita firstOrCreate
            $mechanicJob = \App\Models\MechanicJob::firstOrCreate(
                ['service_order_id' => $order->id],
                ['mechanic_id' => $order->mechanic_id, 'started_at' => now()]
            );

            if (!$mechanicJob->started_at) {
                $mechanicJob->update(['started_at' => now()]);
            }

            \App\Services\AuditLogService::log('Force start pengerjaan (Oleh SA)', 'service_orders', $order->id);
            return back()->with('success', 'Status diubah menjadi Dikerjakan (Emergency Force Start). Normalnya mekanik yang harus klik mulai.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulai pengerjaan: ' . $e->getMessage());
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

    public function bookings()
    {
        Gate::authorize('view-bookings');

        // Menampilkan booking yang belum diproses (service_advisor_id null)
        $bookings = \App\Models\ServiceOrder::with(['customer', 'vehicle'])
            ->whereNull('service_advisor_id')
            ->where('status', 'terdaftar')
            ->latest()
            ->paginate(10);

        return view('service-orders.bookings', compact('bookings'));
    }

    public function processBookingForm(\App\Models\ServiceOrder $order)
    {
        Gate::authorize('process-booking');

        // Pastikan belum diproses
        if ($order->service_advisor_id !== null) {
            return redirect()->route('bookings.index')->with('error', 'Booking ini sudah diproses.');
        }

        $order->load(['customer', 'vehicle']);
        $services = \App\Models\Service::where('status', 'aktif')->with('category')->get();
        $mechanics = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'mekanik'))
            ->where('status', 'aktif')
            ->get();

        return view('service-orders.process', compact('order', 'services', 'mechanics'));
    }

    public function processBooking(\App\Http\Requests\ProcessBookingRequest $request, \App\Models\ServiceOrder $order)
    {
        Gate::authorize('process-booking');

        if ($order->service_advisor_id !== null) {
            return redirect()->route('bookings.index')->with('error', 'Booking ini sudah diproses.');
        }

        try {
            $data = $request->validated();
            
            // Hitung estimasi biaya
            $selectedServices = \App\Models\Service::whereIn('id', $data['services'])->get();
            $estimatedCost = $selectedServices->sum('price');
            
            // Tentukan status lanjutan
            $status = 'terdaftar';
            if (!empty($data['mechanic_id'])) {
                $status = 'menunggu';
            }

            // Update order
            $order->update([
                'service_advisor_id' => auth()->id(),
                'mechanic_id' => $data['mechanic_id'] ?? null,
                'complaint' => $data['complaint'],
                'current_km' => $data['current_km'],
                'notes' => $data['notes'] ?? null,
                'estimated_cost' => $estimatedCost,
                'status' => $status,
                // Jika booking dari user belum punya order_number, buatkan. Jika sudah, pakai yang lama.
                'order_number' => $order->order_number ?? $this->generateOrderNumber(),
            ]);

            // Simpan layanan ke pivot table dengan harga saat ini (price_at_time)
            $pivotData = [];
            foreach ($selectedServices as $srv) {
                $pivotData[$srv->id] = ['price_at_time' => $srv->price];
            }
            // Karena ini booking baru, kita pakai sync untuk memastikan bersih
            $order->services()->sync($pivotData);

            // Jika ada mechanic_id, buat MechanicJob
            if (!empty($data['mechanic_id'])) {
                $job = \App\Models\MechanicJob::where('service_order_id', $order->id)->first();
                if (!$job) {
                    \App\Models\MechanicJob::create([
                        'service_order_id' => $order->id,
                        'mechanic_id' => $data['mechanic_id'],
                        'started_at' => null,
                        'finished_at' => null,
                    ]);
                } else {
                    $job->update(['mechanic_id' => $data['mechanic_id']]);
                }
            }

            \App\Services\AuditLogService::log('Proses booking menjadi order', 'service_orders', $order->id);

            return redirect()->route('service-orders.show', $order)->with('success', 'Booking berhasil diproses menjadi Order Servis.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memproses booking: ' . $e->getMessage());
        }
    }
}