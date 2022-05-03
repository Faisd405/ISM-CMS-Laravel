<?php

namespace App\Http\Requests\Regional;

use Illuminate\Foundation\Http\FormRequest;

class DistrictRequest extends FormRequest
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
            'code' => $this->method() == 'POST' ? 'required|unique:regional_districts,code' : 
                'required|unique:regional_districts,code,'.$this->id,
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'code' => __('module/regional.district.label.field1'),
            'name' => __('module/regional.district.label.field2'),
            'latitude' => __('module/regional.district.label.field3'),
            'longitude' => __('module/regional.district.label.field4'),
        ];
    }
}
