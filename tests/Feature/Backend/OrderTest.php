<?php

namespace Tests\Feature\Backend;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_get_all_orders()
    {
        $this->actingAs($this->admin)
            ->get(route('orders'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Order::factory(10)->create();

        $this->actingAs($this->admin)
            ->get(route('orders'))
            ->assertJsonCount(10, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_order_because_role()
    {
        $this->actingAs($this->customer)
            ->get(route('orders'))
            ->assertForbidden();
    }
}
