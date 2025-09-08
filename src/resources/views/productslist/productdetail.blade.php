@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/senitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/productdetail.css') }}">
@endsection

@section('content')
<div class="productdetail__content">
    <div class="productdetail__header">
        <a href="{{ url('/productlist') }}" class="back-link">← 商品一覧に戻る</a>
    </div>
    
    <div class="productdetail__main">
        <!-- 左半分：商品画像 -->
        <div class="productdetail__image">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 onerror="this.src='{{ asset('images/no-image.png') }}'">
        </div>
        
        <!-- 右半分：商品詳細 -->
        <div class="productdetail__info">
            <!-- タイトル：商品名 -->
            <h2 class="productdetail__name">{{ $product->name }}</h2>
            
            <!-- ブランド名 -->
            <div class="brand-name">ブランド名: {{ $product->brand ?? '未設定' }}</div>
            
            <!-- 価格 -->
            <div class="detail-item">
                <span class="detail-value price">¥{{ number_format($product->value) }} <span class="tax-included">(税込)</span></span>
            </div>
            
            <!-- 購入手続きボタン -->
            <div class="productdetail__actions">
                @auth
                    <a href="{{ route('purchase.show', $product->id) }}" class="purchase-btn">購入手続きへ</a>
                @else
                    <a href="{{ url('/auth/login') }}" class="purchase-btn">購入手続きへ</a>
                @endauth
            </div>
            
            <!-- 商品説明タイトル（太文字） -->
            <h3 class="section-title">商品説明</h3>
            
            <!-- 商品説明本文 -->
            <div class="productdetail__description">
                <p>{{ $product->detail }}</p>
            </div>
            
            <!-- 商品の情報タイトル（太文字） -->
            <h3 class="section-title">商品の情報</h3>
            
            <!-- カテゴリー -->
            <div class="detail-item">
                <span class="detail-label">カテゴリー:</span>
                <span class="detail-value">{{ $product->category->name ?? '未設定' }}</span>
            </div>
            
            <!-- 商品の状態 -->
            <div class="detail-item">
                <span class="detail-label">商品の状態:</span>
                <span class="detail-value">{{ $product->state->name ?? '未設定' }}</span>
            </div>
            
            <!-- 商品へのコメントタイトル -->
            <h3 class="section-title">商品へのコメント</h3>
            
            <!-- コメント一覧 -->
            <div class="comments-section">
                <h3>コメント一覧</h3>
                <div id="comments-list">
                    @if($product->comments->count() > 0)
                        @foreach($product->comments->sortByDesc('created_at')->take(3) as $comment)
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-author">{{ $comment->userProductRelation->user->name }}</span>
                                    <span class="comment-date">{{ $comment->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                                </div>
                                <div class="comment-content">{{ $comment->comment }}</div>
                            </div>
                        @endforeach
                        
                        @if($product->comments->count() > 3)
                            <div id="hidden-comments" style="display: none;">
                                @foreach($product->comments->sortByDesc('created_at')->skip(3) as $comment)
                                    <div class="comment-item">
                                        <div class="comment-header">
                                            <span class="comment-author">{{ $comment->userProductRelation->user->name }}</span>
                                            <span class="comment-date">{{ $comment->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                                        </div>
                                        <div class="comment-content">{{ $comment->comment }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <button id="show-more-comments" class="show-more-btn">さらに表示する（{{ $product->comments->count() - 3 }}件）</button>
                        @endif
                    @else
                        <p class="no-comments">まだコメントはありません。</p>
                    @endif
                </div>
            </div>

            <!-- コメントフォーム -->
            @auth
            <div class="comment-form">
                <h3>コメントを投稿</h3>
                <form id="comment-form">
                    @csrf
                    <textarea class="comment-textarea" name="comment" placeholder="コメントを入力してください（500文字以内）" maxlength="500" required></textarea>
                    <button type="submit" class="comment-submit-btn">コメントを送信する</button>
                </form>
            </div>
            @else
            <div class="comment-login-prompt">
                <p>コメントを投稿するには<a href="{{ url('/auth/login') }}">ログイン</a>してください。</p>
            </div>
            @endauth
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // コメント投稿フォーム
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const comment = formData.get('comment').trim();
            
            if (!comment) {
                alert('コメントを入力してください。');
                return;
            }
            
            fetch('{{ route("product.comment", $product->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 新しいコメントを追加
                    addCommentToPage(data.comment);
                    this.reset();
                } else {
                    alert('コメントの投稿に失敗しました。');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました。');
            });
        });
    }
    
    // さらに表示するボタン
    const showMoreBtn = document.getElementById('show-more-comments');
    if (showMoreBtn) {
        showMoreBtn.addEventListener('click', function() {
            document.getElementById('hidden-comments').style.display = 'block';
            this.style.display = 'none';
        });
    }
});

function addCommentToPage(comment) {
    const commentsList = document.getElementById('comments-list');
    const noComments = commentsList.querySelector('.no-comments');
    
    if (noComments) {
        noComments.remove();
    }
    
    const commentElement = document.createElement('div');
    commentElement.className = 'comment-item';
    commentElement.innerHTML = `
        <div class="comment-header">
            <span class="comment-author">${comment.user_product_relation.user.name}</span>
            <span class="comment-date">${new Date(comment.created_at).toLocaleString('ja-JP', {timeZone: 'Asia/Tokyo'})}</span>
        </div>
        <div class="comment-content">${comment.comment}</div>
    `;
    
    // 新しいコメントを一番上に追加
    commentsList.insertBefore(commentElement, commentsList.firstChild);
}
</script>
@endsection
