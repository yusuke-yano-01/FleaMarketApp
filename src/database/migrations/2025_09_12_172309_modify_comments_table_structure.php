<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 外部キー制約を削除（存在する場合のみ）
        try {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropForeign(['userproductrelation_id']);
            });
        } catch (Exception $e) {
            // 外部キー制約が存在しない場合は無視
        }

        // カラムを削除
        if (Schema::hasColumn('comments', 'userproductrelation_id')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('userproductrelation_id');
            });
        }

        // 新しいカラムを追加
        Schema::table('comments', function (Blueprint $table) {
            if (! Schema::hasColumn('comments', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('id');
            }
            if (! Schema::hasColumn('comments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('product_id');
            }
        });

        // 外部キー制約を追加
        Schema::table('comments', function (Blueprint $table) {
            try {
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            } catch (Exception $e) {
                // 外部キー制約が既に存在する場合は無視
            }
            try {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            } catch (Exception $e) {
                // 外部キー制約が既に存在する場合は無視
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // 新しい外部キー制約を削除
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);

            // 新しいカラムを削除
            $table->dropColumn(['product_id', 'user_id']);

            // 元のカラムを復元
            $table->unsignedBigInteger('userproductrelation_id');
            $table->foreign('userproductrelation_id')->references('id')->on('user_product_relations')->onDelete('cascade');
        });
    }
};
