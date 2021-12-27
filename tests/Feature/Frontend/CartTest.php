<?php

namespace Tests\Feature\Frontend;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_customer_can_see_his_cart()
    {
        $this->actingAs($this->customer)
            ->get(route('cart.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Cart::factory(10)->create(['customer_id' => $this->customer]);
        Cart::factory(10)->create(['customer_id' => User::factory()->create(['role' => 'Customer'])]);

        $this->actingAs($this->customer)
            ->get(route('cart.index'))
            ->assertJsonCount(10, 'data')
            ->assertSuccessful();
        $this->assertDatabaseCount('carts', 20);

    }

    public function test_customer_can_add_or_update_to_cart()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertCreated();

        $this->assertDatabaseHas('carts', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseCount('carts', 1);

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['product_id' => $product->id])
            ->assertCreated();

        $this->assertDatabaseHas('carts', ['product_id' => $product->id, 'quantity' => 1]);
        $this->assertDatabaseCount('carts', 1);

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['product_id' => $product->id])
            ->assertCreated();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 4])
            ->assertCreated();

        $this->assertDatabaseHas('carts', ['product_id' => $product->id, 'quantity' => 4]);
        $this->assertDatabaseCount('carts', 1);
    }

    public function test_customer_can_delete_product_from_cart()
    {
        $cart = Cart::factory()->create(['customer_id' => $this->customer]);

        $this->actingAs($this->customer)
            ->delete(route('cart.destroy', [$cart->id]))
            ->assertSuccessful();

        $this->assertDatabaseCount('carts', 0);
    }

    public function test_customer_can_clear_cart()
    {
        Cart::factory(10)->create(['customer_id' => $this->customer]);
        Cart::factory(10)->create(['customer_id' => User::factory()->create(['role' => 'Customer'])]);

        $this->actingAs($this->customer)
            ->delete(route('cart.clear'))
            ->assertSuccessful();

        $this->assertDatabaseCount('carts', 10);
    }

}
