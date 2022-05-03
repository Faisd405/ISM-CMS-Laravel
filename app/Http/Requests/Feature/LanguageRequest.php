<?php

namespace App\Http\Requests\Feature;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
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
            'iso_codes' => $this->method() == 'POST' ? 'required|max:5|unique:feature_languages,iso_codes' : 
                'required|max:5|unique:feature_languages,iso_codes,'.$this->id,
            'name' => 'required|max:191',
        ];
    }

    public function attributes()
    {
        return [
            'iso_codes' => __('feature/language.label.field1'),
            'name' => __('feature/language.label.field2'),
        ];
    }
}
