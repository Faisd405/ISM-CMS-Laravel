<?php

namespace App\Http\Requests\Module\Banner;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('app.fallback_locale') => __('module/banner.label.name'),
        ];
    }
}
