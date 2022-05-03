<?php

namespace App\Http\Requests\Module\Document;

use Illuminate\Foundation\Http\FormRequest;

class DocumentFileMultipleRequest extends FormRequest
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
            'file' => 'required|max:'.config('cms.files.document.size_byte').'|mimes:'.config('cms.files.document.mimes'),
        ];
    }
}
