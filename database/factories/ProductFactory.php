<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'status' => rand(0, 1),
            'price' => rand(10, 400),
            'image' => '/images/products/1.png',
            'description' => 'description the product',
            'category_id' => Category::factory()->create()->id,
        ];
    }
}
