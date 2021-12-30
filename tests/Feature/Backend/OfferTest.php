<?php

namespace Tests\Feature\Backend;

use App\Models\Offer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
        $this->data =
            ['product_id' => Product::factory()->create()->id, 'type' => 'FIXED', 'value' => 12, 'message' => 'Create offer', 'start' => now()->addDay(1), 'end' => now()->addDay(3)];
    }

    public function test_get_all_offers()
    {
        $this->actingAs($this->admin)
            ->get(route('offer.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Offer::factory(12)->create();

        $this->actingAs($this->admin)
            ->get(route('offer.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_offer_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('offer.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('offer.store'), $this->data)
                ->assertForbidden();

            $offer = Offer::factory()->create();

            $this->actingAs($user)
                ->get(route('offer.show', [$offer->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('offer.update', [$offer->id]), $this->data)
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('offer.destroy', [$offer->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_offer_success()
    {
        $this->actingAs($this->admin)
            ->post(route('offer.store'), $this->data)
            ->assertCreated();

        $this->assertDatabaseHas('offers', ['id' => 1, 'product_id' => 1, 'type' => 'FIXED']);
    }

    public function test_failed_to_create_offer()
    {
        $test_data =
            ['product_id' => 2, 'type' => 'fixed',
            'value' => 'string value', 'message' => 'me',
            'start' => now()->addDay(5), 'end' => now()];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->post(route('offer.store'), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }

        Offer::create($this->data);
        $this->actingAs($this->admin)
            ->post(route('offer.store'), ['product_id' => 1] + $this->data)
            ->assertSessionHasErrors('product_id');
    }

    public function test_admin_can_update_offer_success()
    {
        $offer = Offer::create($this->data);

        $this->actingAs($this->admin)
            ->put(route('offer.update', [$offer->id]), ['product_id' => 1, 'type' => 'VARIABLE'] + $this->data)
            ->assertSuccessful();

        $this->assertDatabaseHas('offers', ['id' => 1, 'product_id' => 1, 'type' => 'VARIABLE']);
    }

    public function test_failed_to_update_offer()
    {
        $offer = Offer::create($this->data);

        $test_data =
            ['product_id' => 2, 'type' => 'fixed',
            'value' => 'string value', 'message' => 'me',
            'start' => now()->addDay(5), 'end' => now()];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->put(route('offer.update', [$offer->id]), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }
    }

    public function test_admin_can_show_offer_success()
    {
        $offer = Offer::create($this->data);

        $this->actingAs($this->admin)
            ->get(route('offer.show', [$offer->id]))
            ->assertJsonPath('data.name', $offer->name)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_offer_success()
    {
        $offer = Offer::create($this->data);

        $this->actingAs($this->admin)
            ->delete(route('offer.destroy', [$offer->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('offers', $this->data);
    }
}
