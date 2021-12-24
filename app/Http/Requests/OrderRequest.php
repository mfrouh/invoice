<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'customer_id' => 'required|exists:users,id|different:seller_id',
            'seller_id' => 'required|exists:users,id|different:customer_id',
            'total' => 'required|numeric',
            'invoice_qr_code' => 'required|uuid',
            'tax' => 'numeric|nullable',
            'ship' => 'numeric|nullable',
            'discount' => 'numeric|nullable',
        ];
    }
}
