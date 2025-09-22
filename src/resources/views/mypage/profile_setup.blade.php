<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール設定</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>
    <div class="container">
        <!-- 見出し：中央表示 -->
        <div class="header">
            <h2>プロフィール設定</h2>
        </div>

        @if (session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- ユーザーアイコン設定 -->
            <div class="user-icon-section">
                <div class="user-icon-container">
                    <div class="user-icon">
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
                    <button type="button" class="change-image-btn" onclick="document.getElementById('image').click()">
                        画像を選択する
                    </button>
                </div>
                <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)" style="display: none;">
            </div>

            <!-- ユーザー名：左揃え -->
            <div class="form-group">
                <label for="name" class="form-label">ユーザー名 <span class="required">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $user->name) }}" 
                       placeholder="例: 田中太郎" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 郵便番号：左揃え -->
            <div class="form-group">
                <label for="postcode" class="form-label">郵便番号 <span class="required">*</span></label>
                <input type="text" class="form-control @error('postcode') is-invalid @enderror" 
                       id="postcode" name="postcode" value="{{ old('postcode', $user->postcode) }}" 
                       placeholder="例: 100-0001" required>
                @error('postcode')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 住所：左揃え -->
            <div class="form-group">
                <label for="address" class="form-label">住所 <span class="required">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                       id="address" name="address" value="{{ old('address', $user->address) }}" 
                       placeholder="例: 東京都千代田区千代田1-1-1" required>
                @error('address')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- 建物名：左揃え -->
            <div class="form-group">
                <label for="building" class="form-label">建物名・部屋番号</label>
                <input type="text" class="form-control @error('building') is-invalid @enderror" 
                       id="building" name="building" value="{{ old('building', $user->building) }}" 
                       placeholder="例: サンプルビル101">
                @error('building')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                更新する
            </button>
        </form>
    </div>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const userIcon = document.querySelector('.user-icon');
                userIcon.innerHTML = `<img src="${e.target.result}" alt="プロフィール画像" id="preview-image">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
