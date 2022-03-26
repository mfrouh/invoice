<?php

namespace Tests\Feature\Frontend;

use Tests\TestCase;

class CouponTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_customer_can_apply_a_coupon()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
