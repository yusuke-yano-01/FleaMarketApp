<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品の出品</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/productform.css') }}">
</head>
<body>
    <div class="container">
        <!-- 見出し：中央表示 -->
        <div class="header">
            <h2>商品の出品</h2>
        </div>

        @if (session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('productform.store') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
            @csrf
            
            <!-- 商品画像設定 -->
            <div class="product-image-section">
                <div class="product-image-container">
                    <div class="product-image-preview" id="imagePreview" onclick="document.getElementById('image').click()">
                        <span>画像を選択してください</span>
                        <button type="button" class="change-image-btn">
                            画像を選択する
                        </button>
                    </div>
                </div>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)" class="hidden">
            </div>

            <!-- 商品の詳細 -->
            <div class="product-details">
                <h3>商品の詳細</h3>
                
                <!-- カテゴリー -->
                <div class="form-group">
                    <label class="form-label">カテゴリー <span class="required">*</span></label>
                    <div class="category-options">
                        @foreach($categories as $category)
                            <label class="category-option">
                                <input type="radio" name="category_id" value="{{ $category->id }}" 
                                       {{ old('category_id') == $category->id ? 'checked' : '' }} 
                                       class="category-radio" required>
                                <span class="category-label">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('category_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 商品の状態 -->
                <div class="form-group">
                    <label for="state_id" class="form-label">商品の状態 <span class="required">*</span></label>
                    <select class="form-control @error('state_id') is-invalid @enderror" id="state_id" name="state_id" required>
                        <option value="">選択してください</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('state_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 商品名 -->
                <div class="form-group">
                    <label for="name" class="form-label">商品名 <span class="required">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" 
                           placeholder="商品名を入力してください" required>
                    @error('name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ブランド名 -->
                <div class="form-group">
                    <label for="brand" class="form-label">ブランド名 <span class="required">*</span></label>
                    <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                           id="brand" name="brand" value="{{ old('brand') }}" 
                           placeholder="ブランド名を入力してください" required>
                    @error('brand')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 商品の説明 -->
                <div class="form-group">
                    <label for="description" class="form-label">商品の説明 <span class="required">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" 
                              placeholder="商品の説明を入力してください" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 販売価格 -->
                <div class="form-group">
                    <label for="price" class="form-label">販売価格 <span class="required">*</span></label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                           id="price" name="price" value="{{ old('price') }}" 
                           placeholder="0" min="0" step="1" required>
                    @error('price')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="submit-btn">
                出品する
            </button>
        </form>
    </div>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `<img src="${e.target.result}" alt="商品画像" id="preview-image">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function validateForm() {
        console.log('フォーム送信開始');
        
        // 必須項目のチェック
        const name = document.querySelector('input[name="name"]').value;
        const brand = document.querySelector('input[name="brand"]').value;
        const description = document.querySelector('textarea[name="description"]').value;
        const price = document.querySelector('input[name="price"]').value;
        const categoryId = document.querySelector('input[name="category_id"]:checked');
        const stateId = document.querySelector('select[name="state_id"]').value;
        const image = document.querySelector('input[name="image"]').files[0];
        
        console.log('フォームデータ:', {
            name, brand, description, price,
            categoryId: categoryId ? categoryId.value : null,
            stateId: stateId,
            image: image ? image.name : null
        });
        
        if (!name || !brand || !description || !price || !categoryId || !stateId || !image) {
            alert('すべての項目を入力してください。');
            return false;
        }
        
        console.log('バリデーション成功、送信します');
        return true;
    }
    </script>
</body>
</html>
