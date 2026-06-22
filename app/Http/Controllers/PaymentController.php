<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\ServiceOrder;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('process-payment') && !Gate::allows('cancel-payment')) {
            abort(403);
        }

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
        if (!Gate::allows('process-payment') && !Gate::allows('cancel-payment')) {
            abort(403);
        }

        if (!in_array($order->status, ['selesai_servis', 'selesai'])) {
            return redirect()->route('payments.index')->with('error', 'Tagihan hanya tersedia untuk order selesai servis.');
        }

        $order->load(['customer', 'vehicle', 'serviceAdvisor', 'mechanic', 'services', 'spareparts', 'mechanicJob', 'payment.invoice', 'payment.cashier', 'payments.invoice', 'payments.cashier']);
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
            $discountPercent = (float) ($request->discount_percent ?? 0);
            $discountAmount = $totals['grand_total'] * ($discountPercent / 100);
            $totalAmount = max(0, $totals['grand_total'] - $discountAmount);

            $payment = Payment::create([
                'payment_number' => $this->generatePaymentNumber(),
                'service_order_id' => $order->id,
                'total_service' => $totals['total_service'],
                'total_sparepart' => $totals['total_sparepart'],
                'discount' => $discountAmount,
                'discount_percent' => $discountPercent,
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
        $this->loadInvoiceData($payment);

        $order = $payment->serviceOrder;

        if (!Gate::allows('process-payment') && !Gate::allows('view-order', $order)) {
            abort(403);
        }

        $settings = $this->invoiceSettings();

        AuditLogService::log('Cetak invoice', 'invoices', $payment->id);

        return view('invoices.show', compact('payment', 'order', 'settings'));
    }

    public function invoicePdf(Payment $payment)
    {
        $this->loadInvoiceData($payment);

        $order = $payment->serviceOrder;

        if (!Gate::allows('process-payment') && !Gate::allows('view-order', $order)) {
            abort(403);
        }

        $settings = $this->invoiceSettings();
        $fileName = 'invoice-' . ($payment->invoice->invoice_number ?? $payment->payment_number) . '.pdf';

        AuditLogService::log('Download invoice PDF', 'invoices', $payment->id);

        return Pdf::loadView('invoices.pdf', compact('payment', 'order', 'settings'))
            ->setPaper('a4')
            ->download($fileName);
    }

    public function cancel(Request $request, Payment $payment)
    {
        Gate::authorize('cancel-payment');

        $request->validate([
            'cancel_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        if ($payment->status !== 'lunas') {
            return back()->with('error', 'Hanya pembayaran lunas yang bisa dibatalkan.');
        }

        DB::transaction(function () use ($request, $payment) {
            $payment->load('serviceOrder');

            $payment->update([
                'status' => 'batal',
                'cancel_reason' => $request->cancel_reason,
            ]);

            $payment->serviceOrder->update([
                'status' => 'selesai_servis',
            ]);

            AuditLogService::log('Batalkan pembayaran: ' . $request->cancel_reason, 'payments', $payment->id);
        });

        return redirect()->route('payments.show', $payment->serviceOrder)->with('success', 'Pembayaran berhasil dibatalkan. Order dikembalikan ke status siap dibayar.');
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

    private function loadInvoiceData(Payment $payment): void
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
    }

    private function invoiceSettings(): array
    {
        return Setting::group([
            'workshop_name' => 'MotoCare',
            'workshop_tagline' => 'Sistem Informasi Bengkel Motor',
            'workshop_address' => 'Jl. Contoh No. 1, Jakarta',
            'workshop_phone' => '081234567890',
            'workshop_email' => 'admin@motocare.com',
            'workshop_operational_hours' => 'Senin - Sabtu, 08.00 - 17.00',
            'workshop_logo_text' => 'MC',
        ]);
    }
}
