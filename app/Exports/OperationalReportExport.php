<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OperationalReportExport implements WithMultipleSheets
{
    public function __construct(private array $data)
    {
    }

    public function sheets(): array
    {
        return [
            new ReportSheet(
                'Ringkasan',
                'Ringkasan Laporan Operasional',
                $this->period(),
                ['Metrik', 'Nilai'],
                [
                    ['Total Pelanggan', $this->data['summary']['customers']],
                    ['Total Kendaraan', $this->data['summary']['vehicles']],
                    ['Order Periode Ini', $this->data['summary']['orders']],
                    ['Order Selesai', $this->data['summary']['completed_orders']],
                    ['Pekerjaan Mekanik', $this->data['summary']['mechanic_jobs']],
                    ['Pendapatan', $this->data['summary']['payment_total']],
                    ['Sparepart Terpakai', $this->data['summary']['sparepart_usage']],
                    ['Aktivitas User', $this->data['summary']['audit_logs']],
                ],
                ['B' => '#,##0']
            ),
            new ReportSheet(
                'Pelanggan',
                'Laporan Data Pelanggan',
                $this->period(),
                ['Kode', 'Nama', 'Email', 'No HP', 'Status', 'Jumlah Kendaraan', 'Jumlah Order'],
                $this->data['customers']->map(fn ($customer) => [
                    $customer->customer_code,
                    $customer->name,
                    $customer->email ?? '-',
                    $customer->phone,
                    ucfirst($customer->status),
                    (int) $customer->vehicles_count,
                    (int) $customer->service_orders_count,
                ])->all(),
                ['F' => '#,##0', 'G' => '#,##0']
            ),
            new ReportSheet(
                'Kendaraan',
                'Laporan Data Kendaraan',
                $this->period(),
                ['Plat Nomor', 'Pemilik', 'Merek', 'Tipe', 'Tahun', 'Warna', 'KM Terakhir', 'Status'],
                $this->data['vehicles']->map(fn ($vehicle) => [
                    $vehicle->license_plate,
                    $vehicle->customer->name ?? '-',
                    $vehicle->brand,
                    $vehicle->type,
                    $vehicle->year,
                    $vehicle->color,
                    (int) $vehicle->last_km,
                    ucfirst($vehicle->status),
                ])->all(),
                ['G' => '#,##0']
            ),
            new ReportSheet(
                'Order Servis',
                'Laporan Order Servis',
                $this->period(),
                ['No Order', 'Pelanggan', 'Kendaraan', 'Mekanik', 'Status', 'Tanggal'],
                $this->data['orders']->map(fn ($order) => [
                    $order->order_number,
                    $order->customer->name ?? '-',
                    $order->vehicle->license_plate ?? '-',
                    $order->mechanic->name ?? '-',
                    ucfirst(str_replace('_', ' ', $order->status)),
                    optional($order->created_at)->format('d/m/Y H:i'),
                ])->all()
            ),
            new ReportSheet(
                'Riwayat Servis',
                'Laporan Riwayat Servis Kendaraan',
                $this->period(),
                ['Plat Nomor', 'No Order', 'Pelanggan', 'Mekanik', 'Keluhan', 'Status', 'Tanggal'],
                $this->data['vehicleServiceHistories']->map(fn ($order) => [
                    $order->vehicle->license_plate ?? '-',
                    $order->order_number,
                    $order->customer->name ?? '-',
                    $order->mechanic->name ?? '-',
                    $order->complaint,
                    ucfirst(str_replace('_', ' ', $order->status)),
                    optional($order->created_at)->format('d/m/Y H:i'),
                ])->all()
            ),
            new ReportSheet(
                'Pembayaran',
                'Laporan Pembayaran',
                $this->period(),
                ['No Payment', 'Pelanggan', 'Kasir', 'Metode', 'Status', 'Total', 'Tanggal'],
                $this->data['payments']->map(fn ($payment) => [
                    $payment->payment_number,
                    $payment->serviceOrder->customer->name ?? '-',
                    $payment->cashier->name ?? '-',
                    ucfirst(str_replace('_', ' ', $payment->payment_method)),
                    ucfirst($payment->status),
                    (float) $payment->total_amount,
                    optional($payment->payment_date)->format('d/m/Y H:i'),
                ])->all(),
                ['F' => '"Rp" #,##0']
            ),
            new ReportSheet(
                'Pengerjaan Mekanik',
                'Laporan Pengerjaan Mekanik',
                $this->period(),
                ['No Order', 'Mekanik', 'Pelanggan', 'Kendaraan', 'Mulai', 'Selesai'],
                $this->data['mechanicJobs']->map(fn ($job) => [
                    $job->serviceOrder->order_number ?? '-',
                    $job->mechanic->name ?? '-',
                    $job->serviceOrder->customer->name ?? '-',
                    $job->serviceOrder->vehicle->license_plate ?? '-',
                    optional($job->started_at)->format('d/m/Y H:i') ?? '-',
                    optional($job->finished_at)->format('d/m/Y H:i') ?? '-',
                ])->all()
            ),
            new ReportSheet(
                'Sparepart',
                'Laporan Penggunaan Sparepart',
                $this->period(),
                ['Kode', 'Sparepart', 'Qty', 'Total'],
                $this->data['usedSpareparts']->map(fn ($sparepart) => [
                    $sparepart->part_code,
                    $sparepart->name,
                    (int) $sparepart->total_quantity,
                    (float) $sparepart->total_amount,
                ])->all(),
                ['C' => '#,##0', 'D' => '"Rp" #,##0']
            ),
            new ReportSheet(
                'Layanan',
                'Laporan Layanan Servis',
                $this->period(),
                ['Kode', 'Layanan', 'Jumlah Order', 'Total'],
                $this->data['serviceUsage']->map(fn ($service) => [
                    $service->service_code,
                    $service->name,
                    (int) $service->total_orders,
                    (float) $service->total_amount,
                ])->all(),
                ['C' => '#,##0', 'D' => '"Rp" #,##0']
            ),
            new ReportSheet(
                'Aktivitas User',
                'Laporan Aktivitas User',
                $this->period(),
                ['Waktu', 'User', 'Modul', 'Aktivitas'],
                $this->data['auditLogs']->map(fn ($log) => [
                    optional($log->created_at)->format('d/m/Y H:i') ?? '-',
                    $log->user->name ?? 'System',
                    ucfirst(str_replace('_', ' ', $log->module)),
                    $log->activity,
                ])->all()
            ),
        ];
    }

    private function period(): string
    {
        return 'Periode ' . $this->data['dateFrom'] . ' s/d ' . $this->data['dateTo'];
    }
}
