<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttributeRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'name'       => 'required|unique:attributes,name,NULL,id,product_id,'.request('product_id'),
        ];
    }

    protected function updateRequest()
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'name'       => 'required|unique:attributes,name,'.request()->route('attribute')->id.',id,product_id,'.request('product_id'),
        ];
    }
}
