<?php

namespace App\Http\Requests\Module\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class GalleryCategoryRequest extends FormRequest
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
            'name_'.config('app.fallback_locale') => 'required|max:191',
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_gallery_categories,slug' : 
                'required|max:191|unique:mod_gallery_categories,slug,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('app.fallback_locale') => __('module/gallery.category.label.name'),
            'slug' => __('module/gallery.category.label.slug'),
        ];
    }
}
