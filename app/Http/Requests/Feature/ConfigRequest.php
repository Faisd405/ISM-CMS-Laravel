<?php

namespace App\Http\Requests\Feature;

use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
            'group' => 'required',
            'name' => 'required|unique:feature_configurations,name',
            'label' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'group' => 'Group',
            'name' => 'Name',
            'label' => 'Label'
        ];
    }
}
