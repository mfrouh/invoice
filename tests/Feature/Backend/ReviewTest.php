<?php

namespace Tests\Feature\Backend;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
    }

    public function test_get_all_reviews()
    {
        $this->actingAs($this->admin)
            ->get(route('review.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Review::factory(12)->create();

        $this->actingAs($this->admin)
            ->get(route('review.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_reviews_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('review.index'))
                ->assertForbidden();
        }
    }
}
