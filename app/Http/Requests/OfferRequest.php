<?php

namespace App\Http\Requests;

use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id|unique:offers,product_id',
            'type'       => ['required', Rule::in([Offer::FIXED, Offer::VARIABLE])],
            'value'      => 'required|numeric',
            'message'    => 'nullable|min:3|max:100',
            'start'      => 'required|before:end|after_or_equal:'.now(),
            'end'        => 'required|after:start|after:'.now(),
        ];
    }

    protected function updateRequest()
    {
        return [
            'product_id' => 'required|integer|exists:products,id|unique:offers,product_id,'.request()->route('offer')->id,
            'type'       => ['required', Rule::in([Offer::FIXED, Offer::VARIABLE])],
            'value'      => 'required|numeric',
            'message'    => 'nullable|min:3|max:100',
            'start'      => 'required|before:end',
            'end'        => 'required|after:start',
        ];
    }
}
