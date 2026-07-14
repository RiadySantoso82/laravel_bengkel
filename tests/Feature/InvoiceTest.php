<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Models\Customer;
use App\Models\User;
use App\Models\Mechanic;
use App\Models\Vehicle;
use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\Unit;
use App\Models\ServiceType;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Tunai',
            'is_active' => true,
        ]);
    }

    public function test_invoice_store_with_payment()
    {
        $customer = Customer::create(['name' => 'Test Customer', 'phone' => '08123456789']);
        $mechanic = Mechanic::create(['name' => 'Mekanik Test', 'status' => 'active']);
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'plate_number' => 'B 1234 CD',
            'brand' => 'Honda',
            'model' => 'Vario',
        ]);

        $so = ServiceOrder::create([
            'vehicle_id' => $vehicle->id,
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'complaint' => 'Test komplain',
            'status' => 'done',
        ]);

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'order_id' => $so->id,
            'total_amount' => 150000,
            'discount' => 5000,
            'payment_method_id' => $this->paymentMethod->id,
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect(route('invoices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'order_id' => $so->id,
            'total_amount' => 150000,
            'discount' => 5000,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'reference_type' => 'invoice',
            'reference_id' => Invoice::first()->id,
            'payment_method_id' => $this->paymentMethod->id,
        ]);
    }

    public function test_invoice_store_pending_no_payment()
    {
        $customer = Customer::create(['name' => 'Test']);
        $mechanic = Mechanic::create(['name' => 'Mek', 'status' => 'active']);
        $vehicle = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 1 A', 'brand' => 'X', 'model' => 'Y']);

        $so = ServiceOrder::create([
            'vehicle_id' => $vehicle->id,
            'customer_id' => $customer->id,
            'mechanic_id' => $mechanic->id,
            'complaint' => 'test',
            'status' => 'done',
        ]);

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'order_id' => $so->id,
            'total_amount' => 50000,
            'discount' => 0,
            'payment_method_id' => '',
            'payment_status' => 'pending',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('invoices', ['order_id' => $so->id, 'payment_status' => 'pending']);
        $this->assertDatabaseMissing('payment_transactions', ['reference_type' => 'invoice']);
    }

    public function test_invoice_update_change_to_paid()
    {
        $customer = Customer::create(['name' => 'Test']);
        $mechanic = Mechanic::create(['name' => 'Mek', 'status' => 'active']);
        $vehicle = Vehicle::create(['customer_id' => $customer->id, 'plate_number' => 'B 2 B', 'brand' => 'A', 'model' => 'B']);
        $so = ServiceOrder::create(['vehicle_id' => $vehicle->id, 'customer_id' => $customer->id, 'mechanic_id' => $mechanic->id, 'complaint' => 'test', 'status' => 'done']);

        $inv = Invoice::create([
            'order_id' => $so->id,
            'total_amount' => 100000,
            'discount' => 0,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->put(route('invoices.update', $inv), [
            'order_id' => $so->id,
            'total_amount' => 100000,
            'discount' => 0,
            'payment_method_id' => $this->paymentMethod->id,
            'payment_status' => 'paid',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('invoices', ['id' => $inv->id, 'payment_status' => 'paid']);
        $this->assertDatabaseHas('payment_transactions', [
            'reference_type' => 'invoice',
            'reference_id' => $inv->id,
        ]);
    }
}
