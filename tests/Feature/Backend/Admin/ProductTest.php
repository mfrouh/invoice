<?php

namespace Tests\Feature\Backend\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function test_get_all_products()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->admin)
            ->get(route('admin.products'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Product::factory(15)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.products'))
            ->assertJsonCount(15, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_product_because_role()
    {
        $this->actingAs($this->customer)
            ->get(route('admin.products'))
            ->assertForbidden();

        $this->actingAs($this->seller)
            ->get(route('admin.products'))
            ->assertForbidden();
    }
}
