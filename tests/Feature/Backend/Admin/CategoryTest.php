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

    public function test_failed_to_visit_category_because_role()
    {
        $this->actingAs($this->customer)
            ->get(route('admin.category.index'))
            ->assertForbidden();

        $this->actingAs($this->seller)
            ->get(route('admin.category.index'))
            ->assertForbidden();
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
        $this->actingAs($this->customer)
            ->post(route('admin.category.store'), ['name' => 'category', 'status' => 1])
            ->assertForbidden();

        $this->actingAs($this->seller)
            ->post(route('admin.category.store'), ['name' => 'category', 'status' => 1])
            ->assertForbidden();

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

}
