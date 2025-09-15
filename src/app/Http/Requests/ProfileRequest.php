<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        return [
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'name' => ['required', 'string', 'max:20'],
            'postcode' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Profile用のエラーメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像はjpeg、png、jpg、gif形式のみ対応しています。',
            'image.max' => '画像サイズは2MB以下にしてください。',
            'name.required' => 'ユーザー名を入力してください。',
            'name.max' => 'ユーザー名は20文字以内で入力してください。',
            'postcode.required' => '郵便番号は必須です。',
            'postcode.max' => '郵便番号は10文字以内で入力してください。',
            'address.required' => '住所は必須です。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building.max' => '建物名は255文字以内で入力してください。',
        ];
    }
}
