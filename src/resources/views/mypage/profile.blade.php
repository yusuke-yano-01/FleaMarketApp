@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <!-- ユーザープロフィール（上部） -->
    <div class="user-profile-header">
        <div class="user-main-info">
            <div class="user-avatar">
                @php
                    $imagePath = asset('storage/userimages/default_user_icon.png');
                    if ($user->image) {
                        $fullPath = public_path($user->image);
                        if (file_exists($fullPath)) {
                            $imagePath = asset($user->image);
                        }
                    }
                @endphp
                <img src="{{ $imagePath }}" alt="ユーザー画像" class="avatar-image" onerror="this.src='{{ asset('storage/userimages/default_user_icon.png') }}'">
            </div>
            <div class="user-profile-info">
                <h1 class="user-name">{{ $user->name }}</h1>
            </div>
        </div>
        <div class="user-actions">
            <a href="/mypage/profile/edit" class="edit-profile-btn">プロフィールを編集</a>
        </div>
    </div>
    
    <!-- 商品タブ -->
    <div class="product-tabs">
        <div class="tab-nav">
            <button class="tab-btn active" data-tab="sold">出品した商品</button>
            <button class="tab-btn" data-tab="bought">購入した商品</button>
        </div>
        
        <!-- 出品した商品 -->
        <div class="tab-content active" id="sold-tab">
            <div class="product-grid">
                @if(isset($soldProducts) && $soldProducts->count() > 0)
                    @foreach($soldProducts as $product)
                    <div class="product-item">
                        <a href="{{ route('productlist.product', $product->id) }}">
                            <img src="{{ $product->image ? asset('storage/productimages/' . $product->image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" class="product-image" onerror="this.src='/images/no-image.png'">
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">¥{{ number_format($product->value) }}</div>
                                <div class="product-category">{{ $product->category->name ?? 'カテゴリなし' }}</div>
                                <div class="product-state">{{ $product->state->name ?? '状態なし' }}</div>
                                @if($product->soldflg)
                                    <div class="sold-badge">売り切れ</div>
                                @endif
                            </div>
                        </a>
                    </div>
                    @endforeach
                @else
                    <div class="no-products">まだ出品した商品がありません</div>
                @endif
            </div>
        </div>
        
        <!-- 購入した商品 -->
        <div class="tab-content" id="bought-tab">
            <div class="product-grid">
                @if(isset($boughtProducts) && $boughtProducts->count() > 0)
                    @foreach($boughtProducts as $product)
                    <div class="product-item">
                        <a href="{{ route('productlist.product', $product->id) }}">
                            <img src="{{ $product->image ? asset('storage/productimages/' . $product->image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" class="product-image" onerror="this.src='/images/no-image.png'">
                            <div class="product-info">
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">¥{{ number_format($product->value) }}</div>
                                <div class="product-category">{{ $product->category->name ?? 'カテゴリなし' }}</div>
                                <div class="product-state">{{ $product->state->name ?? '状態なし' }}</div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                @else
                    <div class="no-products">まだ購入した商品がありません</div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // すべてのタブボタンとコンテンツから active クラスを削除
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // クリックされたタブボタンにactiveクラスを追加
            this.classList.add('active');
            
            // 対応するコンテンツにactiveクラスを追加
            document.getElementById(targetTab + '-tab').classList.add('active');
        });
    });
});
</script>
@endsection