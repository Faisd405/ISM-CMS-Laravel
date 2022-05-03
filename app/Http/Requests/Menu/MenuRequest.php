<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'title_'.config('cms.module.feature.language.default') => 'required|max:191',
            'url' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'title_'.config('cms.module.feature.language.default') => __('module/menu.label.field1'),
            'url' => __('module/menu.label.field3'),
        ];
    }
}
