<?php

namespace App\Http\Requests\Module\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventFieldRequest extends FormRequest
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

    public function rules()
    {
        return [
            'label_'.config('cms.module.feature.language.default') => 'required|max:191',
            'name' => 'required|max:191',
        ];
    }

    public function attributes()
    {
        return [
            'label_'.config('cms.module.feature.language.default') => __('module/inquiry.field.label.field1'),
            'name' => __('module/inquiry.field.label.field2'),
        ];
    }
}