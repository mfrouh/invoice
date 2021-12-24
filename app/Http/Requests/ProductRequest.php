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
        if (request()->isMethod('post')) {
            return $this->createRequest();
        }
        if (request()->isMethod('put') || request()->isMethod('patch')) {
            return $this->updateRequest();
        }
    }

    protected function createRequest()
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

    protected function updateRequest()
    {
        return [
            'name' => 'required|unique:products,name,' . request()->route('product')->id,
            'status' => 'required|boolean',
            'price' => 'required|numeric',
            'image' => 'required|image',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
        ];
    }

}
