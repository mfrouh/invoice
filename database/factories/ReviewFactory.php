<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'review' => 'review 1',
            'rate' => '1',
            'product_id' => Product::factory()->create()->id,
            'customer_id' => User::factory()->create(['role' => 'Customer']),
        ];
    }
}
