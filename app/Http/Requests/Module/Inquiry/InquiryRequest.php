<?php

namespace App\Http\Requests\Module\Inquiry;

use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
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
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:indexing_urls,slug' : 
                'required|max:191|unique:indexing_urls,slug,'.$this->index_url_id,
            // 'body_'.config('app.fallback_locale') => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('app.fallback_locale') => __('module/inquiry.label.name'),
            'slug' => __('module/inquiry.label.slug'),
            'body_'.config('app.fallback_locale') => __('module/inquiry.label.body'),
        ];
    }
}
