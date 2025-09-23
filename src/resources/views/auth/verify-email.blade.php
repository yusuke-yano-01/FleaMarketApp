@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="register-form__content">
  @if (session('status') == 'verification-link-sent')
    <div class="login-error">
      <div class="login-error__message">新しい認証リンクを送信しました。</div>
    </div>
  @endif

  <p class="verify-message">
    ご登録のメールアドレスに認証リンクを送信しています。<br>
    メール内のリンクをクリックしてアカウントを認証してください。
  </p>

  <form method="POST" action="{{ route('verification.send') }}" class="verify-resend-form">
    @csrf
    <button type="submit" class="verify-button">認証メールを再送信</button>
  </form>
</div>
@endsection
