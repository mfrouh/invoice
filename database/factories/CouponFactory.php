<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->name,
            'start' => now()->addDay(1),
            'end' => now()->addDay(4),
            'condition' => $this->faker->randomElement([Coupon::MORE, Coupon::LESS]),
            'condition_value' => rand(1, 3),
            'type' => $this->faker->randomElement([Coupon::FIXED, Coupon::VARIABLE]),
            'value' => rand(40, 70),
            'message' => 'Coupon Message',
            'times' => rand(1, 4),
        ];
    }
}
