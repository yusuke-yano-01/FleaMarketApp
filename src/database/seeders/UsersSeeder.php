<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => '田中太郎',
                'email' => 'tanaka@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '100-0001',
                'address' => '東京都千代田区千代田1-1-1',
                'building' => 'サンプルビル101',
                'image' => 'default_user_icon.png',
                'registeredflg' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '佐藤花子',
                'email' => 'sato@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '150-0001',
                'address' => '東京都渋谷区神宮前1-1-1',
                'building' => 'サンプルマンション202',
                'image' => 'default_user_icon.png',
                'registeredflg' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '鈴木一郎',
                'email' => 'suzuki@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '220-0001',
                'address' => '神奈川県横浜市西区みなとみらい1-1-1',
                'building' => 'サンプルタワー303',
                'image' => 'default_user_icon.png',
                'registeredflg' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '高橋美咲',
                'email' => 'takahashi@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '460-0001',
                'address' => '愛知県名古屋市中区三の丸1-1-1',
                'building' => 'サンプルプラザ404',
                'image' => 'default_user_icon.png',
                'registeredflg' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '渡辺健太',
                'email' => 'watanabe@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'postcode' => '530-0001',
                'address' => '大阪府大阪市北区梅田1-1-1',
                'building' => 'サンプルビル505',
                'image' => 'default_user_icon.png',
                'registeredflg' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
