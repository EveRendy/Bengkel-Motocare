<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\FileUpload;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-vehicles');
        $vehicles = Vehicle::with('customer')->orderBy('id', 'desc')->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        Gate::authorize('manage-vehicles');
        $customers = Customer::where('status', 'aktif')->get();
        return view('vehicles.create', compact('customers'));
    }

    public function store(StoreVehicleRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['photo']); // Keluarkan foto dari array create

            $data['status'] = 'aktif';
            $vehicle = Vehicle::create($data);

            if ($request->hasFile('photo')) {
                $this->handlePhotoUpload($request->file('photo'), $vehicle);
            }

            AuditLogService::log('Tambah kendaraan', 'vehicles', $vehicle->id);
            return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan kendaraan.');
        }
    }

    public function show(Vehicle $vehicle)
    {
        Gate::authorize('view-vehicle', $vehicle);
        $vehicle->load(['customer', 'fileUploads']);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        Gate::authorize('manage-vehicles');
        $customers = Customer::where('status', 'aktif')->get();
        return view('vehicles.edit', compact('vehicle', 'customers'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        try {
            $data = $request->validated();
            unset($data['photo']);

            $vehicle->update($data);

            if ($request->hasFile('photo')) {
                $this->handlePhotoUpload($request->file('photo'), $vehicle);
            }

            AuditLogService::log('Ubah kendaraan', 'vehicles', $vehicle->id);
            return redirect()->route('vehicles.index')->with('success', 'Data kendaraan diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function deactivate(Vehicle $vehicle)
    {
        Gate::authorize('manage-vehicles');
        try {
            // Soft deactivate (jika punya relasi, biarkan tetap ada di DB namun status nonaktif)
            $vehicle->update(['status' => 'nonaktif']);
            AuditLogService::log('Nonaktifkan kendaraan', 'vehicles', $vehicle->id);
            return redirect()->route('vehicles.index')->with('success', 'Kendaraan dinonaktifkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menonaktifkan kendaraan.');
        }
    }

    private function handlePhotoUpload($file, $vehicle)
    {
        // Hapus foto lama jika ada
        $oldPhoto = $vehicle->fileUploads()->first();
        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto->path);
            $oldPhoto->delete();
        }

        // Simpan foto baru
        $path = $file->store('vehicles', 'public');
        
        $vehicle->fileUploads()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}