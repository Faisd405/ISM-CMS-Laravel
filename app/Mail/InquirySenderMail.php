<?php

namespace App\Mail;

use App\Models\Feature\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquirySenderMail extends Mailable
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
        
        $from = env('MAIL_FROM_ADDRESS');
        return $this->from($from, $webname)
            ->subject(__('mail.inquiry_sender.title', [
                'attribute' => $webname
            ]))->view('mail.inquiry-sender');
    }
}
