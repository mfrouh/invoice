<?php

namespace Tests\Feature\Backend;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_get_all_attributes()
    {
        $this->actingAs($this->admin)
            ->get(route('attribute.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Attribute::factory(12)->create(['product_id' => Product::factory()->create()->id]);
        $this->actingAs($this->admin)
            ->get(route('attribute.index', ['product_id' => 1]))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_attribute_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('attribute.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('attribute.store'), ['name' => 'attribute'])
                ->assertForbidden();

            $attribute = Attribute::factory()->create();

            $this->actingAs($user)
                ->get(route('attribute.show', [$attribute->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('attribute.update', [$attribute->id]), ['name' => 'attribute2'])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('attribute.destroy', [$attribute->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_attribute_success()
    {
        $this->actingAs($this->admin)
            ->post(route('attribute.store'), ['name' => 'attribute', 'product_id' => Product::factory()->create()->id])
            ->assertCreated();

        $this->assertDatabaseHas('attributes', ['name' => 'attribute']);
    }

    public function test_failed_to_create_attribute()
    {
        $this->actingAs($this->admin)
            ->post(route('attribute.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $attribute = Attribute::create(['product_id' => Product::factory()->create()->id, 'name' => 'attribute']);

        $this->actingAs($this->admin)
            ->post(route('attribute.store'), ['name' => 'attribute', 'product_id' => $attribute->product_id])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->post(route('attribute.store'), ['name' => 'attribute', 'product_id' => 2])
            ->assertSessionHasErrors('product_id');
    }

    public function test_admin_can_update_attribute_success()
    {
        $attribute = Attribute::create(['product_id' => Product::factory()->create()->id, 'name' => 'attribute']);

        $this->actingAs($this->admin)
            ->put(route('attribute.update', [$attribute->id]), ['name' => 'attribute4', 'product_id' => $attribute->product_id])
            ->assertSuccessful();

        $this->assertDatabaseHas('attributes', ['name' => 'attribute4']);
    }

    public function test_failed_to_update_attribute()
    {
        $attribute = Attribute::create(['product_id' => Product::factory()->create()->id, 'name' => 'attribute']);

        $this->actingAs($this->admin)
            ->put(route('attribute.update', [$attribute->id]), ['name' => '', 'product_id' => $attribute->product_id])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->put(route('attribute.update', [$attribute->id]), ['name' => '', 'product_id' => 2])
            ->assertSessionHasErrors('product_id');

        $this->actingAs($this->admin)
            ->put(route('attribute.update', [$attribute->id]), ['name' => 'attribute', 'product_id' => $attribute->product_id])
            ->assertSessionHasNoErrors('name');
    }

    public function test_admin_can_show_attribute_success()
    {
        $attribute = Attribute::create(['product_id' => Product::factory()->create()->id, 'name' => 'attribute']);

        $this->actingAs($this->admin)
            ->get(route('attribute.show', [$attribute->id]))
            ->assertJsonPath('data.name', $attribute->name)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_attribute_success()
    {
        $attribute = Attribute::create(['product_id' => Product::factory()->create()->id, 'name' => 'attribute']);

        $this->actingAs($this->admin)
            ->delete(route('attribute.destroy', [$attribute->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('attributes', ['name' => 'attribute']);
    }
}
