<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValueRequest extends FormRequest
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
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value' => 'required|unique:values,value,NULL,id,attribute_id,' . request('attribute_id'),
        ];
    }

    protected function updateRequest()
    {
        return [
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value' => 'required|unique:values,value,' . request()->route('value')->id . ',id,attribute_id,' . request('attribute_id'),
        ];
    }
}
