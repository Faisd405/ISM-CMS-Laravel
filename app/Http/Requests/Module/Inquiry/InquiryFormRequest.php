<?php

namespace App\Http\Requests\Module\Inquiry;

use App\Services\Module\InquiryService;
use Illuminate\Foundation\Http\FormRequest;

class InquiryFormRequest extends FormRequest
{
    private $inquiry;

    public function __construct(
        InquiryService $inquiry
    )
    {
        $this->inquiry = $inquiry;
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
        $getFields = $this->inquiry->getFieldList(['inquiry_id' => $this->id], false);

        $fields['g-recaptcha-response'] = config('recaptcha.version') == 'v2' ? 'required' : 'nullable';
        foreach ($getFields as $value) {
            $fields[$value['name']] = $value['validation'] ?? 'nullable';
        }

        return $fields;
    }

    public function attributes()
    {
        $getFields = $this->inquiry->getFieldList(['inquiry_id' => $this->id], false);
        
        $fields['g-recaptcha-response'] = 'Recaptcha';
        foreach ($getFields as $value) {
            $fields[$value['name']] = $value->fieldLang('label');
        }

        return $fields;
    }
}
