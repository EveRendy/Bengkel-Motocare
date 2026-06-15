<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        Gate::authorize('manage-services');
        $data = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        $nextId = (Service::max('id') ?? 0) + 1;
        $data['service_code'] = 'SRV-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $data['status'] = 'aktif';

        $service = Service::create($data);
        AuditLogService::log('Tambah layanan', 'services', $service->id);

        return back()->with('success', 'Layanan berhasil ditambah.');
    }

    public function update(Request $request, Service $service)
    {
        Gate::authorize('manage-services');
        $data = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        if ($service->price != $data['price']) {
            AuditLogService::log('Ubah harga layanan', 'services', $service->id);
        }

        $service->update($data);
        return back()->with('success', 'Layanan berhasil diubah.');
    }
}