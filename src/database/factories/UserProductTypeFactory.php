<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserProductTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = [
            '購入商品',
            '販売商品',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($types),
        ];
    }
}
