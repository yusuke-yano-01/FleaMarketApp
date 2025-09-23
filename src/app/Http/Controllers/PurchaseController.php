<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Address;
use App\Models\UserProductRelation;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    /**
     * 購入手続き画面を表示
     */
    public function show($id)
    {
        $product = Product::with(['category', 'state'])->findOrFail($id);
        $user = Auth::user();
        
        // 商品に対する購入関係のレコードを確認（Buyer または 未購入）
        $purchaseRelation = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->whereIn('userproducttype_id', [2, 4]) // Buyer または 未購入
            ->with('address')
            ->first();
        
        // 購入関係のレコードがない場合は「未購入」レコードを作成
        if (!$purchaseRelation) {
            $purchaseRelation = UserProductRelation::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'userproducttype_id' => 4, // 未購入
            ]);
        }
        
        $address = null;
        if ($purchaseRelation && $purchaseRelation->address_id && $purchaseRelation->address) {
            // addressテーブルにレコードがある場合はaddressテーブルの情報を使用
            $address = $purchaseRelation->address;
        } else {
            // addressテーブルにレコードがない場合はusersテーブルの情報を使用
            $address = (object) [
                'postcode' => $user->postcode,
                'address' => $user->address,
                'building' => $user->building,
            ];
        }
        
        return view('purchase.purchase', compact('product', 'user', 'address'));
    }
    
    /**
     * 購入処理を実行
     */
    public function purchase(Request $request, $id)
    {
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
        
        // UserProductRelationのuserproducttype_idを「未購入」から「Buyer」に更新
        $user = Auth::user();
        $purchaseRelation = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $id)
            ->where('userproducttype_id', 4) // 未購入
            ->first();
        
        if ($purchaseRelation) {
            $purchaseRelation->update(['userproducttype_id' => 2]); // Buyer
        } else {
            // レコードがない場合は新規作成
            UserProductRelation::create([
                'user_id' => $user->id,
                'product_id' => $id,
                'userproducttype_id' => 2, // Buyer
            ]);
        }
        
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
        
        // 商品に対する購入関係のレコードを確認（Buyer または 未購入）
        $purchaseRelation = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->whereIn('userproducttype_id', [2, 4]) // Buyer または 未購入
            ->with('address')
            ->first();
        
        $address = null;
        if ($purchaseRelation && $purchaseRelation->address_id && $purchaseRelation->address) {
            // addressテーブルにレコードがある場合はaddressテーブルの情報を使用
            $address = $purchaseRelation->address;
        } else {
            // addressテーブルにレコードがない場合はusersテーブルの情報を使用
            $address = (object) [
                'postcode' => $user->postcode,
                'address' => $user->address,
                'building' => $user->building,
            ];
        }
        
        return view('purchase.address_edit', compact('product', 'user', 'address'));
    }
    
    /**
     * 住所変更を処理
     */
    public function updateAddress(AddressRequest $request, $id)
    {
        $user = Auth::user();
        
        // 商品に対する購入関係のレコードを確認（Buyer または 未購入）
        $purchaseRelation = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $id)
            ->whereIn('userproducttype_id', [2, 4]) // Buyer または 未購入
            ->with('address')
            ->first();
        
        $address = null;
        if ($purchaseRelation && $purchaseRelation->address_id && $purchaseRelation->address) {
            // addressテーブルにレコードがある場合は更新
            $address = $purchaseRelation->address;
            $address->update([
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building ?? '',
            ]);
        } else {
            // addressテーブルにレコードがない場合は新規作成
            $address = Address::create([
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building ?? '',
            ]);
            
            if ($purchaseRelation) {
                // 既存の購入関係がある場合はaddress_idを更新
                $purchaseRelation->update(['address_id' => $address->id]);
            } else {
                // 購入関係がない場合は新規作成
                UserProductRelation::create([
                    'user_id' => $user->id,
                    'product_id' => $id,
                    'userproducttype_id' => 2, // Buyer
                    'address_id' => $address->id,
                ]);
            }
        }
        
        return redirect()->route('purchase.show', $id)
            ->with('success', '住所を更新しました。');
    }
}
