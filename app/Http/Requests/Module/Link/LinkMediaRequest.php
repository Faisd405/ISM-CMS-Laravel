<?php

namespace App\Http\Requests\Module\Link;

use Illuminate\Foundation\Http\FormRequest;

class LinkMediaRequest extends FormRequest
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
            'url' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'title_'.config('cms.module.feature.language.default') => __('module/link.media.label.title'),
            'url' => __('module/link.media.label.url'),
        ];
    }
}
