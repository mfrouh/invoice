<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory()->create()->id,
            'type' => 'FIXED',
            'value' => 12,
            'message' => 'Create offer',
            'start' => now()->addDay(1),
            'end' => now()->addDay(3),
        ];
    }
}
