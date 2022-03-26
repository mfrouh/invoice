<?php

namespace Tests\Feature\Frontend;

use App\Models\Attribute;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Value;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

    public function test_customer_can_add_or_update_to_cart_with_quantity()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku, 'quantity' => 2])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku, 'quantity' => 5])
            ->assertSuccessful();

        $this->assertDatabaseHas('carts', ['sku' => $product->sku, 'quantity' => 5]);
        $this->assertDatabaseCount('carts', 1);
    }

    public function test_customer_can_add_or_update_to_cart_without_quantity()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku, 'quantity' => 3])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku])
            ->assertSuccessful();

        $this->assertDatabaseHas('carts', ['sku' => $product->sku, 'quantity' => 5]);
        $this->assertDatabaseCount('carts', 1);

        $product1 = Product::factory()->create();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product1->sku, 'quantity' => 6])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product1->sku])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product1->sku])
            ->assertSuccessful();

        $this->assertDatabaseHas('carts', ['sku' => $product1->sku, 'quantity' => 8]);
        $this->assertDatabaseCount('carts', 2);
    }

    public function test_customer_can_add_or_update_to_cart_variant()
    {
        $product = Product::factory()->create();
        Attribute::factory(3)->create(['product_id' => $product->id]);
        Value::factory(3)->create(['attribute_id' => 1]);
        Value::factory(3)->create(['attribute_id' => 2]);
        Value::factory(3)->create(['attribute_id' => 3]);
        $variant = Variant::factory()->create(['product_id' => $product->id, 'sku' => 'p'.$product->id.'_1_4_7']);

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $variant->sku, 'quantity' => 3])
            ->assertSuccessful();

        $this->actingAs($this->customer)
            ->post(route('cart.store'), ['sku' => $product->sku, 'quantity' => 7])
            ->assertSuccessful();

        $this->assertDatabaseHas('carts', ['sku' => $variant->sku, 'variant_id' => $variant->id, 'quantity' => 3]);
        $this->assertDatabaseHas('carts', ['sku' => $product->sku, 'variant_id' => null, 'quantity' => 7]);
        $this->assertDatabaseCount('carts', 2);
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
