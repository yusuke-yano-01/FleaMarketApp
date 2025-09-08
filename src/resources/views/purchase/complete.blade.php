@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="purchase-complete__content">
    <div class="purchase-complete__header">
        <h1>購入完了</h1>
    </div>
    
    <div class="purchase-complete__main">
        <div class="complete-message">
            <div class="success-icon">✓</div>
            <h2>購入手続きが完了しました</h2>
            <p>ご購入いただき、ありがとうございます。</p>
        </div>
        
        <div class="purchase-complete__actions">
            <a href="{{ url('/productlist') }}" class="btn btn-primary">商品一覧に戻る</a>
            <a href="{{ url('/mypage') }}" class="btn btn-secondary">マイページへ</a>
        </div>
    </div>
</div>
@endsection
