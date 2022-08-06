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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $webname = config('cmsConfig.website_name');
        $email = config('cmsConfig.system_email');
        
        $from = env('MAIL_FROM_ADDRESS');
        return $this->from($from, $webname)
            ->subject(__('mail.inquiry_sender.title', [
                'attribute' => $webname
            ]))->view('mail.inquiry-sender');
    }
}
