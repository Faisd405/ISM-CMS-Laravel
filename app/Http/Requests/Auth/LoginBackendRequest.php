<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginBackendRequest extends FormRequest
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
            'username' => 'required|string|min:5|max:15
                |exists:users,'.$this->loginType().',active,1',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => config('recaptcha.version') == 'v2' ? 'required' : 'nullable'
        ];
    }

    public function attributes()
    {
        return [
            'username' => __('auth.login_backend.label.field1'),
            'password' => __('auth.login_backend.label.field2'),
            'g-recaptcha-response' => 'Recaptcha'
        ];
    }

    public function messages()
    {
        return [
            'username.exists' => __('auth.login_backend.alert.exists'),
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
