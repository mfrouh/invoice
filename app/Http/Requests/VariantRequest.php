<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
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
            'price'      => 'required',
            'quantity'   => 'required|integer',
        ];
    }

    protected function updateRequest()
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'sku'        => 'required',
            'price'      => 'required',
            'quantity'   => 'required|integer',
        ];
    }
}
