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
            ['name' => '非常に良い'],
            ['name' => '良い'],
            ['name' => '普通'],
            ['name' => '悪い'],
            ['name' => '非常に悪い']
        ];

        foreach ($states as $state) {
            DB::table('product_states')->insert([
                'name' => $state['name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
