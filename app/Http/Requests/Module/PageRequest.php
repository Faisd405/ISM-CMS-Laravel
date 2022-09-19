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
        $field['title_'.config('app.fallback_locale')] = 'required|max:191';

        if ($this->parent == 0) {
            $field['slug'] = $this->method() == 'POST' ? 'required|max:191|unique:indexing_urls,slug' : 
                'required|max:191|unique:indexing_urls,slug,'.$this->index_url_id;
        } else {
            $field['slug'] = $this->method() == 'POST' ? 'required|max:191|unique:mod_pages,slug' : 
                'required|max:191|unique:mod_pages,slug,'.$this->id;
        }

        return $field;
    }

    public function attributes()
    {
        return [
            'title_'.config('app.fallback_locale') => __('module/page.label.title'),
            'slug' => __('module/page.label.slug'),
        ];
    }
}
