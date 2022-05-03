<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|max:191',
            'email' => 'required|email|unique:users,email|max:191',
            'username' => 'required|regex:/^[\w-]*$/|min:5|unique:users,username|max:15',
            'password' => 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed|min:6',
            'phone' => 'nullable',
            'agree' => config('cms.module.auth.register.agree') == true ? 'required' : 'nullable',
            'g-recaptcha-response' => config('recaptcha.version') == 'v2' ? 'required' : 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('auth.register.label.field1'),
            'email' => __('auth.register.label.field2'),
            'username' => __('auth.register.label.field3'),
            'password' => __('auth.register.label.field4'),
            'phone' => __('auth.register.label.field6'),
            'g-recaptcha-response' => 'Recaptcha'
        ];
    }
}
