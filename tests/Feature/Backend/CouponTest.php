<?php

namespace Tests\Feature\Backend;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->customer = User::factory()->create(['role' => 'Customer']);
        $this->data =
            ['code'               => 'code 1', 'start' => now()->addDay(1), 'end' => now()->addDay(4),
                'condition'       => $this->faker->randomElement([Coupon::MORE, Coupon::LESS]),
                'condition_value' => rand(1, 3), 'type' => $this->faker->randomElement([Coupon::FIXED, Coupon::VARIABLE]),
                'value'           => rand(40, 70), 'message' => 'Coupon Message', 'times' => rand(1, 4),
            ];
    }

    public function test_get_all_coupons()
    {
        $this->actingAs($this->admin)
            ->get(route('coupon.index'))
            ->assertSimilarJson(['data' => []])
            ->assertSuccessful();

        Coupon::factory(12)->create();

        $this->actingAs($this->admin)
            ->get(route('coupon.index'))
            ->assertJsonCount(12, 'data')
            ->assertSuccessful();
    }

    public function test_failed_to_visit_all_routes_coupon_because_role()
    {
        foreach ([$this->customer] as $user) {
            $this->actingAs($user)
                ->get(route('coupon.index'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('coupon.store'), $this->data)
                ->assertForbidden();

            $coupon = Coupon::factory()->create();

            $this->actingAs($user)
                ->get(route('coupon.show', [$coupon->id]))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('coupon.update', [$coupon->id]), $this->data)
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('coupon.destroy', [$coupon->id]))
                ->assertForbidden();
        }
    }

    public function test_admin_can_create_coupon_success()
    {
        $this->actingAs($this->admin)
            ->post(route('coupon.store'), $this->data)
            ->assertCreated();

        $this->assertDatabaseHas('coupons', ['id' => 1, 'code' => 'code 1']);
    }

    public function test_failed_to_create_coupon()
    {
        $test_data =
            ['code' => '', 'start' => now()->addDay(5), 'end' => now(), 'condition' => 'wrong condition', 'type' => 'wrong type', 'times' => '',
            ];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->post(route('coupon.store'), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }
    }

    public function test_admin_can_update_coupon_success()
    {
        $coupon = Coupon::create($this->data);

        $this->actingAs($this->admin)
            ->put(route('coupon.update', [$coupon->id]), ['code' => 'code 1', 'type' => 'VARIABLE'] + $this->data)
            ->assertSuccessful();

        $this->assertDatabaseHas('coupons', ['id' => 1, 'code' => 'code 1', 'type' => 'VARIABLE']);
    }

    public function test_failed_to_update_coupon()
    {
        $coupon = Coupon::create($this->data);

        $test_data =
            ['code' => '', 'start' => now()->addDay(5), 'end' => now(), 'condition' => 'wrong condition', 'type' => 'wrong type', 'times' => '',
            ];

        foreach ($test_data as $key => $value) {
            $this->actingAs($this->admin)
                ->put(route('coupon.update', [$coupon->id]), [$key => $value] + $this->data)
                ->assertSessionHasErrors($key);
        }
    }

    public function test_admin_can_show_coupon_success()
    {
        $coupon = Coupon::create($this->data);

        $this->actingAs($this->admin)
            ->get(route('coupon.show', [$coupon->id]))
            ->assertJsonPath('data.name', $coupon->name)
            ->assertSuccessful();
    }

    public function test_admin_can_destroy_coupon_success()
    {
        $coupon = Coupon::create($this->data);

        $this->actingAs($this->admin)
            ->delete(route('coupon.destroy', [$coupon->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('coupons', $this->data);
    }
}
