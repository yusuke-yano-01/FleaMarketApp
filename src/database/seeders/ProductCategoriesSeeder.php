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
            ['id' => 1, 'name' => '家電・PC・スマホ'],
            ['id' => 2, 'name' => 'ファッション'],
            ['id' => 3, 'name' => '本・雑誌'],
            ['id' => 4, 'name' => 'ホビー・スポーツ'],
            ['id' => 5, 'name' => '食品・飲料'],
            ['id' => 6, 'name' => '自動車・バイク'],
            ['id' => 7, 'name' => 'おもちゃ・ゲーム'],
            ['id' => 8, 'name' => '美容・健康'],
            ['id' => 9, 'name' => 'ジュエリー・アクセサリー'],
            ['id' => 10, 'name' => 'その他']
        ];

        foreach ($categories as $category) {
            // 既存のレコードがあるかチェック
            $exists = DB::table('product_categories')->where('id', $category['id'])->exists();
            
            if (!$exists) {
                DB::table('product_categories')->insert([
                    'id' => $category['id'],
                    'name' => $category['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                // 既存のレコードを更新
                DB::table('product_categories')->where('id', $category['id'])->update([
                    'name' => $category['name'],
                    'updated_at' => now()
                ]);
            }
        }
    }
}
