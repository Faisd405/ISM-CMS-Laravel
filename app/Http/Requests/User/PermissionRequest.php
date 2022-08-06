<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
            'name' => $this->method() == 'POST' ? 'required|string|max:191|unique:permissions,name' : 
                'required|string|max:191|unique:permissions,name,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('module/user.permission.label.name'),
        ];
    }
}
