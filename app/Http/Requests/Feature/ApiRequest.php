<?php

namespace App\Http\Requests\Feature;

use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
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
            // 'ip_address' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('feature/api.label.field1'),
            'ip_address' => __('feature/api.label.field6'),
        ];
    }
}
