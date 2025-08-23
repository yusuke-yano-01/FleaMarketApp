<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductBrandsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $brands = [
            'Apple', 'Samsung', 'Sony', 'Nike', 'Adidas',
            'Uniqlo', 'Zara', 'H&M', 'IKEA', 'MUJI'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($brands),
        ];
    }
}
