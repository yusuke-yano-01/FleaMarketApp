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
                <button class="purchase-btn">購入手続きへ</button>
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
            
            <!-- コメントフォーム -->
            <div class="comment-form">
                <textarea class="comment-textarea" ></textarea>
                <button class="comment-submit-btn">コメントを送信する</button>
            </div>
        </div>
    </div>
</div>
@endsection
