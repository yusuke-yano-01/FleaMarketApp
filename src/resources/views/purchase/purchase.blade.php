@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('js')
<script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')
<div class="purchase__content">
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div id="payment-error" class="alert alert-danger" style="display: none;">
        お支払い方法を選択してください。
    </div>
    
    <div class="purchase__main">
        <div class="purchase__left-column">
            <!-- 商品情報 -->
            <div class="purchase__product-info">
                <div class="purchase__header">
                    <a href="{{ url('/productlist/product/' . $product->id) }}" class="back-link">← 商品詳細に戻る</a>
                </div>
                <h2>購入商品</h2>
                <div class="product-card">
                    <div class="product-image">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                             alt="{{ $product->name }}" 
                             onerror="this.src='{{ asset('images/no-image.png') }}'">
                    </div>
                    <div class="product-details">
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <div class="product-price">¥{{ number_format($product->value) }} <span class="tax-included">(税込)</span></div>
                    </div>
                </div>
            </div>
            
            <!-- 支払い方法 -->
            <div class="purchase__payment-info">
                <h2>お支払い方法</h2>
                <div class="payment-details">
                    <div class="form-group">
                        <label for="payment_method" class="form-label">お支払い方法 <span class="required">*</span></label>
                        <select id="payment_method" name="payment_method" class="form-select" required onchange="updatePaymentMethod()">
                            <option value="">選択してください</option>
                            <option value="convenience_store">コンビニ払い</option>
                            <option value="card">カード支払い</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- 配送先情報 -->
            <div class="purchase__shipping-info">
                <h2>配送先</h2>
                <a href="{{ route('purchase.address.edit', $product->id) }}" class="change-address-btn">住所を変更する</a>
                <div class="shipping-address-display">
                    <div class="address-info">
                        <div class="detail-item">
                            <label class="detail-label">郵便番号:</label>
                            <span class="detail-value">{{ Auth::user()->postcode ?? '郵便番号を登録してください' }}</span>
                        </div>
                        <div class="detail-item">
                            <label class="detail-label">住所:</label>
                            <span class="detail-value">{{ Auth::user()->address ?? '住所を登録してください' }}{{ Auth::user()->building ? ' ' . Auth::user()->building : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 合計金額 -->
        <div class="purchase__right-column">
            <div class="purchase__total">
                <div class="total-summary">
                    <div class="total-item">
                        <span class="total-label">商品代金:</span>
                        <span class="total-value">¥{{ number_format($product->value) }}</span>
                    </div>
                    <div class="total-item">
                        <span class="total-label">支払い方法:</span>
                        <span class="total-value" id="selected-payment-method">未選択</span>
                    </div>
                </div>
                
                <!-- 購入確定 -->
                <div class="purchase__actions">
                    <form method="POST" action="{{ route('purchase.process', $product->id) }}" id="purchase-form">
                        @csrf
                        <input type="hidden" name="payment_method" id="payment_method_hidden">
                        <input type="hidden" name="shipping_postal" value="{{ Auth::user()->postcode ?? '150-0013' }}">
                        <input type="hidden" name="shipping_address" value="{{ Auth::user()->address ?? '東京都渋谷区恵比寿1-1-1' }}{{ Auth::user()->building ? ' ' . Auth::user()->building : '' }}">
                        <button type="button" class="purchase-confirm-btn" onclick="submitPurchase()">購入する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePaymentMethod() {
    const select = document.getElementById('payment_method');
    const display = document.getElementById('selected-payment-method');
    const hiddenInput = document.getElementById('payment_method_hidden');
    const errorDiv = document.getElementById('payment-error');
    
    const paymentMethods = {
        'convenience_store': 'コンビニ払い',
        'card': 'カード支払い'
    };
    
    display.textContent = paymentMethods[select.value] || '未選択';
    hiddenInput.value = select.value;
    
    // 支払い方法が選択されたらエラーメッセージを非表示にする
    if (select.value) {
        errorDiv.style.display = 'none';
    }
}

async function submitPurchase() {
    const select = document.getElementById('payment_method');
    const errorDiv = document.getElementById('payment-error');
    
    // エラーメッセージを非表示にする
    errorDiv.style.display = 'none';
    
    // 支払い方法が選択されているかチェック
    if (!select.value) {
        errorDiv.style.display = 'block';
        // エラーメッセージまでスクロール
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    // 支払い方法に応じて処理を分岐
    if (select.value === 'card') {
        await processStripePayment();
    } else if (select.value === 'convenience_store') {
        await processConveniencePayment();
    }
}

async function processStripePayment() {
    try {
        // Stripe Checkoutセッションを作成
        const response = await fetch(`/purchase/{{ $product->id }}/checkout`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_method: 'card'
            })
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error:', response.status, errorText);
            throw new Error(`サーバーエラー (${response.status}): ${errorText}`);
        }

        const data = await response.json();

        if (data.error) {
            throw new Error(data.error);
        }

        if (!data.session_id) {
            throw new Error('セッションIDが取得できませんでした。');
        }

        // Stripe Checkoutにリダイレクト
        const stripe = Stripe('{{ config("services.stripe.key") }}');
        const result = await stripe.redirectToCheckout({
            sessionId: data.session_id
        });

        if (result.error) {
            throw new Error(result.error.message);
        }
    } catch (error) {
        alert('決済処理でエラーが発生しました: ' + error.message);
        console.error('Stripe payment error:', error);
    }
}

async function processConveniencePayment() {
    // コンビニ払いの場合は従来のフォーム送信
    const form = document.getElementById('purchase-form');
    const hiddenInput = document.getElementById('payment_method_hidden');
    hiddenInput.value = 'convenience_store';
    form.submit();
}
</script>

@endsection
