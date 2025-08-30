<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProductRelation;
use App\Models\UserProductType;

class MyPageController extends Controller
{
    /**
     * マイページを表示
     */
    public function index()
    {
        $user = Auth::user();
        
        // TODO: 実際の出品・購入履歴データを取得する実装が必要
        // 現在はダミーデータとして空のコレクションを返す
        $soldProducts = collect(); // ユーザーが出品した商品
        $boughtProducts = collect(); // ユーザーが購入した商品
        
        return view('mypage.profile', compact('user', 'soldProducts', 'boughtProducts'));
    }
    
    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('mypage.profile_edit', compact('user'));
    }
}
