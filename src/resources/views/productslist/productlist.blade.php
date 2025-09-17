@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/productlist.css') }}">
@endsection

@section('content')
<div class="productlist__content">
  <div class="productlist__heading">
    <h2>商品一覧</h2>
    @auth
    <div style="margin-top: 10px;">
      <button id="createTestDataBtn" class="form__button-submit" style="padding: 8px 16px; font-size: 14px;">
        テスト用：購入済みデータを作成
      </button>
    </div>
    @endauth
  </div>
  
  <!-- 検索フォーム -->
  <form class="form" action="/productlist/search" method="get">  
    <div class="form__group">
      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="name" value="{{ old('name', request('name')) }}" placeholder="商品名で検索" />
        </div>
      </div>
    </div>
    <div class="form__button">
      <button class="form__button-submit" type="submit">検索</button>
      <a class="form__button-reset" href="/productlist">クリア</a>
    </div>
  </form>

  @if($showTabs)
  <!-- タブメニュー -->
  <div class="tabs">
    <div class="tab-menu">
      <div class="tab-link active" onclick="switchTab('recommended')">
        おすすめ
      </div>
      @auth
      <div class="tab-link" onclick="switchTab('mylist')">
        マイリスト
      </div>
      @endauth
    </div>
  </div>

  <!-- おすすめタブのコンテンツ -->
  <div id="recommended-content" class="tab-content active">
    <div class="product-grid">
      @foreach ($recommendedProducts as $product)
      <div class="product-item">
        <div class="product-image">
          <a href="{{ route('productlist.product', $product->id) }}" class="product-link">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 onerror=this.src="{{ asset('images/no-image.png') }}">
            @if($product->is_purchased ?? false)
              <div class="purchased-icon">
                <i class="fas fa-check-circle"></i>
                <span>購入済み</span>
              </div>
            @endif
          </a>
        </div>
        <div class="product-name">{{ $product->name }}</div>
      </div>
      @endforeach
    </div>
    <div class="pagination">
      {{ $recommendedProducts->links() }}
    </div>
  </div>

  <!-- マイリストタブのコンテンツ -->
  @auth
  <div id="mylist-content" class="tab-content">
    @if($mylistProducts && count($mylistProducts) > 0)
    <div class="product-grid">
      @foreach ($mylistProducts as $product)
      <div class="product-item">
        <div class="product-image">
          <a href="{{ route('productlist.product', $product->id) }}" class="product-link">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 onerror=this.src="{{ asset('images/no-image.png') }}">
            @if($product->is_purchased ?? false)
              <div class="purchased-icon">
                <i class="fas fa-check-circle"></i>
                <span>購入済み</span>
              </div>
            @endif
          </a>
        </div>
        <div class="product-name">{{ $product->name }}</div>
      </div>
      @endforeach
    </div>
    @if(method_exists($mylistProducts, 'links'))
    <div class="pagination">
      {{ $mylistProducts->links() }}
    </div>
    @endif
    @else
    <div class="empty-mylist">
      <p>マイリストに商品がありません。</p>
    </div>
    @endif
  </div>
  @endauth

  @else
  <!-- 検索結果表示 -->
  <div class="search-results">
    <h3>検索結果 ({{ $searchResults->total() }}件)</h3>
    
    @if($searchResults->count() > 0)
    <div class="product-grid">
      @foreach ($searchResults as $product)
      <div class="product-item">
        <div class="product-image">
          <a href="{{ route('productlist.product', $product->id) }}" class="product-link">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 onerror=this.src="{{ asset('images/no-image.png') }}">
            @if($product->is_purchased ?? false)
              <div class="purchased-icon">
                <i class="fas fa-check-circle"></i>
                <span>購入済み</span>
              </div>
            @endif
          </a>
        </div>
        <div class="product-name">{{ $product->name }}</div>
      </div>
      @endforeach
    </div>
    <div class="pagination">
      {{ $searchResults->appends(request()->query())->links() }}
    </div>
    @else
    <p>検索条件に一致する商品が見つかりませんでした。</p>
    @endif
  </div>
  @endif
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    // タブ切り替え機能
    window.switchTab = function(tabName) {
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        event.target.classList.add('active');
        document.getElementById(tabName + '-content').classList.add('active');
    }
    
    // テスト用購入済みデータ作成ボタン
    document.getElementById('createTestDataBtn').addEventListener('click', function() {
        fetch('/test/create-purchased-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || data.error);
            if (data.message) {
                location.reload(); // ページをリロードしてアイコンを確認
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。');
        });
    });
});
</script>
@else
<script>
document.addEventListener('DOMContentLoaded', function() {
    // タブ切り替え機能のみ
    window.switchTab = function(tabName) {
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('active');
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        event.target.classList.add('active');
        document.getElementById(tabName + '-content').classList.add('active');
    }
});
</script>
@endauth

@endsection