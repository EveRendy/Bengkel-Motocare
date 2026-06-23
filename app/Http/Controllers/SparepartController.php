<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSparepartRequest;
use App\Http\Requests\UpdateSparepartRequest;
use App\Http\Requests\UpdateSparepartStatusRequest;
use App\Models\Sparepart;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;

class SparepartController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-spareparts');
        $spareparts = Sparepart::orderBy('id', 'desc')->paginate(10);
        return view('spareparts.index', compact('spareparts'));
    }

    public function create()
    {
        Gate::authorize('manage-spareparts');
        return view('spareparts.create');
    }

    public function store(StoreSparepartRequest $request)
    {
        $data = $request->validated();

        $nextId = (Sparepart::max('id') ?? 0) + 1;
        $data['part_code'] = 'SPR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $data['stock_status'] = 'tersedia';

        $sp = Sparepart::create($data);
        AuditLogService::log('Tambah sparepart', 'spareparts', $sp->id);

        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil ditambah.');
    }

    public function edit(Sparepart $sparepart)
    {
        Gate::authorize('manage-spareparts');
        return view('spareparts.edit', compact('sparepart'));
    }

    public function update(UpdateSparepartRequest $request, Sparepart $sparepart)
    {
        $data = $request->validated();

        if ($sparepart->price != $data['price']) {
            AuditLogService::log('Ubah harga sparepart', 'spareparts', $sparepart->id);
        }

        $sparepart->update($data);
        return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil diubah.');
    }

    public function updateStatus(UpdateSparepartStatusRequest $request, Sparepart $sparepart)
    {
        $data = $request->validated();
        $sparepart->update($data);
        AuditLogService::log('Ubah status sparepart', 'spareparts', $sparepart->id);
        
        return back()->with('success', 'Status sparepart diupdate.');
    }

    public function destroy(Sparepart $sparepart)
    {
        Gate::authorize('manage-spareparts');

        try {
            $sparepart->delete();
            AuditLogService::log('Hapus sparepart', 'spareparts', $sparepart->id);

            return redirect()->route('spareparts.index')->with('success', 'Sparepart berhasil dihapus.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus sparepart. Silakan coba lagi.');
        }
    }
}
