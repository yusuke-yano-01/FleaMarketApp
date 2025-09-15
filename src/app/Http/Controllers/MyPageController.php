<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProductRelation;
use App\Models\UserProductType;
use App\Http\Requests\ProfileRequest;

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
     * プロフィール設定画面を表示
     */
    public function showProfileSetup()
    {
        $user = Auth::user();
        return view('mypage.profile_setup', compact('user'));
    }
    
    /**
     * プロフィール編集画面を表示
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('mypage.profile_setup', compact('user'));
    }
    
    /**
     * プロフィール設定を保存
     */
    public function storeProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        
        // 画像アップロード処理
        $imagePath = $user->image; // デフォルト画像を保持
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/userimages', $imageName);
            $imagePath = 'storage/userimages/' . $imageName;
        }
        
        // ユーザー情報を更新
        $user->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
            'image' => $imagePath,
            'registeredflg' => true,
        ]);
        
        return redirect()->route('productlist.index')
            ->with('success', 'プロフィール設定が完了しました。');
    }
}
