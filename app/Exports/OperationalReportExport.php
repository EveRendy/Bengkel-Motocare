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
