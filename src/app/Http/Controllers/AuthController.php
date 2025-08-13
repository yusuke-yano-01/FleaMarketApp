<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            return redirect('/contactlist');
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
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/auth/login');
    }
}
