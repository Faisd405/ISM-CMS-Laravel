<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ActivateRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email,active,0'
        ];
    }

    public function attributes()
    {
        return [
            'email' => __('auth.activate.label.field1')
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => __('auth.activate.alert.exists'),
        ];
    }
}
