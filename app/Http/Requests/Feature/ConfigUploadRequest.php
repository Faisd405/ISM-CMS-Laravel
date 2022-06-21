<?php

namespace App\Http\Requests\Feature;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ConfigUploadRequest extends FormRequest
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
            $this->name => 'required|max:'.config('cms.files.config.'. $this->name.'.size_byte').
                '|mimes:'.config('cms.files.config.'.
            $this->name.'.mimes'),
        ];
    }

    public function attributes()
    {
        return [
            $this->name => Str::replace('_', ' ', Str::upper($this->name)),
        ];
    }
}
