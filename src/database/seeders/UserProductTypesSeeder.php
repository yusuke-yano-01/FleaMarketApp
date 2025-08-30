<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProductTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Seller'],
            ['name' => 'Buyer'],
            ['name' => 'watched'],
            ['name' => 'mylist']
        ];

        foreach ($types as $type) {
            // 既存のレコードがあるかチェック
            $exists = DB::table('user_product_types')->where('name', $type['name'])->exists();
            
            if (!$exists) {
                DB::table('user_product_types')->insert([
                    'name' => $type['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
