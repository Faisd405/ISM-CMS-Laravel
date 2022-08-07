<?php

namespace App\Http\Requests\Module\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'name_'.config('cms.module.feature.language.default') => 'required|max:191',
            'slug' => $this->method() == 'POST' ? 'required|max:191|unique:mod_events,slug' : 
                'required|max:191|unique:mod_events,slug,'.$this->id,
            'type' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name_'.config('cms.module.feature.language.default') => __('module/event.label.name'),
            'slug' => __('module/event.label.slug'),
            'type' => __('global.type'),
        ];
    }
}
