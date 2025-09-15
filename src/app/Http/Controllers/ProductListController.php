<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $query = Product::with(['category', 'state'])
            ->where('soldflg', false); // 売却済み商品を除外
        
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
        // 最新の商品をおすすめとして表示（売却済み商品を除外）
        $products = Product::with(['category', 'state'])
            ->where('soldflg', false)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
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
        
        return Product::with(['category', 'state'])
            ->whereIn('id', $productIds)
            ->where('soldflg', false) // 売却済み商品を除外
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
        
        // デフォルト住所を取得（最初の住所を使用）
        $defaultAddress = \DB::table('address')->first();
        if (!$defaultAddress) {
            return response()->json(['error' => '住所データが見つかりません。'], 500);
        }
        
        // マイリストに追加
        UserProductRelation::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'userproducttype_id' => $mylistType->id,
            'address_id' => $defaultAddress->id,
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
}
