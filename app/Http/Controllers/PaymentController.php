<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ServiceOrder;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('process-payment');

        $query = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'payment.invoice'])
            ->whereIn('status', ['selesai_servis', 'selesai']);

        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'belum_bayar') {
                $query->where('status', 'selesai_servis');
            }

            if ($request->status === 'lunas') {
                $query->where('status', 'selesai');
            }
        }

        $orders = $query->latest()->paginate(10);

        return view('payments.index', compact('orders'));
    }

    public function show(ServiceOrder $order)
    {
        Gate::authorize('process-payment');

        if (!in_array($order->status, ['selesai_servis', 'selesai'])) {
            return redirect()->route('payments.index')->with('error', 'Tagihan hanya tersedia untuk order selesai servis.');
        }

        $order->load(['customer', 'vehicle', 'serviceAdvisor', 'mechanic', 'services', 'spareparts', 'mechanicJob', 'payment.invoice', 'payment.cashier']);
        $totals = $this->calculateTotals($order);

        return view('payments.show', compact('order', 'totals'));
    }

    public function store(StorePaymentRequest $request, ServiceOrder $order)
    {
        Gate::authorize('process-payment');

        if ($order->status !== 'selesai_servis') {
            return back()->with('error', 'Pembayaran hanya bisa diproses untuk order berstatus selesai servis.');
        }

        if ($order->payment()->exists()) {
            return redirect()->route('payments.show', $order)->with('error', 'Order ini sudah memiliki pembayaran.');
        }

        $payment = DB::transaction(function () use ($request, $order) {
            $order->load(['services', 'spareparts']);
            $totals = $this->calculateTotals($order);
            $discount = (float) ($request->discount ?? 0);
            $totalAmount = max(0, $totals['total_service'] + $totals['total_sparepart'] - $discount);

            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'service_order_id' => $order->id,
                'total_service' => $totals['total_service'],
                'total_sparepart' => $totals['total_sparepart'],
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'lunas',
                'payment_date' => now(),
                'cashier_id' => auth()->id(),
            ]);

            Invoice::create([
                'payment_id' => $payment->id,
                'invoice_number' => $this->generateInvoiceNumber(),
            ]);

            $order->update(['status' => 'selesai']);

            AuditLogService::log('Input pembayaran', 'payments', $payment->id);
            AuditLogService::log('Generate invoice', 'invoices', $payment->id);

            return $payment;
        });

        return redirect()->route('invoices.show', $payment)->with('success', 'Pembayaran berhasil diproses dan invoice dibuat.');
    }

    public function invoice(Payment $payment)
    {
        $payment->load([
            'invoice',
            'cashier',
            'serviceOrder.customer',
            'serviceOrder.vehicle',
            'serviceOrder.serviceAdvisor',
            'serviceOrder.mechanic',
            'serviceOrder.services',
            'serviceOrder.spareparts',
        ]);

        $order = $payment->serviceOrder;

        if (!Gate::allows('process-payment') && !Gate::allows('view-order', $order)) {
            abort(403);
        }

        AuditLogService::log('Cetak invoice', 'invoices', $payment->id);

        return view('invoices.show', compact('payment', 'order'));
    }

    private function calculateTotals(ServiceOrder $order): array
    {
        $totalService = $order->services->sum(fn ($service) => (float) $service->pivot->price_at_time);
        $totalSparepart = $order->spareparts->sum(fn ($sparepart) => (float) $sparepart->pivot->price_at_time * (int) $sparepart->pivot->quantity);

        return [
            'total_service' => $totalService,
            'total_sparepart' => $totalSparepart,
            'grand_total' => $totalService + $totalSparepart,
        ];
    }

    private function generatePaymentNumber(): string
    {
        $datePrefix = 'PAY-' . date('Ymd') . '-';
        $lastPayment = Payment::where('payment_number', 'like', $datePrefix . '%')->latest('id')->first();

        if (!$lastPayment) {
            return $datePrefix . '0001';
        }

        $lastNumber = (int) substr($lastPayment->payment_number, -4);

        return $datePrefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generateInvoiceNumber(): string
    {
        $datePrefix = 'INV-' . date('Ymd') . '-';
        $lastInvoice = Invoice::where('invoice_number', 'like', $datePrefix . '%')->latest('id')->first();

        if (!$lastInvoice) {
            return $datePrefix . '0001';
        }

        $lastNumber = (int) substr($lastInvoice->invoice_number, -4);

        return $datePrefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
