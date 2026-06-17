<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMechanicJobRequest;
use App\Models\ServiceOrder;
use App\Models\Sparepart;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MechanicJobController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-mechanic-jobs');

        $query = ServiceOrder::with(['customer', 'vehicle', 'mechanic', 'services', 'mechanicJob'])
            ->whereNotNull('mechanic_id')
            ->whereIn('status', ['menunggu', 'dikerjakan', 'selesai_servis']);

        if (auth()->user()->hasRole('mekanik')) {
            $query->where('mechanic_id', auth()->id());
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('mechanic-jobs.index', compact('orders'));
    }

    public function show(ServiceOrder $order)
    {
        $this->authorizeView($order);

        $order->load(['customer', 'vehicle', 'serviceAdvisor', 'mechanic', 'services', 'spareparts', 'mechanicJob']);
        $spareparts = Sparepart::where('stock_status', 'tersedia')
            ->orWhereIn('id', $order->spareparts->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('mechanic-jobs.show', compact('order', 'spareparts'));
    }

    public function start(ServiceOrder $order)
    {
        $this->authorizeHandle($order);

        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Order harus berstatus menunggu sebelum mulai dikerjakan.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'dikerjakan']);

            $order->mechanicJob()->updateOrCreate(
                ['service_order_id' => $order->id],
                [
                    'mechanic_id' => auth()->id(),
                    'started_at' => now(),
                ]
            );

            AuditLogService::log('Mulai pengerjaan mekanik', 'mechanic_jobs', $order->id);
        });

        return redirect()->route('mechanic-jobs.show', $order)->with('success', 'Pekerjaan servis dimulai.');
    }

    public function update(StoreMechanicJobRequest $request, ServiceOrder $order)
    {
        $this->saveMechanicJob($request, $order, false);

        return back()->with('success', 'Catatan pengerjaan berhasil disimpan.');
    }

    public function finish(StoreMechanicJobRequest $request, ServiceOrder $order)
    {
        $this->saveMechanicJob($request, $order, true);

        return redirect()->route('mechanic-jobs.index')->with('success', 'Pekerjaan selesai. Order menunggu pembayaran.');
    }

    private function saveMechanicJob(StoreMechanicJobRequest $request, ServiceOrder $order, bool $finish): void
    {
        DB::transaction(function () use ($request, $order, $finish) {
            $order->mechanicJob()->updateOrCreate(
                ['service_order_id' => $order->id],
                [
                    'mechanic_id' => auth()->id(),
                    'inspection_result' => $request->inspection_result,
                    'service_actions' => $request->service_actions,
                    'mechanic_notes' => $request->mechanic_notes,
                    'started_at' => $order->mechanicJob?->started_at ?? now(),
                    'finished_at' => $finish ? now() : $order->mechanicJob?->finished_at,
                ]
            );

            $order->spareparts()->sync($this->sparepartPivotData($request));

            $order->vehicle->update(['last_km' => max($order->vehicle->last_km, $order->current_km)]);

            if ($finish) {
                $order->update(['status' => 'selesai_servis']);
                AuditLogService::log('Input hasil pengerjaan selesai', 'mechanic_jobs', $order->id);
                return;
            }

            AuditLogService::log('Ubah catatan pengerjaan', 'mechanic_jobs', $order->id);
        });
    }

    private function sparepartPivotData(StoreMechanicJobRequest $request): array
    {
        $rows = collect($request->input('spareparts', []))
            ->filter(fn ($row) => !empty($row['id']) && !empty($row['quantity']));

        $spareparts = Sparepart::whereIn('id', $rows->pluck('id'))->get()->keyBy('id');
        $pivotData = [];

        foreach ($rows as $row) {
            $sparepart = $spareparts->get((int) $row['id']);
            if (!$sparepart) {
                continue;
            }

            $pivotData[$sparepart->id] = [
                'quantity' => (int) $row['quantity'],
                'price_at_time' => $sparepart->price,
            ];
        }

        return $pivotData;
    }

    private function authorizeView(ServiceOrder $order): void
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('service_advisor')) {
            return;
        }

        $this->authorizeHandle($order);
    }

    private function authorizeHandle(ServiceOrder $order): void
    {
        abort_unless(
            auth()->user()->hasRole('mekanik') && $order->mechanic_id === auth()->id(),
            403,
            'Anda tidak memiliki akses ke pekerjaan ini.'
        );
    }
}
