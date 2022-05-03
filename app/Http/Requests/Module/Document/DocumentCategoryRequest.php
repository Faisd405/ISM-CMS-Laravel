<?php

namespace App\Http\Requests\Module\Document;

use Illuminate\Foundation\Http\FormRequest;

class DocumentCategoryRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_document_categories,slug' : 
                'required|max:191|unique:mod_document_categories,slug,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('cms.module.feature.language.default') => __('module/document.category.label.field1'),
            'slug' => __('module/document.category.label.field2'),
        ];
    }
}
