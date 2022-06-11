<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class WidgetRequest extends FormRequest
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
            'name_'.config('cms.module.feature.language.default') => 'required|max:191',
            'template' => 'required',
            'moduleable_id' => $this->type != 'text' ? 'required' : 'nullable',

        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('cms.module.feature.language.default') => __('module/widget.label.field1'),
            'template' => __('global.template'),
            'moduleable_id' => $this->type
        ];
    }
}