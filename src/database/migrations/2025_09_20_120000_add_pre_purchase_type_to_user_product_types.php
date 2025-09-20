<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // user_product_typesテーブルにID 4「未購入」を追加
        DB::table('user_product_types')->insert([
            'id' => 4,
            'name' => '未購入',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ID 4のレコードを削除
        DB::table('user_product_types')->where('id', 4)->delete();
    }
};
