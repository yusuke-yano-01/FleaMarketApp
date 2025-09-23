<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductState;
use App\Models\UserProductRelation;
use App\Models\UserProductType;

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
            $request->validate([
                'name' => 'required|string|max:255',
                'brand' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:product_categories,id',
                'state_id' => 'required|exists:product_states,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            $user = Auth::user();
            
            // 画像アップロード処理
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                
                // 既存の商品と同じ形式で保存（productimages/日付/フォルダ/ファイル名）
                $dateFolder = date('Ymd');
                $randomFolder = sprintf('%03d', rand(1, 999));
                $storagePath = "public/productimages/{$dateFolder}/{$randomFolder}";
                
                $image->storeAs($storagePath, $imageName);
                $imagePath = "productimages/{$dateFolder}/{$randomFolder}/{$imageName}";
            }
            
            // 商品を作成
            $product = Product::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'detail' => $request->description,
                'value' => $request->price,
                'productcategory_id' => $request->category_id,
                'productstate_id' => $request->state_id,
                'image' => $imagePath,
            ]);
            
            // UserProductRelationにSellerレコードを作成（userproducttype_id = 1）
            UserProductRelation::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'userproducttype_id' => 1, // Seller
            ]);
            
            return redirect()->route('productlist.index')
                ->with('success', '商品を出品しました。');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => '商品の作成中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}
