<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-services');
        $services = Service::with('category')->paginate(10);
        $categories = ServiceCategory::all(); // Untuk dropdown di modal/create
        return view('services.index', compact('services', 'categories'));
    }

    public function create()
    {
        Gate::authorize('manage-services');
        return view('services.create');
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();

        $nextId = (Service::max('id') ?? 0) + 1;
        $data['service_code'] = 'SRV-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $data['status'] = 'aktif';

        $service = Service::create($data);
        AuditLogService::log('Tambah layanan', 'services', $service->id);

        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambah.');
    }

    public function edit(Service $service)
    {
        Gate::authorize('manage-services');
        return view('services.edit', compact('service'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();

        if ($service->price != $data['price']) {
            AuditLogService::log('Ubah harga layanan', 'services', $service->id);
        }

        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil diubah.');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('manage-services');

        // Catat log sebelum data dihapus (karena setelah dihapus, id mungkin tidak valid di beberapa log audit)
        AuditLogService::log('Hapus layanan', 'services', $service->id);

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
