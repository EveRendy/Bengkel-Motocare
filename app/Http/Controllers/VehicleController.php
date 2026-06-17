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

    // =========================================================================
    // FITUR KENDARAAN SAYA (KHUSUS PELANGGAN)
    // =========================================================================

    public function myVehicles()
    {
        Gate::authorize('manage-own-vehicles');
        
        $customerId = auth()->user()->customer->id ?? null;
        if (!$customerId) {
            return redirect()->route('dashboard')->with('error', 'Profil pelanggan tidak ditemukan.');
        }

        $vehicles = Vehicle::where('customer_id', $customerId)
            ->with('fileUploads')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('vehicles.my-index', compact('vehicles'));
    }

    public function myCreate()
    {
        Gate::authorize('manage-own-vehicles');
        return view('vehicles.my-create');
    }

    public function myStore(\App\Http\Requests\StoreMyVehicleRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Auto isi customer_id dengan ID pelanggan yang sedang login
            $data['customer_id'] = auth()->user()->customer->id;
            $data['status'] = 'aktif';

            // Pisahkan foto jika ada
            $photo = null;
            if (isset($data['photo'])) {
                $photo = $data['photo'];
                unset($data['photo']);
            }

            $vehicle = Vehicle::create($data);

            if ($photo) {
                $path = $photo->store('vehicles', 'public');
                $vehicle->fileUploads()->create([
                    'file_path' => $path,
                    'file_type' => 'image',
                    'original_name' => $photo->getClientOriginalName()
                ]);
            }

            AuditLogService::log('Tambah kendaraan sendiri', 'vehicles', $vehicle->id);

            return redirect()->route('my-vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan. Data akan diverifikasi saat servis pertama.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambah kendaraan: ' . $e->getMessage());
        }
    }

    public function myShow(Vehicle $vehicle)
    {
        Gate::authorize('view-own-vehicle', $vehicle);
        return view('vehicles.my-show', compact('vehicle'));
    }

    public function myEdit(Vehicle $vehicle)
    {
        Gate::authorize('update-own-vehicle', $vehicle);
        return view('vehicles.my-edit', compact('vehicle'));
    }

    public function myUpdate(\App\Http\Requests\UpdateMyVehicleRequest $request, Vehicle $vehicle)
    {
        try {
            $data = $request->validated();
            
            // Pisahkan foto jika ada
            $photo = null;
            if (isset($data['photo'])) {
                $photo = $data['photo'];
                unset($data['photo']);
            }

            // license_plate sengaja diabaikan karena tidak boleh diubah,
            // validasi di Request juga sudah meng-exclude field tersebut.

            $vehicle->update($data);

            if ($photo) {
                // Hapus foto lama jika ada
                if ($vehicle->fileUploads->count() > 0) {
                    foreach ($vehicle->fileUploads as $file) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->file_path);
                        $file->delete();
                    }
                }

                $path = $photo->store('vehicles', 'public');
                $vehicle->fileUploads()->create([
                    'file_path' => $path,
                    'file_type' => 'image',
                    'original_name' => $photo->getClientOriginalName()
                ]);
            }

            AuditLogService::log('Ubah kendaraan sendiri', 'vehicles', $vehicle->id);

            return redirect()->route('my-vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui kendaraan.');
        }
    }
}