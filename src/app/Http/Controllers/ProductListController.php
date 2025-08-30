<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductState;
use App\Models\UserProductRelation;
use App\Models\UserProductType;

class ProductListController extends Controller
{
    public function index(Request $request)
    {
        // おすすめ商品を取得（デフォルト表示）
        $recommendedProducts = $this->getRecommendedProducts() ?? collect();
        
        // マイリスト商品を取得（ログインユーザーのみ）
        $mylistProducts = $this->getMylistProducts() ?? collect();
        
        // 検索フラグ（検索結果表示時はfalse）
        $showTabs = true;
        $searchResults = null;
        
        return view('productslist.productlist', compact('recommendedProducts', 'mylistProducts', 'showTabs', 'searchResults')); 
    }

    public function search(Request $request)
    {
        $query = Product::with(['category', 'state']);
        
        // 商品名で検索（商品名のみ）
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        $searchResults = $query->paginate(12);
        
        // 検索結果表示時はタブを非表示
        $showTabs = false;
        $recommendedProducts = null;
        $mylistProducts = null;
        
        return view('productslist.productlist', compact('searchResults', 'showTabs', 'recommendedProducts', 'mylistProducts')); 
    }

    /**
     * 商品詳細ページを表示
     */
    public function show($id)
    {
        $product = Product::with(['category', 'state'])->findOrFail($id);
        
        return view('productslist.productdetail', compact('product'));
    }

    /**
     * おすすめ商品を取得
     */
    private function getRecommendedProducts()
    {
        // 最新の商品をおすすめとして表示
        return Product::with(['category', 'state'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }
    
    /**
     * マイリスト商品を取得
     */
    private function getMylistProducts()
    {
        if (!Auth::check()) {
            // 未ログインの場合は空のコレクションを返す
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 12, 1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }
        
        $user = Auth::user();
        $mylistType = UserProductType::where('name', 'mylist')->first();
        
        if (!$mylistType) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 12, 1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }
        
        // ユーザーのマイリスト商品を取得
        $productIds = UserProductRelation::where('user_id', $user->id)
            ->where('userproducttype_id', $mylistType->id)
            ->pluck('product_id');
        
        // 商品IDが空の場合は空のページネーションを返す
        if ($productIds->isEmpty()) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 12, 1,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }
        
        return Product::with(['category', 'state'])
            ->whereIn('id', $productIds)
            ->paginate(12);
    }
    
    /**
     * マイリストに商品を追加
     */
    public function addToMylist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です。'], 401);
        }
        
        $user = Auth::user();
        $productId = $request->product_id;
        
        $mylistType = UserProductType::where('name', 'mylist')->first();
        
        if (!$mylistType) {
            return response()->json(['error' => 'マイリスト機能が利用できません。'], 500);
        }
        
        // 既にマイリストに追加されているかチェック
        $existingRelation = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('userproducttype_id', $mylistType->id)
            ->first();
        
        if ($existingRelation) {
            return response()->json(['message' => '既にマイリストに追加されています。'], 400);
        }
        
        // マイリストに追加
        UserProductRelation::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'userproducttype_id' => $mylistType->id,
        ]);
        
        return response()->json(['message' => 'マイリストに追加しました。']);
    }
    
    /**
     * マイリストから商品を削除
     */
    public function removeFromMylist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です。'], 401);
        }
        
        $user = Auth::user();
        $productId = $request->product_id;
        
        $mylistType = UserProductType::where('name', 'mylist')->first();
        
        if (!$mylistType) {
            return response()->json(['error' => 'マイリスト機能が利用できません。'], 500);
        }
        
        // マイリストから削除
        $deleted = UserProductRelation::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('userproducttype_id', $mylistType->id)
            ->delete();
        
        if ($deleted) {
            return response()->json(['message' => 'マイリストから削除しました。']);
        } else {
            return response()->json(['message' => 'マイリストに該当商品が見つかりません。'], 404);
        }
    }
}
