<?php

namespace Tests\Feature\Frontend\Customer;

use App\Mail\InvoiceMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
        $this->seller = User::factory()->create(['role' => 'Seller']);
    }

    public function test_get_all_auth_customer_orders()
    {
        $this->actingAs($this->customer)
            ->get(route('customer.order.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Order::factory(10)->create(['customer_id' => $this->customer]);
        Order::factory(10)->create(['customer_id' => User::factory()->create(['role' => 'Customer'])]);
        $this->actingAs($this->customer)
            ->get(route('customer.order.index'))
            ->assertJsonCount(10, 'data')
            ->assertSuccessful();
        $this->assertDatabaseCount('orders', 20);
    }

    public function test_failed_to_visit_order_because_role()
    {
        $this->actingAs($this->admin)
            ->get(route('customer.order.index'))
            ->assertForbidden();

        $this->actingAs($this->seller)
            ->get(route('customer.order.index'))
            ->assertForbidden();
    }

    public function test_user_can_create_order_success()
    {
        Mail::fake();
        Cart::factory(10)->create(['customer_id' => $this->customer]);
        $this->actingAs($this->customer)
            ->post(route('customer.order.store'),
                ['customer_id' => $this->customer->id,
                    'seller_id' => $this->seller->id, 'total' => 199, 'invoice_qr_code' => $this->faker->uuid])
            ->assertCreated();

        $this->assertDatabaseCount('carts', 0);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_details', 10);
        $this->assertDatabaseCount('invoices', 1);
        Mail::assertSent(InvoiceMail::class, function ($mail) {
            return $mail->hasTo($this->customer->email) ;
        });
    }

}
