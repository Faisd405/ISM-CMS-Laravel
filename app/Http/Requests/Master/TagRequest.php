<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
                'name' => 'required|max:191|unique:master_tags,name',
            ];
        } else {
            return [
                'name' => 'required|max:191|unique:master_tags,name,'.$this->id,
            ];
        }
    }

    public function attributes()
    {
        return [
            'name' => __('master/tags.label.name'),
        ];
    }
}
