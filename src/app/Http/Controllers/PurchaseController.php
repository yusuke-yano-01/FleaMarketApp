<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class PurchaseController extends Controller
{
    /**
     * 購入手続き画面を表示
     */
    public function show($id)
    {
        $product = Product::with(['category', 'state'])->findOrFail($id);
        
        return view('purchase.purchase', compact('product'));
    }
    
    /**
     * 購入処理を実行
     */
    public function purchase(Request $request, $id)
    {
        // デバッグ用：リクエストデータをログに出力
        \Log::info('Purchase request data:', $request->all());
        
        $request->validate([
            'payment_method' => 'required|string|in:convenience_store,card',
            'shipping_postal' => 'required|string|max:10',
            'shipping_address' => 'required|string|max:255',
        ], [
            'payment_method.required' => 'お支払い方法を選択してください。',
            'payment_method.in' => '選択された支払い方法が無効です。',
            'shipping_postal.required' => '郵便番号は必須です。',
            'shipping_address.required' => '住所は必須です。',
        ]);
        
        $product = Product::findOrFail($id);
        
        // 商品が既に売却済みかチェック
        if ($product->soldflg) {
            return redirect()->back()
                ->with('error', 'この商品は既に売却済みです。');
        }
        
        // 購入処理：商品のsoldflgを更新
        $product->update(['soldflg' => true]);
        
        return redirect()->route('purchase.complete', $id)
            ->with('success', '購入手続きが完了しました。');
    }
    
    /**
     * 購入完了画面を表示
     */
    public function complete($id)
    {
        $product = Product::with(['category', 'state'])->findOrFail($id);
        
        return view('purchase.complete', compact('product'));
    }
    
    /**
     * 住所変更画面を表示
     */
    public function editAddress($id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();
        
        return view('purchase.address_edit', compact('product', 'user'));
    }
    
    /**
     * 住所変更を処理
     */
    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'postcode' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);
        
        // ユーザー情報を更新
        $user = Auth::user();
        $user->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ]);
        
        return redirect()->route('purchase.show', $id)
            ->with('success', '住所を更新しました。');
    }
}
