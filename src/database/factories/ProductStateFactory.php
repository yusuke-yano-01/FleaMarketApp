<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $states = [
            ['state' => 'excellent', 'name' => '非常に良い'],
            ['state' => 'good', 'name' => '良い'],
            ['state' => 'normal', 'name' => '普通'],
            ['state' => 'poor', 'name' => '悪い'],
            ['state' => 'very_poor', 'name' => '非常に悪い']
        ];
        
        $state = $this->faker->unique()->randomElement($states);
        
        return [
            'state' => $state['state'],
            'name' => $state['name'],
        ];
    }
}
