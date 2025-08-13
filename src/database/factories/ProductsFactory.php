<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'productcategory_id' => \App\Models\ProductCategory::factory(),
            'productstate_id' => \App\Models\ProductState::factory(),
            'productbrand_id' => \App\Models\ProductBrand::factory(),
            'name' => $this->faker->words(3, true),
            'detail' => $this->faker->paragraph(),
            'value' => $this->faker->numberBetween(100, 100000),
            'image' => $this->faker->imageUrl(800, 600, 'products'),
            'soldflg' => $this->faker->boolean(30), // 30%の確率でtrue（売却済み）
        ];
    }
}
