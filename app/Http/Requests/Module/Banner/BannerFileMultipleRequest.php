<?php

namespace App\Http\Requests\Module\Banner;

use Illuminate\Foundation\Http\FormRequest;

class BannerFileMultipleRequest extends FormRequest
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
            'file' => 'required|max:'.config('cms.files.banner.size_byte').'|mimes:'.config('cms.files.banner.mimes'),
        ];
    }

    // public function attributes()
    // {
    //     return [
    //         'file' => __('module/banner.label.field5'),
    //     ];
    // }
}
