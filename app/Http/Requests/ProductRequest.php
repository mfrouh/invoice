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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:products,name',
            'status' => 'required|boolean',
            'price' => 'required|numeric',
            'image' => 'required|image',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
