<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
        if ($this->password != '') {

            return [
                'name' => 'required|max:191',
                'email' => 'required|max:191|email|unique:users,email,'.Auth::user()->id,
                'username' => 'required|regex:/^[\w-]*$/|max:15|min:5|unique:users,username,'.Auth::user()->id,
                'old_password' => 'required|min:6',
                'password' => 'nullable|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@$#%]).*$/|confirmed|min:6|different:old_password',
                'phone' => 'nullable|numeric',
            ];

        } else {

            return [
                'name' => 'required|max:191',
                'email' => 'required|email|unique:users,email,'. Auth::user()->id,
                'username' => 'required|regex:/^[\w-]*$/|min:5|max:15|unique:users,username,'. Auth::user()->id,
                'phone' => 'nullable|numeric',
            ];
            
        }
    }

    public function attributes()
    {
        return [
            'name' => __('module/user.label.field1'),
            'email' => __('module/user.label.field2'),
            'username' => __('module/user.label.field3'),
            'phone' => __('module/user.label.field4'),
            'old_password' =>  __('module/user.label.field7'),
            'password' => __('module/user.label.field5'),
        ];
    }
}
