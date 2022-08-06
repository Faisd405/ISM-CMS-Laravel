<?php

namespace App\Http\Requests\Module\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentCategoryRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_content_categories,slug' : 
                'required|max:191|unique:mod_content_categories,slug,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('cms.module.feature.language.default') => __('module/content.category.label.name'),
            'slug' => __('module/content.category.label.slug'),
        ];
    }
}
