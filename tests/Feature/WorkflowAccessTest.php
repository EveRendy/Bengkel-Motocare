<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Role;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class WorkflowAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_open_reports_audit_logs_and_settings(): void
    {
        $manager = $this->userWithRole('manager');

        $this->actingAs($manager)->get(route('reports.index'))->assertOk();
        $this->actingAs($manager)->get(route('audit-logs.index'))->assertOk();
        $this->actingAs($manager)->get(route('settings.edit'))->assertOk();
    }

    public function test_customer_cannot_open_management_pages(): void
    {
        $customer = $this->userWithRole('pelanggan');

        $this->actingAs($customer)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('audit-logs.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('settings.edit'))->assertForbidden();
    }

    public function test_manager_can_cancel_paid_payment_without_deleting_history(): void
    {
        $manager = $this->userWithRole('manager');
        $cashier = $this->userWithRole('kasir');
        $order = $this->serviceOrder(['status' => 'selesai']);

        $payment = Payment::create([
            'payment_number' => 'PAY-20260621-0001',
            'service_order_id' => $order->id,
            'total_service' => 100000,
            'total_sparepart' => 50000,
            'discount' => 0,
            'total_amount' => 150000,
            'payment_method' => 'cash',
            'status' => 'lunas',
            'payment_date' => now(),
            'cashier_id' => $cashier->id,
        ]);

        $this->actingAs($manager)
            ->patch(route('payments.cancel', $payment), [
                'cancel_reason' => 'Koreksi pembayaran karena nominal salah input.',
            ])
            ->assertRedirect(route('payments.show', $order));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'batal',
            'cancel_reason' => 'Koreksi pembayaran karena nominal salah input.',
        ]);

        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status' => 'selesai_servis',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $manager->id,
            'module' => 'payments',
            'data_id' => (string) $payment->id,
        ]);
    }

    public function test_customer_can_only_see_own_order_detail(): void
    {
        $owner = $this->userWithRole('pelanggan');
        $other = $this->userWithRole('pelanggan', 'other@example.com');
        $ownerCustomer = Customer::create([
            'user_id' => $owner->id,
            'customer_code' => 'CUST-OWN',
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'phone' => '0811111111',
            'address' => 'Alamat Owner',
            'registration_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);
        Customer::create([
            'user_id' => $other->id,
            'customer_code' => 'CUST-OTHER',
            'name' => 'Other',
            'email' => 'other-customer@example.com',
            'phone' => '0822222222',
            'address' => 'Alamat Other',
            'registration_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);

        $order = $this->serviceOrder(['customer_id' => $ownerCustomer->id]);

        $this->actingAs($owner)->get(route('booking.show', $order))->assertOk();
        $this->actingAs($other)->get(route('booking.show', $order))->assertForbidden();
    }

    private function userWithRole(string $roleName, ?string $email = null): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        $user = User::create([
            'name' => ucfirst(str_replace('_', ' ', $roleName)),
            'email' => $email ?? $roleName . '@example.com',
            'password' => 'password',
            'status' => 'aktif',
        ]);

        $user->roles()->attach($role->id);

        return $user;
    }

    private function serviceOrder(array $overrides = []): ServiceOrder
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-' . fake()->unique()->numberBetween(1000, 9999),
            'name' => 'Pelanggan Test',
            'email' => fake()->unique()->safeEmail(),
            'phone' => '081234567890',
            'address' => 'Alamat Test',
            'registration_date' => now()->toDateString(),
            'status' => 'aktif',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'license_plate' => 'B' . fake()->unique()->numberBetween(1000, 9999) . 'TES',
            'brand' => 'Honda',
            'type' => 'Beat',
            'year' => 2022,
            'color' => 'Hitam',
            'last_km' => 10000,
            'status' => 'aktif',
        ]);

        return ServiceOrder::create(array_merge([
            'order_number' => 'ORD-' . fake()->unique()->numberBetween(1000, 9999),
            'order_date' => now(),
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'complaint' => 'Mesin berisik',
            'current_km' => 10000,
            'estimated_cost' => 100000,
            'status' => 'selesai_servis',
        ], $overrides));
    }
}
