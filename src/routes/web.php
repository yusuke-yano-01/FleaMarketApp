<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\MyPageController;
use Laravel\Fortify\Fortify;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ホーム画面を商品一覧にリダイレクト
Route::get('/', function () {
    return redirect('/productlist');
});

// 商品一覧関連のルート（認証不要）
Route::group(['prefix' => 'productlist'], function() {
    Route::get('', [ProductListController::class, 'index']);
    Route::get('search', [ProductListController::class, 'search']);
    Route::post('search', [ProductListController::class, 'search']);
    Route::get('product/{id}', [ProductListController::class, 'show'])->name('productlist.product'); // 商品詳細ページ
});

// 認証関連のルート
Route::group(['prefix' => 'auth'], function() {
    Route::get('login', [AuthController::class, 'index']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'registerForm']);
    Route::post('register', [AuthController::class,'register']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// ログイン後のみアクセス可能なルート
Route::middleware('auth')->group(function () {
    // マイリスト機能
    Route::post('productlist/mylist/add', [ProductListController::class, 'addToMylist']);
    Route::post('productlist/mylist/remove', [ProductListController::class, 'removeFromMylist']);
    
    // マイページ
    Route::get('mypage', [MyPageController::class, 'index']);
    Route::get('mypage/profile/edit', [MyPageController::class, 'editProfile']);
});

