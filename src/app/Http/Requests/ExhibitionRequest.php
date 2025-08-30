<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'detail' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:5120'],
            'category_id' => ['required', 'integer', 'exists:product_categories,id'],
            'productstate_id' => ['required', 'integer', 'exists:product_states,id'],
            'value' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Exhibition用のエラーメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'detail.required' => '商品説明を入力してください',
            'detail.max' => '商品説明は255文字以内で入力してください',
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '商品画像は画像ファイルを選択してください',
            'image.mimes' => '商品画像はJPEGまたはPNG形式で選択してください',
            'image.max' => '商品画像は5MB以下のファイルを選択してください',
            'category_id.required' => '商品のカテゴリーを選択してください',
            'category_id.exists' => '選択されたカテゴリーが存在しません',
            'productstate_id.required' => '商品の状態を選択してください',
            'productstate_id.exists' => '選択された商品状態が存在しません',
            'value.required' => '商品価格を入力してください',
            'value.numeric' => '商品価格は数値で入力してください',
            'value.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
