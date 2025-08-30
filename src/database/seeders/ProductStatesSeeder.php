<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['state' => 'excellent', 'name' => '非常に良い'],
            ['state' => 'good', 'name' => '良い'],
            ['state' => 'normal', 'name' => '普通'],
            ['state' => 'poor', 'name' => '悪い'],
            ['state' => 'very_poor', 'name' => '非常に悪い']
        ];

        foreach ($states as $state) {
            DB::table('product_states')->insert([
                'state' => $state['state'],
                'name' => $state['name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
