<?php

namespace App\Http\Controllers;

use App\Exports\OperationalReportExport;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\MechanicJob;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Sparepart;
use App\Models\Vehicle;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-reports');

        $data = $this->getReportData($request);

        return view('reports.index', $data);
    }

    public function exportExcel(Request $request)
    {
        Gate::authorize('view-reports');

        $data = $this->getReportData($request, null);
        $fileName = 'laporan-motocare-' . $data['dateFrom'] . '-sd-' . $data['dateTo'] . '.xlsx';

        AuditLogService::log('Export laporan Excel', 'reports');

        return Excel::download(new OperationalReportExport($data), $fileName);
    }

    public function exportPdf(Request $request)
    {
        Gate::authorize('view-reports');

        $data = $this->getReportData($request, null);
        $fileName = 'laporan-motocare-' . $data['dateFrom'] . '-sd-' . $data['dateTo'] . '.pdf';

        AuditLogService::log('Export laporan PDF', 'reports');

        return Pdf::loadView('reports.export-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    private function getReportData(Request $request, ?int $limit = 10): array
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $orders = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'payment'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->latest();

        if ($limit) {
            $orders->limit($limit);
        }

        $orders = $orders->get();

        $customers = Customer::withCount(['vehicles', 'serviceOrders'])
            ->latest();

        if ($limit) {
            $customers->limit($limit);
        }

        $customers = $customers->get();

        $vehicles = Vehicle::with('customer')
            ->latest();

        if ($limit) {
            $vehicles->limit($limit);
        }

        $vehicles = $vehicles->get();

        $vehicleServiceHistories = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'mechanicJob'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->whereNotNull('vehicle_id')
            ->latest();

        if ($limit) {
            $vehicleServiceHistories->limit($limit);
        }

        $vehicleServiceHistories = $vehicleServiceHistories->get();

        $payments = Payment::with(['serviceOrder.customer', 'cashier'])
            ->whereDate('payment_date', '>=', $dateFrom)
            ->whereDate('payment_date', '<=', $dateTo)
            ->latest('payment_date');

        if ($limit) {
            $payments->limit($limit);
        }

        $payments = $payments->get();

        $mechanicJobs = MechanicJob::with(['mechanic', 'serviceOrder.customer', 'serviceOrder.vehicle'])
            ->whereHas('serviceOrder', function ($query) use ($dateFrom, $dateTo) {
                $query->whereDate('created_at', '>=', $dateFrom)
                    ->whereDate('created_at', '<=', $dateTo);
            })
            ->latest();

        if ($limit) {
            $mechanicJobs->limit($limit);
        }

        $mechanicJobs = $mechanicJobs->get();

        $usedSpareparts = Sparepart::query()
            ->select('spareparts.id', 'spareparts.part_code', 'spareparts.name')
            ->selectRaw('COALESCE(SUM(service_order_spareparts.quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(service_order_spareparts.quantity * service_order_spareparts.price_at_time), 0) as total_amount')
            ->join('service_order_spareparts', 'spareparts.id', '=', 'service_order_spareparts.sparepart_id')
            ->join('service_orders', 'service_order_spareparts.service_order_id', '=', 'service_orders.id')
            ->whereDate('service_orders.created_at', '>=', $dateFrom)
            ->whereDate('service_orders.created_at', '<=', $dateTo)
            ->groupBy('spareparts.id', 'spareparts.part_code', 'spareparts.name')
            ->orderByDesc('total_quantity');

        if ($limit) {
            $usedSpareparts->limit($limit);
        }

        $usedSpareparts = $usedSpareparts->get();

        $serviceUsage = Service::query()
            ->select('services.id', 'services.service_code', 'services.name')
            ->selectRaw('COUNT(service_order_services.service_order_id) as total_orders')
            ->selectRaw('COALESCE(SUM(service_order_services.price_at_time), 0) as total_amount')
            ->join('service_order_services', 'services.id', '=', 'service_order_services.service_id')
            ->join('service_orders', 'service_order_services.service_order_id', '=', 'service_orders.id')
            ->whereDate('service_orders.created_at', '>=', $dateFrom)
            ->whereDate('service_orders.created_at', '<=', $dateTo)
            ->groupBy('services.id', 'services.service_code', 'services.name')
            ->orderByDesc('total_orders');

        if ($limit) {
            $serviceUsage->limit($limit);
        }

        $serviceUsage = $serviceUsage->get();

        $auditLogs = AuditLog::with('user')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->latest('created_at');

        if ($limit) {
            $auditLogs->limit($limit);
        }

        $auditLogs = $auditLogs->get();

        $summary = [
            'customers' => Customer::count(),
            'vehicles' => Vehicle::count(),
            'orders' => ServiceOrder::whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->count(),
            'completed_orders' => ServiceOrder::where('status', 'selesai')->whereDate('updated_at', '>=', $dateFrom)->whereDate('updated_at', '<=', $dateTo)->count(),
            'mechanic_jobs' => MechanicJob::whereHas('serviceOrder', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo))->count(),
            'payment_total' => Payment::where('status', 'lunas')->whereDate('payment_date', '>=', $dateFrom)->whereDate('payment_date', '<=', $dateTo)->sum('total_amount'),
            'sparepart_usage' => $usedSpareparts->sum('total_quantity'),
            'audit_logs' => AuditLog::whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->count(),
        ];

        return compact(
            'dateFrom',
            'dateTo',
            'summary',
            'orders',
            'customers',
            'vehicles',
            'vehicleServiceHistories',
            'payments',
            'mechanicJobs',
            'usedSpareparts',
            'serviceUsage',
            'auditLogs'
        );
    }

}
