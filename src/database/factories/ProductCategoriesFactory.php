<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            '電化製品', '服・ファッション', '本・雑誌', 'スポーツ用品', 'おもちゃ・ゲーム',
            '家具・インテリア', '美容・コスメ', '食品・飲料', '車・バイク', '楽器',
            'アート・コレクション', '工具・DIY', 'アウトドア', 'ペット用品', 'その他'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($categories),
        ];
    }
}
