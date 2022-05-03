<?php

namespace App\Http\Requests\Module\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class GalleryFileRequest extends FormRequest
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
        $type = $this->type;
        $imageType = $this->image_type;
        $videoType = $this->video_type;

        $rules['type'] =  $this->method() == 'POST' ? 'required' : 'nullable';

        if ($type == '0') {
            $rules['image_type'] = $this->method() == 'POST' ? 'required' : 'nullable';
            
            if ($imageType == '0') {
                $rules['file_image'] = $this->method() == 'POST' ? 'required|max:'.config('cms.files.gallery.size_byte').'|mimes:'.config('cms.files.gallery.mimes') : 
                    'nullable|max:'.config('cms.files.gallery.size_byte').'|mimes:'.config('cms.files.gallery.mimes');
            }

            if ($imageType == '1') {
                $rules['filemanager'] = 'required';
            }

            if ($imageType == '2') {
                $rules['file_url'] = 'required';
            }

        }

        if ($type == '1') {
            $rules['video_type'] = $this->method() == 'POST' ? 'required' : 'nullable';

            if ($videoType == '0') {
                $rules['file_video'] = $this->method() == 'POST' ? 'required|max:'.config('cms.files.gallery.size_byte').'|mimes:'.config('cms.files.gallery.mimes_video') : 
                    'nullable|max:'.config('cms.files.gallery.size_byte').'|mimes:'.config('cms.files.gallery.mimes_video');
                $rules['thumbnail'] = 'nullable|max:'.config('cms.files.gallery.thumbnail.size_byte').'|mimes:'.config('cms.files.gallery.thumbnail.mimes');
            }

            if ($videoType == '1') {
                $rules['file_youtube'] = 'required';
            }
        }

        return $rules;
    }
}
