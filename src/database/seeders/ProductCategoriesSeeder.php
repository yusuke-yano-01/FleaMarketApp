<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Books'],
            ['name' => 'Home & Garden'],
            ['name' => 'Sports'],
            ['name' => 'Automotive'],
            ['name' => 'Toys & Games'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Jewelry'],
            ['name' => 'Food & Beverages']
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insert([
                'name' => $category['name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
