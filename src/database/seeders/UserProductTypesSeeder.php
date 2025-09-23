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
            ['id' => 1, 'name' => 'Seller'],
            ['id' => 2, 'name' => 'Buyer'],
            ['id' => 3, 'name' => 'mylist'],
            ['id' => 4, 'name' => 'Pending'],
        ];

        foreach ($types as $type) {
            // 既存のレコードがあるかチェック
            $exists = DB::table('user_product_types')->where('id', $type['id'])->exists();

            if (! $exists) {
                DB::table('user_product_types')->insert([
                    'id' => $type['id'],
                    'name' => $type['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // 既存のレコードを更新
                DB::table('user_product_types')->where('id', $type['id'])->update([
                    'name' => $type['name'],
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
