<?php

namespace App\Http\Requests\Module\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class GalleryAlbumRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_gallery_albums,slug' : 
                'required|max:191|unique:mod_gallery_albums,slug,'.$this->id,
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('cms.module.feature.language.default') => __('module/gallery.album.label.name'),
            'slug' => __('module/gallery.album.label.slug'),
        ];
    }
}
