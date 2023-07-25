<?php

namespace App\Http\Requests\Module\Event;

use App\Repositories\Module\EventRepository;
use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    private $event;

    public function __construct(
        EventRepository $event
    )
    {
        $this->event = $event;
    }

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
        $getFields = $this->event->getFieldList([
            'event_id' => $this->id,
            'publish' => 1,
            'approved' => 1
        ], false);

        $fields['g-recaptcha-response'] = config('recaptcha.version') == 'v2' ? 'required' : 'nullable';
        foreach ($getFields as $value) {
            $fields[$value['name']] = !empty($value['validation']) ? implode('|', $value['validation']) : 'nullable';
        }

        return $fields;
    }

    public function attributes()
    {
        $getFields = $this->event->getFieldList([
            'event_id' => $this->id,
            'publish' => 1,
            'approved' => 1
        ], false);

        $fields['g-recaptcha-response'] = 'Recaptcha';
        foreach ($getFields as $value) {
            $fields[$value['name']] = $value->fieldLang('label');
        }

        return $fields;
    }
}
