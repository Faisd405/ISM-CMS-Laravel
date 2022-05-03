<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email' => $this->method() == 'POST' ? 'required|email|max:191|unique:users,email' : 
                'required|email|max:191|unique:users,email,'.$this->id,
            'username' => $this->method() == 'POST' ? 'required|regex:/^[\w-]*$/|min:5|max:15|unique:users,username' : 
                'required|min:5|max:15|regex:/^[\w-]*$/|unique:users,username,'.$this->id,
            'phone' => 'nullable|numeric',
            'roles' => 'required',
            'password' => $this->method() == 'POST' ? 'required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed|min:6' : 
                'nullable|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed|min:6',
        ];

    }

    public function attributes()
    {
        return [
            'name' => __('module/user.label.field1'),
            'email' => __('module/user.label.field2'),
            'username' => __('module/user.label.field3'),
            'roles' => __('module/user.role.caption'),
            'phone' => __('module/user.label.field4'),
            'password' => __('module/user.label.field5'),
        ];
    }
}
