<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>FashionablyLate</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body style="background-color: rgb(255, 236, 211);">
  <header class="header">
    <div class="header__inner">
      <div class="header-utilities">
        <a class="header__logo" href="/">
          FashionablyLate
        </a>
        <nav class="header-nav">
            @if (Auth::check())
              <form action="/auth/logout" method="post">
                @csrf
                <button class="header-nav__link">ログアウト</button>
              </form>
            @elseif (request()->path() === 'auth/login')
              <a class="header-nav__link" href="/auth/register">register</a>
            @elseif (request()->path() === 'auth/register')
              <a class="header-nav__link" href="/auth/login">login</a>
            @endif
        </nav>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>