<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // カテゴリ別の価格帯を定義
        $categoryPriceRanges = [
            1 => [1000, 50000],    // Electronics: 1,000円〜50,000円
            2 => [500, 15000],     // Clothing: 500円〜15,000円
            3 => [100, 3000],      // Books: 100円〜3,000円
            4 => [200, 8000],      // Home & Garden: 200円〜8,000円
            5 => [300, 12000],     // Sports: 300円〜12,000円
        ];
        
        // 既存のカテゴリからランダムに選択
        $category = \App\Models\ProductCategory::inRandomOrder()->first();
        $categoryId = $category ? $category->id : 1;
        $minPrice = $categoryPriceRanges[$categoryId] ?? [100, 10000];
        
        // 日本語の商品名サンプル
        $productNames = [
            '高品質なスマートフォン', 'スタイリッシュなTシャツ', '面白い小説本',
            '使いやすい工具セット', '快適なソファ', '美味しいお菓子',
            '高性能なノートパソコン', 'おしゃれなバッグ', '実用的なキッチン用品',
            '楽しいボードゲーム', '美しいアート作品', '便利な収納用品',
            '高級な腕時計', '快適な枕', '実用的な傘', 'おしゃれな帽子',
            '高性能なカメラ', '快適な椅子', '実用的な本棚', 'おしゃれな花瓶',
            '高品質なヘッドフォン', '快適な布団', '実用的なテーブル', 'おしゃれなランプ',
            '高性能なプリンター', '快適なクッション', '実用的な収納ボックス', 'おしゃれな絵画',
            '高品質なスピーカー', '快適なマット', '実用的な棚', 'おしゃれな時計',
            '高性能なスキャナー', '快適なブランケット', '実用的なゴミ箱', 'おしゃれな花瓶',
            '高品質なマイク', '快適なタオル', '実用的な洗面台', 'おしゃれな鏡',
            '高性能なルーター', '快適なカーペット', '実用的なドア', 'おしゃれなカーテン'
        ];
        
        // 日本語の商品詳細サンプル
        $productDetails = [
            'この商品は高品質で長持ちします。日常使いに最適です。',
            'デザインが美しく、機能性も抜群です。多くのお客様に愛用されています。',
            '使いやすさを重視した設計で、初心者の方でも簡単に使えます。',
            '耐久性に優れ、長期間の使用に耐える品質です。',
            'スタイリッシュな見た目で、お部屋のインテリアとしても活躍します。',
            '安全性を最優先に考えて作られた商品です。',
            'コストパフォーマンスが良く、お得感のある商品です。',
            '環境に配慮した素材を使用しています。',
            'メンテナンスが簡単で、お手入れが楽です。',
            '多機能で様々な用途に使える便利な商品です。'
        ];
        
        return [
            'productcategory_id' => $categoryId,
            'productstate_id' => \App\Models\ProductState::inRandomOrder()->first()->id,
            'name' => $this->faker->unique()->randomElement($productNames),
            'detail' => $this->faker->randomElement($productDetails),
            'value' => $this->faker->numberBetween($minPrice[0], $minPrice[1]),
            'brand' => $this->faker->randomElement([
                'Apple', 'Samsung', 'Sony', 'Nike', 'Adidas',
                'Uniqlo', 'Zara', 'H&M', 'IKEA', 'MUJI'
            ]),
            'image' => $this->faker->randomElement([
                'productimages/20250813/001/laptop.jpg',
                'productimages/20250813/002/shoes.jpg',
                'productimages/20250813/003/hdd.jpg',
                'productimages/20250813/004/mic.jpg',
                'productimages/20250813/005/coffee-grinder.jpg'
            ]),
            'soldflg' => $this->faker->boolean(30), // 30%の確率でtrue（売却済み）
        ];
    }
}
