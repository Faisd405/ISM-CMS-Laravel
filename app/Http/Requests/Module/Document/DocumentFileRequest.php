<?php

namespace App\Http\Requests\Module\Document;

use Illuminate\Foundation\Http\FormRequest;

class DocumentFileRequest extends FormRequest
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

        $rules['type'] =  $this->method() == 'POST' ? 'required' : 'nullable';

        if ($type == '0') {
            $rules['file_document'] = $this->method() == 'POST' ? 'required|max:'.config('cms.files.document.size_byte').'|mimes:'.config('cms.files.document.mimes') : 
                'nullable|max:'.config('cms.files.document.size_byte').'|mimes:'.config('cms.files.document.mimes');
        }

        if ($type == '1') {
            $rules['filemanager'] = 'required';
        }

        if ($type == '2') {
            $rules['file_url'] = 'required';
        }

        return $rules;
    }
}
