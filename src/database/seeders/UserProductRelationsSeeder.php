<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProductRelationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relations = [
            // 売り手の関係
            [
                'product_id' => 1,           // Living Room Laptop
                'user_id' => 1,              // 田中太郎
                'userproducttype_id' => 1,   // Seller（売り手）
                'address_id' => 1,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,           // Leather Shoes
                'user_id' => 2,              // 佐藤花子
                'userproducttype_id' => 1,   // Seller（売り手）
                'address_id' => 2,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 3,           // HDD Hard Disk
                'user_id' => 3,              // 鈴木一郎
                'userproducttype_id' => 1,   // Seller（売り手）
                'address_id' => 3,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 4,           // Music Mic
                'user_id' => 4,              // 高橋美咲
                'userproducttype_id' => 1,   // Seller（売り手）
                'address_id' => 4,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 5,           // Coffee Grinder
                'user_id' => 5,              // 渡辺健太
                'userproducttype_id' => 1,   // Seller（売り手）
                'address_id' => 5,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // 買い手の関係
            [
                'product_id' => 1,           // Living Room Laptop
                'user_id' => 2,              // 佐藤花子（買い手）
                'userproducttype_id' => 2,   // Buyer（買い手）
                'address_id' => 2,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 2,           // Leather Shoes
                'user_id' => 3,              // 鈴木一郎（買い手）
                'userproducttype_id' => 2,   // Buyer（買い手）
                'address_id' => 3,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 3,           // HDD Hard Disk
                'user_id' => 4,              // 高橋美咲（買い手）
                'userproducttype_id' => 2,   // Buyer（買い手）
                'address_id' => 4,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 4,           // Music Mic
                'user_id' => 5,              // 渡辺健太（買い手）
                'userproducttype_id' => 2,   // Buyer（買い手）
                'address_id' => 5,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'product_id' => 5,           // Coffee Grinder
                'user_id' => 1,              // 田中太郎（買い手）
                'userproducttype_id' => 2,   // Buyer（買い手）
                'address_id' => 1,           // 住所ID
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($relations as $relation) {
            DB::table('user_product_relations')->insert($relation);
        }
    }
}
