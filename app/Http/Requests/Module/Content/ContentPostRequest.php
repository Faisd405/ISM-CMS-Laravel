<?php

namespace App\Http\Requests\Module\Content;

use Illuminate\Foundation\Http\FormRequest;

class ContentPostRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_content_posts,slug' : 
                'required|max:191|unique:mod_content_posts,slug,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'title_'.config('cms.module.feature.language.default') => __('module/content.post.label.field1'),
            'slug' => __('module/content.post.label.field2'),
        ];
    }
}
