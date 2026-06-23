<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional MotoCare</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
        }

        h1, h2 {
            margin: 0;
        }

        h1 {
            color: #111827;
            font-size: 22px;
        }

        h2 {
            color: #111827;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .header {
            border-bottom: 3px solid #ea580c;
            margin-bottom: 14px;
            padding-bottom: 10px;
        }

        .brand {
            color: #ea580c;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .muted {
            color: #6b7280;
        }

        .summary {
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-card {
            border: 1px solid #fed7aa;
            display: table-cell;
            padding: 8px;
            width: 25%;
        }

        .summary-label {
            color: #6b7280;
            font-size: 9px;
        }

        .summary-value {
            color: #111827;
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
        }

        .section {
            margin-top: 14px;
            page-break-inside: avoid;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #ea580c;
            color: #ffffff;
            font-size: 9px;
            padding: 6px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #fff7ed;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">MotoCare</div>
        <h1>Laporan Operasional Bengkel</h1>
        <div class="muted">Periode {{ $dateFrom }} s/d {{ $dateTo }} | Dicetak {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Total Pelanggan</div>
                <div class="summary-value">{{ $summary['customers'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Kendaraan</div>
                <div class="summary-value">{{ $summary['vehicles'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Order Periode Ini</div>
                <div class="summary-value">{{ $summary['orders'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Order Selesai</div>
                <div class="summary-value">{{ $summary['completed_orders'] }}</div>
            </div>
        </div>
        <div class="summary-row">
            <div class="summary-card">
                <div class="summary-label">Pekerjaan Mekanik</div>
                <div class="summary-value">{{ $summary['mechanic_jobs'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Pendapatan</div>
                <div class="summary-value">Rp {{ number_format($summary['payment_total'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Sparepart Terpakai</div>
                <div class="summary-value">{{ $summary['sparepart_usage'] }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Aktivitas User</div>
                <div class="summary-value">{{ $summary['audit_logs'] }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Laporan Data Pelanggan</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No HP</th>
                    <th>Status</th>
                    <th>Kendaraan</th>
                    <th>Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="nowrap">{{ $customer->customer_code }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ ucfirst($customer->status) }}</td>
                    <td class="text-right">{{ $customer->vehicles_count }}</td>
                    <td class="text-right">{{ $customer->service_orders_count }}</td>
                </tr>
                @empty
                <tr><td colspan="7">Belum ada data pelanggan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Laporan Data Kendaraan</h2>
        <table>
            <thead>
                <tr>
                    <th>Plat</th>
                    <th>Pemilik</th>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>Tahun</th>
                    <th>KM</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                <tr>
                    <td class="nowrap">{{ $vehicle->license_plate }}</td>
                    <td>{{ $vehicle->customer->name ?? '-' }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->type }}</td>
                    <td>{{ $vehicle->year }}</td>
                    <td class="text-right">{{ number_format($vehicle->last_km, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($vehicle->status) }}</td>
                </tr>
                @empty
                <tr><td colspan="7">Belum ada data kendaraan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Laporan Order Servis</h2>
        <table>
            <thead>
                <tr>
                    <th>No Order</th>
                    <th>Pelanggan</th>
                    <th>Kendaraan</th>
                    <th>Mekanik</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="nowrap">{{ $order->order_number }}</td>
                    <td>{{ $order->customer->name ?? '-' }}</td>
                    <td>{{ $order->vehicle->license_plate ?? '-' }}</td>
                    <td>{{ $order->mechanic->name ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                    <td class="nowrap">{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6">Belum ada order pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Laporan Riwayat Servis Kendaraan</h2>
        <table>
            <thead>
                <tr>
                    <th>Plat</th>
                    <th>No Order</th>
                    <th>Pelanggan</th>
                    <th>Mekanik</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicleServiceHistories as $history)
                <tr>
                    <td class="nowrap">{{ $history->vehicle->license_plate ?? '-' }}</td>
                    <td class="nowrap">{{ $history->order_number }}</td>
                    <td>{{ $history->customer->name ?? '-' }}</td>
                    <td>{{ $history->mechanic->name ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $history->status)) }}</td>
                    <td class="nowrap">{{ optional($history->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6">Belum ada riwayat servis kendaraan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Laporan Pembayaran</h2>
        <table>
            <thead>
                <tr>
                    <th>No Payment</th>
                    <th>Pelanggan</th>
                    <th>Kasir</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="nowrap">{{ $payment->payment_number }}</td>
                    <td>{{ $payment->serviceOrder->customer->name ?? '-' }}</td>
                    <td>{{ $payment->cashier->name ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</td>
                    <td class="nowrap">{{ optional($payment->payment_date)->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7">Belum ada pembayaran pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Laporan Pengerjaan Mekanik</h2>
        <table>
            <thead>
                <tr>
                    <th>No Order</th>
                    <th>Mekanik</th>
                    <th>Pelanggan</th>
                    <th>Kendaraan</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mechanicJobs as $job)
                <tr>
                    <td class="nowrap">{{ $job->serviceOrder->order_number ?? '-' }}</td>
                    <td>{{ $job->mechanic->name ?? '-' }}</td>
                    <td>{{ $job->serviceOrder->customer->name ?? '-' }}</td>
                    <td>{{ $job->serviceOrder->vehicle->license_plate ?? '-' }}</td>
                    <td class="nowrap">{{ optional($job->started_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="nowrap">{{ optional($job->finished_at)->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6">Belum ada pekerjaan mekanik pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Penggunaan Sparepart</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Sparepart</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usedSpareparts as $sparepart)
                <tr>
                    <td>{{ $sparepart->part_code }}</td>
                    <td>{{ $sparepart->name }}</td>
                    <td class="text-right">{{ $sparepart->total_quantity }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($sparepart->total_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Belum ada penggunaan sparepart pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Layanan Servis Terpakai</h2>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Layanan</th>
                    <th>Jumlah Order</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($serviceUsage as $service)
                <tr>
                    <td>{{ $service->service_code }}</td>
                    <td>{{ $service->name }}</td>
                    <td class="text-right">{{ $service->total_orders }}</td>
                    <td class="text-right nowrap">Rp {{ number_format($service->total_amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Belum ada layanan servis pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Aktivitas User</h2>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Modul</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr>
                    <td class="nowrap">{{ optional($log->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $log->module)) }}</td>
                    <td>{{ $log->activity }}</td>
                </tr>
                @empty
                <tr><td colspan="4">Belum ada aktivitas user pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
