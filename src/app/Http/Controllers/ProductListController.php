<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductState;
use App\Models\UserProductRelation;
use App\Models\UserProductType;
use App\Models\Comment;

class ProductListController extends Controller
{
    public function index(Request $request)
    {
        // 認証済みユーザーの住所チェック
        if (Auth::check()) {
            $user = Auth::user();
            if (empty($user->postcode) || empty($user->address) || !$user->registeredflg) {
                return redirect()->route('profile.setup')
                    ->with('info', 'プロフィール設定を完了してください。');
            }
        }
        
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
        $query = Product::with(['category', 'state']); // 購入済み商品も含む
        
        // 商品名で検索（商品名のみ）
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        $searchResults = $query->paginate(12);
        
        // 購入済みフラグを追加
        $this->addPurchasedFlags($searchResults);
        
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
        try {
            // まず商品のみを取得
            $product = Product::findOrFail($id);
            
            // カテゴリーと状態を個別に取得
            $product->load(['category', 'state']);
            
            // コメントを個別に取得（エラーが発生した場合は空のコレクションを返す）
            try {
                $product->load(['comments.user']);
            } catch (\Exception $e) {
                \Log::error('コメント取得エラー: ' . $e->getMessage());
                $product->setRelation('comments', collect());
            }
            
            return view('productslist.productdetail', compact('product'));
            
        } catch (\Exception $e) {
            \Log::error('商品詳細ページエラー: ' . $e->getMessage());
            abort(404, '商品が見つかりません。');
        }
    }

    /**
     * おすすめ商品を取得
     */
    private function getRecommendedProducts()
    {
        // 最新の商品をおすすめとして表示（購入済み商品も含む）
        $products = Product::with(['category', 'state'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // 購入済みフラグを追加
        $this->addPurchasedFlags($products);
            
        // デバッグ用：取得された商品数をログに出力
        \Log::info('おすすめ商品数: ' . $products->count());
        
        // デバッグ用：商品ID一覧をログに出力
        $productIds = $products->pluck('id')->toArray();
        \Log::info('商品ID一覧: ' . json_encode($productIds));
        
        return $products;
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
        
        $products = Product::with(['category', 'state'])
            ->whereIn('id', $productIds)
            ->paginate(12);
        
        // 購入済みフラグを追加
        $this->addPurchasedFlags($products);
        
        return $products;
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

    /**
     * コメントを投稿
     */
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ], [
            'comment.required' => 'コメントを入力してください。',
            'comment.max' => 'コメントは500文字以内で入力してください。',
        ]);

        $product = Product::findOrFail($id);
        
        // コメントを直接作成
        $comment = Comment::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'コメントを投稿しました。'
        ]);
    }
    
    /**
     * 商品に購入済みフラグを追加
     */
    private function addPurchasedFlags($products)
    {
        if (!Auth::check()) {
            // 未ログインの場合はすべての商品に購入済みフラグをfalseに設定
            foreach ($products as $product) {
                $product->is_purchased = false;
            }
            return;
        }
        
        $user = Auth::user();
        $buyerType = UserProductType::where('name', 'Buyer')->first();
        
        Log::info("User ID: {$user->id}, Buyer Type: " . ($buyerType ? $buyerType->id : 'null'));
        
        if (!$buyerType) {
            Log::warning("Buyerタイプが見つかりません");
            // Buyerタイプが存在しない場合はすべての商品に購入済みフラグをfalseに設定
            foreach ($products as $product) {
                $product->is_purchased = false;
            }
            return;
        }
        
        // ユーザーが購入した商品IDを取得
        $purchasedProductIds = UserProductRelation::where('user_id', $user->id)
            ->where('userproducttype_id', $buyerType->id)
            ->pluck('product_id')
            ->toArray();
        
        // 各商品に購入済みフラグを設定
        foreach ($products as $product) {
            $product->is_purchased = in_array($product->id, $purchasedProductIds);
            if ($product->is_purchased) {
                Log::info("購入済み商品発見: Product ID {$product->id}, Name: {$product->name}");
            }
        }
        
        Log::info("購入済み商品ID一覧: " . json_encode($purchasedProductIds));
    }
    
    /**
     * テスト用：購入済みデータを作成
     */
    public function createTestPurchasedData()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'ログインが必要です。']);
        }
        
        $user = Auth::user();
        $buyerType = UserProductType::where('name', 'Buyer')->first();
        
        if (!$buyerType) {
            // Buyerタイプを作成
            $buyerType = UserProductType::create(['name' => 'Buyer']);
        }
        
        // 最初の商品を購入済みとしてマーク
        $firstProduct = Product::first();
        if ($firstProduct) {
            // 既存の関係を削除してから新規作成
            UserProductRelation::where('user_id', $user->id)
                ->where('product_id', $firstProduct->id)
                ->where('userproducttype_id', $buyerType->id)
                ->delete();
                
            UserProductRelation::create([
                'user_id' => $user->id,
                'product_id' => $firstProduct->id,
                'userproducttype_id' => $buyerType->id,
            ]);
            
            return response()->json([
                'message' => "商品「{$firstProduct->name}」を購入済みとしてマークしました。",
                'product_id' => $firstProduct->id
            ]);
        }
        
        return response()->json(['error' => '商品が見つかりません。']);
    }
}
