<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-customers');
        $customers = Customer::orderBy('id', 'desc')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        Gate::authorize('manage-customers');
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $lastCustomer = Customer::orderBy('id', 'desc')->first();
            $nextId = $lastCustomer ? $lastCustomer->id + 1 : 1;
            $customerCode = 'CUST-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $data = $request->validated();
            $data['customer_code'] = $customerCode;
            $data['status'] = 'aktif';

            $customer = Customer::create($data);

            AuditLogService::log('Tambah pelanggan', 'customers', $customer->id);

            return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function show(Customer $customer)
    {
        Gate::authorize('view-customer', $customer);
        $customer->load(['vehicles', 'serviceOrders']); // Load relasi
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        Gate::authorize('manage-customers');
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            $customer->update($request->validated());
            AuditLogService::log('Ubah pelanggan', 'customers', $customer->id);
            return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function deactivate(Customer $customer)
    {
        Gate::authorize('manage-customers');
        try {
            $customer->update(['status' => 'nonaktif']);
            AuditLogService::log('Nonaktifkan pelanggan', 'customers', $customer->id);
            return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menonaktifkan pelanggan.');
        }
    }
}