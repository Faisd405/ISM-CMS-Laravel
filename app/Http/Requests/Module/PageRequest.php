<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class PageRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:indexing_urls,slug' : 
                'required|max:191|unique:indexing_urls,slug,'.$this->index_url_id,
        ];
    }

    public function attributes()
    {
        return [
            'title_'.config('cms.module.feature.language.default') => __('module/page.label.field1'),
            'slug' => __('module/page.label.field2'),
        ];
    }
}
