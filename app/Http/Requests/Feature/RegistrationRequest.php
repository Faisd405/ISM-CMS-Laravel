<?php

namespace App\Http\Requests\Feature;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'roles' => 'required',
            'type' => $this->method() == 'POST' ? 'required|unique:feature_registrations,type' : 
                'required|unique:feature_registrations,type,'.$this->id,
            'start_date' => 'nullable',
            'end_date' => $this->start_date != null ? 'required' : 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('feature/registration.label.name'),
            'roles' => __('module/user.role.caption'),
            'type' => __('global.type'),
            'start_date' => __('feature/registration.label.start_date'),
            'end_date' => __('feature/registration.label.end_date'),
        ];
    }
}
