<?php

namespace App\Http\Controllers;

use App\Models\MechanicJob;
use App\Models\Sparepart;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MechanicJobController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-mechanic-jobs');

        $query = \App\Models\MechanicJob::with(['serviceOrder.customer', 'serviceOrder.vehicle', 'mechanic']);

        // Jika mekanik, tampilkan job berstatus aktif miliknya
        if (auth()->user()->hasRole('mekanik')) {
            $query->where('mechanic_id', auth()->id())
                  ->whereHas('serviceOrder', function($q) {
                      $q->whereIn('status', ['menunggu', 'dikerjakan', 'selesai_servis']);
                  });
        } 
        // Jika Admin/SA dan memfilter berdasarkan mekanik
        elseif ($request->has('mechanic_id') && $request->mechanic_id != '') {
            $query->where('mechanic_id', $request->mechanic_id);
        }

        $jobs = $query->latest('created_at')->paginate(10);
        
        $mechanics = [];
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('service_advisor')) {
            $mechanics = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'mekanik'))->get();
        }

        return view('mechanic-jobs.index', compact('jobs', 'mechanics'));
    }

    public function show(MechanicJob $job)
    {
        Gate::authorize('view-mechanic-job', $job);
        
        $job->load(['serviceOrder.customer', 'serviceOrder.vehicle', 'serviceOrder.services', 'serviceOrder.spareparts']);
        $spareparts = Sparepart::where('stock_status', 'tersedia')->get();

        return view('mechanic-jobs.show', compact('job', 'spareparts'));
    }

    public function edit(MechanicJob $job)
    {
        Gate::authorize('update-mechanic-job', $job);
        $job->load(['serviceOrder.vehicle', 'serviceOrder.customer']);

        return view('mechanic-jobs.edit', compact('job'));
    }

    public function update(\App\Http\Requests\StoreMechanicJobRequest $request, MechanicJob $job)
    {
        Gate::authorize('update-mechanic-job', $job);
        
        try {
            $job->update($request->validated());
            AuditLogService::log('Ubah catatan pengerjaan', 'mechanic_jobs', $job->id);

            return redirect()->route('mechanic-jobs.show', $job)->with('success', 'Hasil pemeriksaan dan tindakan servis berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan catatan: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, MechanicJob $job)
    {
        Gate::authorize('complete-mechanic-job', $job);

        // Validasi ekstra: tidak bisa diselesaikan jika hasil pemeriksan kosong
        if (empty($job->inspection_result) || empty($job->service_actions)) {
            return back()->with('error', 'Harap isi Hasil Pemeriksaan dan Tindakan Servis terlebih dahulu sebelum menandai selesai.');
        }

        try {
            $job->update(['finished_at' => now()]);
            $job->serviceOrder->update(['status' => 'selesai_servis']);

            AuditLogService::log('Selesai pengerjaan', 'mechanic_jobs', $job->id);

            return redirect()->route('mechanic-jobs.index')->with('success', 'Pengerjaan selesai. Status order dilanjutkan ke tahap pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan pengerjaan: ' . $e->getMessage());
        }
    }

    public function storeSparepart(\App\Http\Requests\StoreMechanicJobSparepartRequest $request, MechanicJob $job)
    {
        Gate::authorize('update-mechanic-job', $job);

        try {
            $data = $request->validated();
            $sparepart = Sparepart::findOrFail($data['sparepart_id']);

            // Attach ke pivot table
            $job->serviceOrder->spareparts()->attach($sparepart->id, [
                'quantity' => $data['quantity'],
                'price_at_time' => $sparepart->price
            ]);

            AuditLogService::log('Input sparepart digunakan (ID: '.$sparepart->id.', Qty: '.$data['quantity'].')', 'mechanic_jobs', $job->id);

            return back()->with('success', 'Sparepart berhasil ditambahkan ke pengerjaan ini.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan sparepart.');
        }
    }

    public function removeSparepart(Request $request, MechanicJob $job, $sparepartId)
    {
        Gate::authorize('update-mechanic-job', $job);

        try {
            // Detach menggunakan ID pada tabel relasi
            $job->serviceOrder->spareparts()->wherePivot('id', $request->pivot_id)->detach($sparepartId);

            AuditLogService::log('Hapus sparepart dari pengerjaan (ID: '.$sparepartId.')', 'mechanic_jobs', $job->id);

            return back()->with('success', 'Sparepart berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus sparepart.');
        }
    }

    public function startJob(\App\Models\MechanicJob $job)
    {
        // Gunakan view-mechanic-job karena update-mechanic-job mensyaratkan status 'dikerjakan'
        Gate::authorize('view-mechanic-job', $job);

        if (auth()->id() !== $job->mechanic_id) {
            abort(403, 'Anda bukan mekanik yang ditugaskan untuk order ini.');
        }

        if ($job->serviceOrder->status !== 'menunggu') {
            return back()->with('error', 'Status order tidak valid untuk dimulai.');
        }

        if ($job->started_at !== null) {
            return back()->with('error', 'Pekerjaan ini sudah dimulai sebelumnya.');
        }

        try {
            $job->update(['started_at' => now()]);
            $job->serviceOrder->update(['status' => 'dikerjakan']);

            \App\Services\AuditLogService::log('Mekanik mulai pengerjaan', 'mechanic_jobs', $job->id);

            return redirect()->route('mechanic-jobs.show', $job)->with('success', 'Pengerjaan dimulai. Waktu mulai telah dicatat. Silakan lakukan pemeriksaan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memulai pengerjaan: ' . $e->getMessage());
        }
    }
}