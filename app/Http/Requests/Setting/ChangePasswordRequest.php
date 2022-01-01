<?php

namespace App\Http\Requests\Setting;

use App\Rules\CheckPassword;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password'=>['required','min:8',new CheckPassword],
            'password'=>'required|min:8|confirmed'
        ];
    }
}
