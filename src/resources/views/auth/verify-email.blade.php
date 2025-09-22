<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証 - フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>メール認証が必要です</h1>
            </div>

            <div class="auth-content">
                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success">
                        新しい認証リンクがメールアドレスに送信されました。
                    </div>
                @endif

                <p class="verification-message">
                    ご登録いただいたメールアドレスに認証リンクを送信いたしました。<br>
                    メール内のリンクをクリックして、アカウントを認証してください。
                </p>

                <div class="verification-actions">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            認証メールを再送信
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            ログアウト
                        </button>
                    </form>
                </div>

                <div class="verification-help">
                    <p>メールが届かない場合：</p>
                    <ul>
                        <li>迷惑メールフォルダをご確認ください</li>
                        <li>メールアドレスが正しいかご確認ください</li>
                        <li>数分お待ちいただいてから再送信してください</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
