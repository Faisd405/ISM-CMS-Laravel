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
        $notFromModule = (bool)$this->not_from_module;

        return [
            'title_'.config('cms.module.feature.language.default') => $notFromModule == 1 ? 'required|max:191' : 'nullable',
            'module' => $notFromModule == 0 ? 'required' : 'nullable',
            'menuable_id' => $notFromModule == 0 ? 'required' : 'nullable',
            'url' => $notFromModule == 1 ? 'required' : 'nullable',
        ];
    }

    public function attributes()
    {
        return [
            'title_'.config('cms.module.feature.language.default') => __('module/menu.label.field1'),
            'module' => __('module/menu.label.field4'),
            'menuable_id' => __('module/menu.label.field5'),
            'url' => __('module/menu.label.field3'),
        ];
    }
}
