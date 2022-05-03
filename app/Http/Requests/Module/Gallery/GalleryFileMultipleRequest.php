<?php

namespace App\Http\Requests\Module\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class GalleryFileMultipleRequest extends FormRequest
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
            'file' => 'required|max:'.config('cms.files.gallery.size_byte').'|mimes:'.config('cms.files.gallery.mimes'),
        ];
    }
}
