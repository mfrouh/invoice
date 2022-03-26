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
            'customer_id'     => User::where('role', 'Customer')->get()->random()->id,
            'total'           => rand(500, 900),
            'tax'             => rand(2, 5),
            'ship'            => null,
            'discount'        => null,
            'invoice_qr_code' => $this->faker->uuid,
        ];
    }
}
