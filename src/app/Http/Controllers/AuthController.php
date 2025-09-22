<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(UsersRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/productlist');
        }

        return back()->withErrors([
            'login' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(UsersRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録イベントを発火してメール認証通知を送信
        event(new Registered($user));

        // 自動ログイン後、メール認証案内画面へ
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/auth/login');
    }
}
