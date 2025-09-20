@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
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
            
            <!-- お気に入り・コメント情報 -->
            <div class="product-stats">
                <div class="stats-item">
                    @auth
                        <button class="mylist-btn {{ $product->is_in_mylist ? 'in-mylist' : '' }}" data-product-id="{{ $product->id }}">
                            <span class="star-icon">{{ $product->is_in_mylist ? '★' : '☆' }}</span>
                            <span class="mylist-count">{{ $product->mylistCount ?? 0 }}</span>
                        </button>
                    @else
                        <a href="{{ url('/auth/login') }}" class="mylist-btn">
                            <span class="star-icon">☆</span>
                            <span class="mylist-count">{{ $product->mylistCount ?? 0 }}</span>
                        </a>
                    @endauth
                </div>
                <div class="stats-item">
                    <div class="comment-stats">
                        <span class="comment-icon">💬</span>
                        <span class="comment-count">{{ $product->comments->count() }}</span>
                    </div>
                </div>
            </div>
            
            <!-- 購入手続きボタン -->
            <div class="productdetail__actions">
                @if($product->soldflg)
                    <button class="purchase-btn purchase-btn--sold" disabled>売り切れ</button>
                @else
                    @auth
                        <a href="{{ route('purchase.show', $product->id) }}" class="purchase-btn">購入手続きへ</a>
                    @else
                        <a href="{{ url('/auth/login') }}" class="purchase-btn">購入手続きへ</a>
                    @endauth
                @endif
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
                    @if($product->comments && $product->comments->count() > 0)
                        @foreach($product->comments->sortByDesc('created_at')->take(3) as $comment)
                            <div class="comment-item">
                                <div class="comment-header">
                                    <span class="comment-author">{{ $comment->user ? $comment->user->name : '不明なユーザー' }}</span>
                                    <span class="comment-date">{{ $comment->created_at ? $comment->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') : '' }}</span>
                                </div>
                                <div class="comment-content">{{ $comment->comment }}</div>
                            </div>
                        @endforeach
                        
                        @if($product->comments->count() > 3)
                            <div id="hidden-comments" style="display: none;">
                                @foreach($product->comments->sortByDesc('created_at')->skip(3) as $comment)
                                    <div class="comment-item">
                                        <div class="comment-header">
                                            <span class="comment-author">{{ $comment->user ? $comment->user->name : '不明なユーザー' }}</span>
                                            <span class="comment-date">{{ $comment->created_at ? $comment->created_at->setTimezone('Asia/Tokyo')->format('Y/m/d H:i') : '' }}</span>
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
    // マイリストボタン
    const mylistBtn = document.querySelector('.mylist-btn');
    if (mylistBtn && mylistBtn.tagName === 'BUTTON') {
        mylistBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const isInMylist = this.classList.contains('in-mylist');
            
            // 重複送信防止
            if (this.disabled) {
                return;
            }
            this.disabled = true;
            
            const url = isInMylist ? '/productlist/mylist/remove' : '/productlist/mylist/add';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => {
                if (response.status === 401) {
                    alert('ログインが必要です。');
                    window.location.href = '/auth/login';
                    return;
                }
                if (response.status === 302) {
                    alert('プロフィール設定を完了してください。');
                    window.location.href = '/profile/setup';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.message) {
                    // ボタンの状態を切り替え
                    if (isInMylist) {
                        // マイリストから削除
                        this.classList.remove('in-mylist');
                        this.innerHTML = '<span class="star-icon">☆</span><span class="mylist-count">' + (parseInt(this.querySelector('.mylist-count').textContent) - 1) + '</span>';
                    } else {
                        // マイリストに追加
                        this.classList.add('in-mylist');
                        this.innerHTML = '<span class="star-icon">★</span><span class="mylist-count">' + (parseInt(this.querySelector('.mylist-count').textContent) + 1) + '</span>';
                    }
                    
                    alert(data.message);
                } else if (data && data.error) {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました。');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    }
    
    // コメント投稿フォーム
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // 重複送信防止：ボタンを無効化
            if (submitBtn.disabled) {
                return;
            }
            submitBtn.disabled = true;
            submitBtn.textContent = '送信中...';
            
            const formData = new FormData(this);
            const comment = formData.get('comment').trim();
            
            if (!comment) {
                alert('コメントを入力してください。');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
                return;
            }
            
            fetch('{{ route("product.comment", $product->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.status === 401) {
                    alert('ログインが必要です。');
                    window.location.href = '/auth/login';
                    return;
                }
                if (response.status === 302) {
                    alert('プロフィール設定を完了してください。');
                    window.location.href = '/profile/setup';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    // 新しいコメントを追加
                    addCommentToPage(data.comment);
                    this.reset();
                    
                    // コメントカウントを更新
                    const commentCountElement = document.querySelector('.comment-count');
                    const currentCount = parseInt(commentCountElement.textContent);
                    commentCountElement.textContent = currentCount + 1;
                } else {
                    alert('コメントの投稿に失敗しました。');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました。');
            })
            .finally(() => {
                // 送信完了後にボタンを有効化
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
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
            <span class="comment-author">${comment.user.name}</span>
            <span class="comment-date">${new Date(comment.created_at).toLocaleString('ja-JP', {timeZone: 'Asia/Tokyo'})}</span>
        </div>
        <div class="comment-content">${comment.comment}</div>
    `;
    
    // 新しいコメントを一番上に追加
    commentsList.insertBefore(commentElement, commentsList.firstChild);
}
</script>
@endsection
