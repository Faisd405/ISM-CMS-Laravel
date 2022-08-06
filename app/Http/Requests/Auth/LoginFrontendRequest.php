<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginFrontendRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
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
            'username' => 'required|regex:/^[\w-]*$/|string|min:5|max:15
                |exists:users,'.$this->loginType().',active,1',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => config('recaptcha.version') == 'v2' ? 'required' : 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'username' => __('auth.login_frontend.label.username'),
            'password' => __('auth.login_frontend.label.password'),
            'g-recaptcha-response' => 'Recaptcha'
        ];
    }

    public function messages()
    {
        return [
            'username.exists' => __('auth.login_frontend.alert.exists'),
        ];
    }

    public function forms()
    {
        return [
            $this->loginType() => $this->username,
            'password' => $this->password,
        ];
    }

    private function loginType()
    {

        $loginType = filter_var($this->username, FILTER_VALIDATE_EMAIL) ?
                    'email' : 'username';

        return $loginType;
    }
}
