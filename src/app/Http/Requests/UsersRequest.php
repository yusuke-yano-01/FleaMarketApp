<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // リクエストパスに基づいてルールを分ける
        if ($this->is('auth/register')) {
            return $this->registerRules();
        } elseif ($this->is('auth/login')) {
            return $this->loginRules();
        }
        
        // デフォルトはregister用のルール
        return $this->registerRules();
    }
    
    /**
     * Register用のバリデーションルール
     *
     * @return array
     */
    private function registerRules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
    
    /**
     * Login用のバリデーションルール
     *
     * @return array
     */
    private function loginRules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
        
    public function messages()
    {
        // リクエストパスに基づいてメッセージを分ける
        if ($this->is('auth/register')) {
            return $this->registerMessages();
        } elseif ($this->is('auth/login')) {
            return $this->loginMessages();
        }
        
        // デフォルトはregister用のメッセージ
        return $this->registerMessages();
    }
    
    /**
     * Register用のエラーメッセージ
     *
     * @return array
     */
    private function registerMessages()
    {
        return [
            'name.required' => '名前を入力してください',
            'name.string' => '名前を文字列で入力してください',
            'name.max' => '名前を255文字以下で入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'email.unique' => 'メールアドレスが既に存在しています',
            'password.required' => 'パスワードを入力してください',
            'password.string' => 'パスワードを文字列で入力してください',
            'password.min' => 'パスワードを8文字以上で入力してください',
            'password.confirmed' => 'パスワードが一致しません',
        ];
    }
    
    /**
     * Login用のエラーメッセージ
     *
     * @return array
     */
    private function loginMessages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.string' => 'パスワードを文字列で入力してください',
        ];
    }
}
