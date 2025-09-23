@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="register-form__content">
  <div class="register-form__heading">
    <h2>メール認証が必要です</h2>
  </div>

  @if (session('status') == 'verification-link-sent')
    <div class="login-error">
      <div class="login-error__message">新しい認証リンクを送信しました。</div>
    </div>
  @endif

  <div class="form">
    <p class="verification-message">
      ご登録のメールアドレスに認証リンクを送信しました。<br>
      メール内のリンクをクリックしてアカウントを認証してください。
    </p>
    <div class="form__button">
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="form__button-submit">認証メールを再送信</button>
      </form>
    </div>
  </div>
</div>
@endsection
