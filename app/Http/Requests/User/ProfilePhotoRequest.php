<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePhotoRequest extends FormRequest
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
            'avatar' => 'required|max:'.config('cms.files.avatar.size_byte').'
                |mimes:'.config('cms.files.avatar.mimes'),
        ];
    }

    public function attributes()
    {
        return [
            'avatar' => __('module/user.label.photo'),
        ];
    }
}
