<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $payment->invoice->invoice_number ?? '-' }}</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            border-bottom: 3px solid #ea580c;
            padding-bottom: 16px;
        }

        .brand {
            display: table;
            width: 100%;
        }

        .brand-left,
        .brand-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .brand-right {
            text-align: right;
        }

        .logo {
            background: #ea580c;
            border-radius: 8px;
            color: #fff;
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            height: 46px;
            line-height: 46px;
            margin-right: 10px;
            text-align: center;
            width: 46px;
        }

        .brand-name {
            color: #111827;
            font-size: 24px;
            font-weight: bold;
        }

        .muted {
            color: #6b7280;
        }

        .grid {
            display: table;
            margin-top: 22px;
            width: 100%;
        }

        .col {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .section {
            margin-top: 24px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #ea580c;
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        .right {
            text-align: right;
        }

        .total-box {
            margin-left: auto;
            margin-top: 18px;
            width: 330px;
        }

        .grand-total {
            background: #fff7ed;
            font-size: 16px;
            font-weight: bold;
        }

        .status {
            border: 1px solid #16a34a;
            color: #16a34a;
            display: inline-block;
            font-weight: bold;
            padding: 4px 10px;
            text-transform: uppercase;
        }

        .status-batal {
            border-color: #dc2626;
            color: #dc2626;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            margin-top: 28px;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <div class="brand-left">
                <span class="logo">{{ $settings['workshop_logo_text'] ?? 'MC' }}</span>
                <span class="brand-name">{{ $settings['workshop_name'] ?? 'MotoCare' }}</span>
                <p class="muted">{{ $settings['workshop_tagline'] ?? 'Sistem Informasi Bengkel Motor' }}</p>
                <p class="muted">{{ $settings['workshop_address'] ?? '-' }}</p>
                <p class="muted">{{ $settings['workshop_phone'] ?? '-' }} | {{ $settings['workshop_email'] ?? '-' }}</p>
            </div>
            <div class="brand-right">
                <h1>INVOICE</h1>
                <p><strong>{{ $payment->invoice->invoice_number ?? '-' }}</strong></p>
                <p class="muted">{{ $payment->payment_date?->format('d M Y H:i') ?? '-' }}</p>
                <p style="margin-top: 8px;">
                    <span class="status {{ $payment->status === 'batal' ? 'status-batal' : '' }}">{{ $payment->status }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="col">
            <h3>Ditagihkan Kepada</h3>
            <p>{{ $order->customer->name ?? '-' }}</p>
            <p class="muted">{{ $order->customer->phone ?? '-' }}</p>
            <p class="muted">{{ $order->customer->address ?? '-' }}</p>
        </div>
        <div class="col right">
            <h3>Detail Order</h3>
            <p>{{ $order->order_number }}</p>
            <p class="muted">{{ $order->vehicle->license_plate ?? '-' }} - {{ $order->vehicle->brand ?? '-' }} {{ $order->vehicle->type ?? '' }}</p>
            <p class="muted">Kasir: {{ $payment->cashier->name ?? '-' }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Rincian Layanan</h3>
        <table style="margin-top: 8px;">
            <thead>
                <tr>
                    <th>Layanan</th>
                    <th class="right">Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->services as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td class="right">Rp {{ number_format($service->pivot->price_at_time, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2">Tidak ada layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Rincian Sparepart</h3>
        <table style="margin-top: 8px;">
            <thead>
                <tr>
                    <th>Sparepart</th>
                    <th class="right">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->spareparts as $sparepart)
                <tr>
                    <td>{{ $sparepart->name }}</td>
                    <td class="right">{{ $sparepart->pivot->quantity }}</td>
                    <td class="right">Rp {{ number_format($sparepart->pivot->price_at_time, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($sparepart->pivot->price_at_time * $sparepart->pivot->quantity, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Tidak ada sparepart.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <table class="total-box">
        <tr>
            <td>Total Jasa</td>
            <td class="right">Rp {{ number_format($payment->total_service, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Sparepart</td>
            <td class="right">Rp {{ number_format($payment->total_sparepart, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right">Rp {{ number_format($payment->discount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total Bayar</td>
            <td class="right">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Metode</td>
            <td class="right">{{ strtoupper($payment->payment_method) }}</td>
        </tr>
    </table>

    @if($payment->status === 'batal')
    <div class="section">
        <h3>Alasan Pembatalan</h3>
        <p class="muted">{{ $payment->cancel_reason ?? '-' }}</p>
    </div>
    @endif

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem {{ $settings['workshop_name'] ?? 'MotoCare' }}.
        Jam operasional: {{ $settings['workshop_operational_hours'] ?? '-' }}.
    </div>
</body>
</html>
