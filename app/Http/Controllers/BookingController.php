<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\ServiceOrder;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function index()
    {
        // Hanya tampilkan order milik customer yang login
        $customerId = auth()->user()->customer->id ?? null;
        if (!$customerId) abort(403);

        $orders = ServiceOrder::where('customer_id', $customerId)
            ->with(['vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('booking.index', compact('orders'));
    }

    public function create()
    {
        Gate::authorize('create-booking');
        
        $customer = auth()->user()->customer;
        if (!$customer) {
            return redirect()->route('dashboard')->with('error', 'Profil pelanggan tidak ditemukan.');
        }

        // Ambil kendaraan yang aktif saja
        $vehicles = $customer->vehicles()->where('status', 'aktif')->get();
        return view('booking.create', compact('vehicles'));
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Generate Order Number
            $data['order_number'] = $this->generateOrderNumber();
            $data['customer_id'] = auth()->user()->customer->id;
            $data['status'] = 'terdaftar';
            $data['estimated_cost'] = 0; // Karena pelanggan belum pilih layanan dari web, hanya booking slot
            
            $order = ServiceOrder::create($data);

            AuditLogService::log('Booking servis', 'service_orders', $order->id);

            return redirect()->route('booking.index')->with('success', 'Booking berhasil! Silakan bawa kendaraan Anda pada tanggal yang ditentukan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat booking: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, ServiceOrder $order)
    {
        Gate::authorize('cancel-order', $order);

        $request->validate([
            'cancel_reason' => 'required|string|min:5'
        ]);

        try {
            $order->update([
                'status' => 'batal',
                'notes' => $order->notes . "\n\nAlasan Batal: " . $request->cancel_reason
            ]);

            AuditLogService::log('Pembatalan order via pelanggan', 'service_orders', $order->id);

            return redirect()->back()->with('success', 'Booking servis berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan booking.');
        }
    }

    public function show(ServiceOrder $order)
    {
        Gate::authorize('view-order', $order);
        $order->load(['vehicle', 'serviceAdvisor', 'mechanic', 'services']);
        return view('booking.show', compact('order')); // Kita buat view terpisah untuk pelanggan agar tampilannya lebih simpel jika perlu, atau pakai view yang sama dengan admin (saya sediakan view-nya di bawah)
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