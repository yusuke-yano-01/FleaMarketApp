<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 指定された商品データ
        $products = [
            [
                'name' => '腕時計',
                'value' => 15000,
                'brand' => 'Rolax',
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'productimages/sample/006/watch.jpg',
                'state' => '良好',
            ],
            [
                'name' => 'HDD',
                'value' => 5000,
                'brand' => '西芝',
                'detail' => '高速で信頼性の高いハードディスク',
                'image' => 'productimages/sample/003/hdd.jpg',
                'state' => '目立った傷や汚れなし',
            ],
            [
                'name' => '玉ねぎ3束',
                'value' => 300,
                'brand' => 'なし',
                'detail' => '新鮮な玉ねぎ3束のセット',
                'image' => 'productimages/sample/007/onion.jpg',
                'state' => 'やや傷や汚れあり',
            ],
            [
                'name' => '革靴',
                'value' => 4000,
                'brand' => 'LeatherWorks',
                'detail' => 'クラシックなデザインの革靴',
                'image' => 'productimages/sample/002/shoes.jpg',
                'state' => '状態が悪い',
            ],
            [
                'name' => 'ノートPC',
                'value' => 45000,
                'brand' => 'TechPro',
                'detail' => '高性能なノートパソコン',
                'image' => 'productimages/sample/001/laptop.jpg',
                'state' => '良好',
            ],
            [
                'name' => 'マイク',
                'value' => 8000,
                'brand' => 'なし',
                'detail' => '高音質のレコーディング用マイク',
                'image' => 'productimages/sample/004/mic.jpg',
                'state' => '目立った傷や汚れなし',
            ],
            [
                'name' => 'ショルダーバッグ',
                'value' => 3500,
                'brand' => 'StyleBag',
                'detail' => 'おしゃれなショルダーバッグ',
                'image' => 'productimages/sample/008/bag.jpg',
                'state' => 'やや傷や汚れあり',
            ],
            [
                'name' => 'タンブラー',
                'value' => 500,
                'brand' => 'なし',
                'detail' => '使いやすいタンブラー',
                'image' => 'productimages/sample/009/tumbler.jpg',
                'state' => '状態が悪い',
            ],
            [
                'name' => 'コーヒーミル',
                'value' => 4000,
                'brand' => 'Starbacks',
                'detail' => '手動のコーヒーミル',
                'image' => 'productimages/sample/005/coffee-grinder.jpg',
                'state' => '良好',
            ],
            [
                'name' => 'メイクセット',
                'value' => 2500,
                'brand' => 'BeautyLine',
                'detail' => '便利なメイクアップセット',
                'image' => 'productimages/sample/010/makeup.jpg',
                'state' => '目立った傷や汚れなし',
            ],
        ];

        // ランダムに商品を選択
        $product = $this->faker->randomElement($products);

        // 状態に応じてproductstate_idを決定
        $stateMapping = [
            '良好' => 1,
            '目立った傷や汚れなし' => 2,
            'やや傷や汚れあり' => 3,
            '状態が悪い' => 4,
        ];

        // 商品名に応じてカテゴリを決定
        $categoryMapping = [
            '腕時計' => 9, // ジュエリー・アクセサリー
            'HDD' => 1, // 家電・PC・スマホ
            'ノートPC' => 1, // 家電・PC・スマホ
            'マイク' => 1, // 家電・PC・スマホ
            '玉ねぎ3束' => 5, // 食品・飲料
            'コーヒーミル' => 4, // ホビー・スポーツ
            '革靴' => 2, // ファッション
            'ショルダーバッグ' => 2, // ファッション
            'タンブラー' => 5, // 食品・飲料
            'メイクセット' => 8, // 美容・健康
        ];

        return [
            'productcategory_id' => $categoryMapping[$product['name']] ?? 1,
            'productstate_id' => $stateMapping[$product['state']] ?? 1,
            'name' => $product['name'],
            'detail' => $product['detail'],
            'value' => $product['value'],
            'brand' => $product['brand'],
            'image' => $product['image'],
            'soldflg' => $this->faker->boolean(30), // 30%の確率でtrue（売却済み）
        ];
    }
}
