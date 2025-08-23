<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'comment' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Comment用のエラーメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'comment.required' => '商品コメントを入力してください',
            'comment.max' => '商品コメントは255文字以内で入力してください',
        ];
    }
}
