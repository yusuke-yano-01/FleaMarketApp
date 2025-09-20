<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateUserProductType3ToMylist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ID=3のレコードをwatchedからmylistに変更
        DB::table('user_product_types')
            ->where('id', 3)
            ->update(['name' => 'mylist']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // ID=3のレコードをmylistからwatchedに戻す
        DB::table('user_product_types')
            ->where('id', 3)
            ->update(['name' => 'watched']);
    }
}
