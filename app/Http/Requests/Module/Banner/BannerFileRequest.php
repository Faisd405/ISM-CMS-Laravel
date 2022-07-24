<?php

namespace App\Http\Requests\Module\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class BannerFileRequest extends FormRequest
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
                $rules['file_image'] = $this->method() == 'POST' ? 'required|max:'.config('cms.files.banner.size_byte').'|mimes:'.config('cms.files.banner.mimes') : 
                    'nullable|max:'.config('cms.files.banner.size_byte').'|mimes:'.config('cms.files.banner.mimes');
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
                $rules['file_video'] = $this->method() == 'POST' ? 'required|max:'.config('cms.files.banner.size_byte').'|mimes:'.config('cms.files.banner.mimes_video') : 
                    'nullable|max:'.config('cms.files.banner.size_byte').'|mimes:'.config('cms.files.banner.mimes_video');
            }

            if ($videoType == '1') {
                $rules['file_youtube'] = 'required';
            }

            $rules['thumbnail'] = 'nullable|max:'.config('cms.files.banner.thumbnail.size_byte').'|mimes:'.config('cms.files.banner.thumbnail.mimes');
        } 
        
        if ($type == '2') {
            $rules['title_'.App::getLocale()] = 'required';
        }

        return $rules;
    }

    // public function attributes()
    // {
    //     return [
    //         'file' => __('module/banner.label.field5'),
    //         'thumbnail' => __('module/banner.label.field6'),
    //         'youtube_id' => __('module/banner.label.field7'),
    //     ];
    // }
}
