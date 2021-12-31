<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::where('role', 'Customer')->get()->random()->id;
        $product = Product::factory()->create();
        $quantity = rand(1, 4);
        return [
            'customer_id' => $user,
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'details' => '',
            'total_price' => $quantity * $product->price,
        ];
    }
}
