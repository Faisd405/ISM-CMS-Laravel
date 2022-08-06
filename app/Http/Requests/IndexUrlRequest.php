<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexUrlRequest extends FormRequest
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
        if ($this->method() == 'POST') {
            return [
                'slug' => 'required|max:191|unique:indexing_urls,slug',
            ];
        } else {
            return [
                'slug' => 'required|max:191|unique:indexing_urls,slug,'.$this->id,
            ];
        }
    }

    public function attributes()
    {
        return [
            'slug' => __('module/url.label.slug'),
        ];
    }
}
