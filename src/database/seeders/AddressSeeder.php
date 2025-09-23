<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $addresses = [
            [
                'postcode' => '100-0001',
                'address' => '東京都千代田区千代田1-1-1',
                'building' => 'サンプルビル101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'postcode' => '150-0001',
                'address' => '東京都渋谷区神宮前1-1-1',
                'building' => 'サンプルマンション202',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'postcode' => '220-0001',
                'address' => '神奈川県横浜市西区みなとみらい1-1-1',
                'building' => 'サンプルタワー303',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'postcode' => '460-0001',
                'address' => '愛知県名古屋市中区三の丸1-1-1',
                'building' => 'サンプルプラザ404',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'postcode' => '530-0001',
                'address' => '大阪府大阪市北区梅田1-1-1',
                'building' => 'サンプルビル505',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($addresses as $address) {
            \DB::table('address')->insert($address);
        }
    }
}
