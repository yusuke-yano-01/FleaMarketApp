<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductformController;
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

// 商品一覧関連のルート
Route::group(['prefix' => 'productlist'], function() {
    Route::get('', [ProductListController::class, 'index'])->name('productlist.index');
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

// プロフィール設定（住所チェックなし）
Route::middleware('auth')->group(function () {
    Route::get('profile/setup', [MyPageController::class, 'showProfileSetup'])->name('profile.setup');
    Route::post('profile/setup', [MyPageController::class, 'storeProfile'])->name('profile.store');
});

// 認証済みユーザー向けのルート（住所チェックなし）
Route::middleware('auth')->group(function () {
    // コメント投稿
    Route::post('productlist/product/{id}/comment', [ProductListController::class, 'addComment'])->name('product.comment');
    
    // マイリスト機能
    Route::post('productlist/mylist/add', [ProductListController::class, 'addToMylist']);
    Route::post('productlist/mylist/remove', [ProductListController::class, 'removeFromMylist']);
});

// ログイン後のみアクセス可能なルート（住所チェックあり）
Route::middleware(['auth', 'profile.setup'])->group(function () {
    // マイページ
    Route::get('mypage', [MyPageController::class, 'index']);
    Route::get('mypage/profile/edit', [MyPageController::class, 'editProfile']);
    Route::post('mypage/profile/update', [MyPageController::class, 'updateProfile'])->name('mypage.profile.update');
    
    // 商品出品関連のルート
    Route::get('productform', [ProductformController::class, 'create'])->name('productform.create');
    Route::post('productform', [ProductformController::class, 'store'])->name('productform.store');
    
    // 購入関連のルート
    Route::get('purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('purchase/{id}', [PurchaseController::class, 'purchase'])->name('purchase.process');
    Route::get('purchase/{id}/complete', [PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::get('purchase/{id}/address/edit', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('purchase/{id}/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
});

