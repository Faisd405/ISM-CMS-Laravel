<?php

namespace App\Mail;

use App\Models\Feature\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $webname = config('cmsConfig.general.website_name');
        $email = config('cmsConfig.dev.system_email');
        
        $from = isset($this->data['request']['email']) ? $this->data['request']['email'] : $email;
        $name = isset($this->data['request']['name']) ? $this->data['request']['name'] : __('global.visitor');
        return $this->from($from, $webname)
            ->subject(__('mail.inquiry.title', [
                'attribute' => $name
            ]))->view('mail.inquiry-form');
    }
}
