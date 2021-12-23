<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seller_id' => User::where('role', 'Seller')->get()->random()->id,
            'customer_id' => User::where('role', 'Customer')->get()->random()->id,
            'total' => rand(500, 900),
        ];
    }
}
