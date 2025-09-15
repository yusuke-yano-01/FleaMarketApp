<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductState;

class ProductformController extends Controller
{
    /**
     * 商品出品画面を表示
     */
    public function create()
    {
        $categories = ProductCategory::all();
        $states = ProductState::all();
        
        return view('productform.product_detail', compact('categories', 'states'));
    }
    
    /**
     * 商品を出品
     */
    public function store(Request $request)
    {
        try {
            \Log::info('商品作成処理開始');
            
            $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:product_categories,id',
                'state_id' => 'required|exists:product_states,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            \Log::info('バリデーション成功');
            
            $user = Auth::user();
            \Log::info('ユーザーID: ' . $user->id);
            
            // 画像アップロード処理
            $imagePath = null;
            if ($request->hasFile('image')) {
                \Log::info('画像アップロード開始');
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/productimages', $imageName);
                $imagePath = 'storage/productimages/' . $imageName;
                \Log::info('画像アップロード完了: ' . $imagePath);
            }
            
            // 商品を作成
            \Log::info('商品作成開始');
            $product = Product::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'detail' => $request->description,
                'value' => $request->price,
                'productcategory_id' => $request->category_id,
                'productstate_id' => $request->state_id,
                'image' => $imagePath,
            ]);
            \Log::info('商品作成完了。ID: ' . $product->id);
            
            return redirect()->route('productlist.index')
                ->with('success', '商品を出品しました。');
                
        } catch (\Exception $e) {
            \Log::error('商品作成エラー: ' . $e->getMessage());
            \Log::error('スタックトレース: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->withErrors(['error' => '商品の作成中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}
