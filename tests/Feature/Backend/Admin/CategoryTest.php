<?php

namespace Tests\Feature\Backend\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
        $this->seller = User::factory()->create(['role' => 'Seller']);
    }

    public function test_get_all_categories()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.category.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Category::factory(12)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.category.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_category_because_role()
    {
        foreach ([$this->customer, $this->seller] as $user) {
            $this->actingAs($user)
                ->get(route('admin.category.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('admin.category.store'), ['name' => 'category', 'status' => 1])
                ->assertForbidden();

            $category = Category::factory()->create();

            $this->actingAs($user)
                ->get(route('admin.category.show', [$category->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('admin.category.update', [$category->id]), ['name' => 'category2', 'status' => 1])
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('admin.category.destroy', [$category->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_category_success()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.category.store'), ['name' => 'category', 'status' => 1])
            ->assertCreated();

        $this->assertDatabaseHas('categories', ['name' => 'category', 'status' => 1]);
    }

    public function test_failed_to_create_category()
    {

        $this->actingAs($this->admin)
            ->post(route('admin.category.store'), ['name' => '', 'status' => 0])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->post(route('admin.category.store'), ['name' => 'category', 'status' => ''])
            ->assertSessionHasErrors('status');

        Category::create(['name' => 'category', 'status' => 1]);

        $this->actingAs($this->admin)
            ->post(route('admin.category.store'), ['name' => 'category', 'status' => 1])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category_success()
    {
        $category = Category::create(['name' => 'category', 'status' => 1]);

        $this->actingAs($this->admin)
            ->put(route('admin.category.update', [$category->id]), ['name' => 'category4', 'status' => 1])
            ->assertSuccessful();

        $this->assertDatabaseHas('categories', ['name' => 'category4', 'status' => 1]);
    }

    public function test_failed_to_update_category()
    {
        $category = Category::create(['name' => 'category', 'status' => 1]);

        $this->actingAs($this->admin)
            ->put(route('admin.category.update', [$category->id]), ['name' => '', 'status' => 0])
            ->assertSessionHasErrors('name');

        $this->actingAs($this->admin)
            ->put(route('admin.category.update', [$category->id]), ['name' => 'category', 'status' => ''])
            ->assertSessionHasErrors('status');

        $this->actingAs($this->admin)
            ->put(route('admin.category.update', [$category->id]), ['name' => 'category', 'status' => 1])
            ->assertSessionHasNoErrors('name');
    }

    public function test_admin_can_show_category_success()
    {
        $category = Category::create(['name' => 'category', 'status' => 1]);

        $this->actingAs($this->admin)
            ->get(route('admin.category.show', [$category->id]))
            ->assertJsonPath('data.name', $category->name)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_category_success()
    {
        $category = Category::create(['name' => 'category', 'status' => 1]);

        $this->actingAs($this->admin)
            ->delete(route('admin.category.destroy', [$category->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('categories', ['name' => 'category', 'status' => 1]);
    }

}
