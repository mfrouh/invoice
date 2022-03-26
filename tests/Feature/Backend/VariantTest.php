<?php

namespace Tests\Feature\Backend;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\User;
use App\Models\Value;
use App\Models\Variant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_get_all_variants()
    {
        $this->actingAs($this->admin)
            ->get(route('variant.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Variant::factory(12)->create(['product_id' => Product::factory()->create()->id]);

        $this->actingAs($this->admin)
            ->get(route('variant.index', ['product_id' => 1]))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_variant_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('variant.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('variant.store'), ['price' => 45, 'quantity' => 27])
                ->assertForbidden();

            $variant = Variant::factory()->create();

            $this->actingAs($user)
                ->get(route('variant.show', [$variant->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('variant.update', [$variant->id]), ['sku' => 'variant2'])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('variant.destroy', [$variant->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_variant_success()
    {
        $attribute = Attribute::factory()->create(['product_id' => Product::factory()->create()->id]);
        $value = Value::factory()->create(['attribute_id' => $attribute->id]);
        $this->actingAs($this->admin)
            ->post(route('variant.store'), ["$value->id" => $value->id, 'price' => 45,
                'quantity'                               => 27, 'product_id' => $attribute->product_id, ])
            ->assertCreated();

        $this->assertDatabaseHas('variants', ['price' => 45, 'quantity' => 27]);
    }

    public function test_failed_to_create_variant()
    {
        $this->actingAs($this->admin)
            ->post(route('variant.store'), ['quantity' => ''])
            ->assertSessionHasErrors('quantity');

        $variant = Variant::create(['sku' => 'p1_1', 'product_id' => Product::factory()->create()->id, 'price' => 45, 'quantity' => 27]);

        $this->actingAs($this->admin)
            ->post(route('variant.store'), ['price' => '', 'quantity' => 27, 'product_id' => $variant->product_id])
            ->assertSessionHasErrors('price');

        $this->actingAs($this->admin)
            ->post(route('variant.store'), ['price' => 45, 'quantity' => 27, 'product_id' => 2])
            ->assertSessionHasErrors('product_id');
    }

    public function test_admin_can_update_variant_success()
    {
        $variant = Variant::create(['product_id' => Product::factory()->create()->id, 'sku' => 'p1_1', 'price' => 45, 'quantity' => 2]);

        $this->actingAs($this->admin)
            ->put(route('variant.update', [$variant->id]), ['sku' => 'variant4', 'price' => 45, 'quantity' => 27, 'product_id' => $variant->product_id])
            ->assertSuccessful();

        $this->assertDatabaseHas('variants', ['sku' => 'variant4']);
    }

    public function test_failed_to_update_variant()
    {
        $variant = Variant::create(['product_id' => Product::factory()->create()->id, 'sku' => 'v1', 'price' => 45, 'quantity' => 27]);

        $this->actingAs($this->admin)
            ->put(route('variant.update', [$variant->id]), ['sku' => '', 'product_id' => $variant->product_id])
            ->assertSessionHasErrors('sku');

        $this->actingAs($this->admin)
            ->put(route('variant.update', [$variant->id]), ['product_id' => 2])
            ->assertSessionHasErrors('product_id');

        $this->actingAs($this->admin)
            ->put(route('variant.update', [$variant->id]), ['price' => 45, 'quantity' => 27, 'product_id' => $variant->product_id])
            ->assertSessionHasErrors('sku');
    }

    public function test_admin_can_destroy_variant_success()
    {
        $variant = Variant::create(['product_id' => Product::factory()->create()->id, 'sku' => 'v1', 'price' => 45, 'quantity' => 27]);

        $this->actingAs($this->admin)
            ->delete(route('variant.destroy', [$variant->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('variants', ['price' => 45, 'quantity' => 27]);
    }
}
