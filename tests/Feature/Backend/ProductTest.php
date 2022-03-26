<?php

namespace Tests\Feature\Backend;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
        Storage::fake('avatars');
        $this->data =
            ['name'           => 'product', 'status' => 1,
                'price'       => 12, 'image' => UploadedFile::fake()->image('avatar.jpg'),
                'description' => 'description the product', 'category_id' => Category::factory()->create()->id, ];
    }

    public function test_get_all_products()
    {
        $this->actingAs($this->admin)
            ->get(route('product.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Product::factory(12)->create();

        $this->actingAs($this->admin)
            ->get(route('product.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_product_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('product.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('product.store'), $this->data)
                ->assertForbidden();

            $product = Product::factory()->create();

            $this->actingAs($user)
                ->get(route('product.show', [$product->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('product.update', [$product->id]), ['name' => 'product2', 'status' => 1])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('product.destroy', [$product->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_product_success()
    {
        $this->actingAs($this->admin)
            ->post(route('product.store'), $this->data)
            ->assertCreated();

        $this->assertDatabaseHas('products', ['id' => 1, 'name' => 'product']);
    }

    public function test_failed_to_create_product()
    {
        $test_data = ['name' => '', 'status' => '', 'price' => '', 'image' => 'string image', 'category_id' => 2, 'description' => ''];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->post(route('product.store'), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }

        Product::create($this->data);

        $this->actingAs($this->admin)
            ->post(route('product.store'), $this->data)
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_product_success()
    {
        $product = Product::create($this->data);

        $this->actingAs($this->admin)
            ->put(route('product.update', [$product->id]), ['name' => 'product4'] + $this->data)
            ->assertSuccessful();

        $this->assertDatabaseHas('products', ['name' => 'product4']);
    }

    public function test_failed_to_update_product()
    {
        $product = Product::create($this->data);

        $test_data = ['name' => '', 'status' => '', 'price' => '', 'image' => 'string image', 'category_id' => 2, 'description' => ''];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->put(route('product.update', [$product->id]), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }

        $this->actingAs($this->admin)
            ->put(route('product.update', [$product->id]), $this->data)
            ->assertSessionHasNoErrors('name');
    }

    public function test_admin_can_show_product_success()
    {
        $product = Product::create($this->data);

        $this->actingAs($this->admin)
            ->get(route('product.show', [$product->id]))
            ->assertJsonPath('data.name', $product->name)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_product_success()
    {
        $product = Product::create($this->data);

        $this->actingAs($this->admin)
            ->delete(route('product.destroy', [$product->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('products', $this->data);
    }
}
