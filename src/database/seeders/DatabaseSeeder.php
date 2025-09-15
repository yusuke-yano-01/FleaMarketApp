<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        // マスターデータの投入
        $this->call([
            UsersSeeder::class,              // ユーザー（最初に実行）
            AddressSeeder::class,            // 住所
            ProductCategoriesSeeder::class,  // 商品カテゴリー
            ProductStatesSeeder::class,      // 商品状態
            UserProductTypesSeeder::class,   // ユーザー商品タイプ
            ProductsSeeder::class,           // 商品（UserProductRelationsSeederの前に実行）
            UserProductRelationsSeeder::class,   // ユーザー商品関係（最後に実行）
        ]);
    }
}
