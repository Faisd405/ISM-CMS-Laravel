<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'name' => $this->method() == 'POST' ? 'required|string|max:191|unique:roles,name' : 
                'required|string|max:191|unique:roles,name,'.$this->id,
            'level' => 'required|string|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('module/user.role.label.name'),
            'level' => __('module/user.role.label.level'),
        ];
    }
}
