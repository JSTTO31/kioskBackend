<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'name' => ['required'],
            'price' => ['required', 'numeric', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'stocks' => ['required', 'numeric', 'min:1'],
            'image' => ['required',  'image', 'mimes:png']
        ];

        if(request()->method() == 'PUT' || request()->method() == 'PATCH'){
            unset($rules['image']);
            $rules['status'] = ['required'];
        }

        return $rules;
    }
}
