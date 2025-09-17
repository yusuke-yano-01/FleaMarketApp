@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="address-edit__content">
    <div class="address-edit__header">
        <a href="{{ route('purchase.show', $product->id) }}" class="back-link">← 購入手続きに戻る</a>
        <h1>住所の変更</h1>
    </div>
    
    <div class="address-edit__main">
        <!-- 住所変更フォーム -->
        <div class="address-edit__form">
            <form method="POST" action="{{ route('purchase.address.update', $product->id) }}">
                @csrf
                <div class="form-group">
                    <label for="postcode" class="form-label">郵便番号 <span class="required">*</span></label>
                    <input type="text" id="postcode" name="postcode" class="form-input" 
                           value="{{ old('postcode', $user->postcode ?? '150-0013') }}" 
                           placeholder="150-0013" required>
                    @error('postcode')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="address" class="form-label">住所 <span class="required">*</span></label>
                    <textarea id="address" name="address" class="form-textarea" 
                              rows="3" placeholder="都道府県、市区町村、番地を入力してください" required>{{ old('address', $user->address ?? '東京都渋谷区恵比寿1-1-1') }}</textarea>
                    @error('address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="building" class="form-label">建物名</label>
                    <input type="text" id="building" name="building" class="form-input" 
                           value="{{ old('building', $user->building ?? '恵比寿ビル101') }}" 
                           placeholder="建物名、部屋番号など">
                    @error('building')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="update-btn">更新する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
