<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
            'code' => 'required|unique:coupons',
            'start' => 'required|before:end|after_or_equal:' . now(),
            'end' => 'required|after:start',
            'condition' => ['nullable', Rule::in([Coupon::MORE, Coupon::LESS])],
            'condition_value' => 'required_with:condition|nullable|numeric',
            'type' => ['required', Rule::in([Coupon::FIXED, Coupon::VARIABLE])],
            'value' => 'required',
            'message' => 'nullable',
            'times' => 'required',
        ];
    }

    protected function updateRequest()
    {
        return [
            'code' => 'required|unique:coupons,code,' . request()->route('coupon')->id,
            'start' => 'required|before:end',
            'end' => 'required|after:start',
            'condition' => ['nullable', Rule::in([Coupon::MORE, Coupon::LESS])],
            'condition_value' => 'required_with:condition|nullable|numeric',
            'type' => ['required', Rule::in([Coupon::FIXED, Coupon::VARIABLE])],
            'value' => 'required',
            'message' => 'nullable',
            'times' => 'required',
        ];
    }
}
