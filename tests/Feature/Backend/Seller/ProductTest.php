<?php

namespace Tests\Feature\Backend\Seller;

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
        $this->seller = User::factory()->create(['role' => 'Seller']);
        Storage::fake('avatars');
        $this->data =
            ['name' => 'product', 'status' => 1,
            'price' => 12, 'image' => UploadedFile::fake()->image('avatar.jpg'),
            'description' => 'description the product', 'category_id' => Category::factory()->create()->id];
    }

    public function test_get_all_products()
    {
        $this->actingAs($this->seller)
            ->get(route('seller.product.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Product::factory(12)->create();

        $this->actingAs($this->seller)
            ->get(route('seller.product.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_product_because_role()
    {
        foreach ([$this->customer, $this->admin] as $user) {
            $this->actingAs($user)
                ->get(route('seller.product.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('seller.product.store'), $this->data)
                ->assertForbidden();

            $product = Product::factory()->create();

            $this->actingAs($user)
                ->get(route('seller.product.show', [$product->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('seller.product.update', [$product->id]), ['name' => 'product2', 'status' => 1])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('seller.product.destroy', [$product->id]))
                ->assertForbidden();
        }
    }

    public function test_seller_can_create_product_success()
    {
        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), $this->data)
            ->assertCreated();

        $this->assertDatabaseHas('products', ['id' => 1, 'name' => 'product']);
    }

    public function test_failed_to_create_product()
    {

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['name' => ''] + $this->data)
            ->assertSessionHasErrors('name');

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['status' => ''] + $this->data)
            ->assertSessionHasErrors('status');

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['price' => ''] + $this->data)
            ->assertSessionHasErrors('price');

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['image' => 'string image'] + $this->data)
            ->assertSessionHasErrors('image');

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['category_id' => 2] + $this->data)
            ->assertSessionHasErrors('category_id');

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), ['description' => ''] + $this->data)
            ->assertSessionHasErrors('description');

        Product::create(['seller_id' => $this->seller->id] + $this->data);

        $this->actingAs($this->seller)
            ->post(route('seller.product.store'), $this->data)
            ->assertSessionHasErrors('name');
    }

    public function test_seller_can_update_product_success()
    {
        $product = Product::create(['seller_id' => $this->seller->id] + $this->data);

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['name' => 'product4'] + $this->data)
            ->assertSuccessful();

        $this->assertDatabaseHas('products', ['name' => 'product4']);
    }

    public function test_failed_to_update_product()
    {
        $product = Product::create(['seller_id' => $this->seller->id] + $this->data);

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['name' => ''] + $this->data)
            ->assertSessionHasErrors('name');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['status' => ''] + $this->data)
            ->assertSessionHasErrors('status');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['price' => ''] + $this->data)
            ->assertSessionHasErrors('price');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['image' => 'string image'] + $this->data)
            ->assertSessionHasErrors('image');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['category_id' => 2] + $this->data)
            ->assertSessionHasErrors('category_id');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), ['description' => ''] + $this->data)
            ->assertSessionHasErrors('description');

        $this->actingAs($this->seller)
            ->put(route('seller.product.update', [$product->id]), $this->data)
            ->assertSessionHasNoErrors('name');
    }

    public function test_seller_can_show_product_success()
    {
        $product = Product::create(['seller_id' => $this->seller->id] + $this->data);

        $this->actingAs($this->seller)
            ->get(route('seller.product.show', [$product->id]))
            ->assertJsonPath('data.name', $product->name)
            ->assertSuccessful();
    }

    public function test_seller_can_destroy_product_success()
    {
        $product = Product::create(['seller_id' => $this->seller->id] + $this->data);

        $this->actingAs($this->seller)
            ->delete(route('seller.product.destroy', [$product->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('products', $this->data);
    }
}
