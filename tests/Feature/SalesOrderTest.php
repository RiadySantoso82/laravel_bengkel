<?php

namespace Tests\Feature;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\SalesOrder as SalesOrderAlias;
use App\Models\Customer;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Sparepart;
use App\Models\StockBatch;
use App\Models\PaymentTransaction;
use App\Models\SparepartCategory;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'username' => 'admin', 'name' => 'Admin', 'email' => 'admin@test.com',
            'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $this->paymentMethod = PaymentMethod::create(['name' => 'Tunai', 'is_active' => true]);

        $cat = SparepartCategory::create(['name' => 'Test' ]);
        $unit = Unit::create(['name' => 'Pcs', 'symbol' => 'pcs']);

        $this->sparepart = Sparepart::create([
            'code' => 'TST-001',
            'name' => 'Part Test',
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'buy_price' => 5000,
            'sell_price' => 10000,
            'min_stock' => 2,
        ]);

        StockBatch::create([
            'part_id' => $this->sparepart->id,
            'qty_in' => 10,
            'qty_remaining' => 10,
            'buy_price' => 5000,
            'received_date' => now(),
        ]);
    }

    public function test_sales_order_store_paid_reduces_stock()
    {
        $response = $this->actingAs($this->admin)->post(route('sales-orders.store'), [
            'customer_id' => '',
            'payment_method_id' => $this->paymentMethod->id,
            'payment_status' => 'paid',
            'total_amount' => 10000,
            'discount' => 0,
            'items' => json_encode([[
                'part_id' => $this->sparepart->id,
                'qty' => 2,
                'sell_price' => 10000,
            ]]),
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('sales_orders', ['payment_status' => 'paid']);
        $this->assertDatabaseHas('sales_order_details', ['part_id' => $this->sparepart->id, 'qty' => 2]);

        $this->assertEquals(8, $this->sparepart->fresh()->stock_qty);
    }

    public function test_sales_order_store_pending_no_stock_change()
    {
        $response = $this->actingAs($this->admin)->post(route('sales-orders.store'), [
            'customer_id' => '',
            'payment_method_id' => '',
            'payment_status' => 'pending',
            'total_amount' => 10000,
            'discount' => 0,
            'items' => json_encode([[
                'part_id' => $this->sparepart->id,
                'qty' => 2,
                'sell_price' => 10000,
            ]]),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('sales_orders', ['payment_status' => 'pending']);
        $this->assertEquals(10, $this->sparepart->fresh()->stock_qty);
    }

    public function test_sales_order_process_payment()
    {
        $so = SalesOrderAlias::create([
            'customer_id' => null,
            'user_id' => $this->admin->id,
            'total_amount' => 10000,
            'discount' => 0,
            'payment_status' => 'pending',
        ]);

        SalesOrderDetail::create([
            'sales_order_id' => $so->id,
            'part_id' => $this->sparepart->id,
            'qty' => 3,
            'sell_price' => 10000,
            'cost_price' => 0,
        ]);

        $response = $this->actingAs($this->admin)->post(route('sales-orders.process-payment', $so), [
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('paid', $so->fresh()->payment_status);
        $this->assertEquals(7, $this->sparepart->fresh()->stock_qty);
    }
}
