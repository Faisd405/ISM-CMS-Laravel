<?php

namespace App\Mail;

use App\Models\Feature\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginFailedMail extends Mailable
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

        return $this->from(env('MAIL_FROM_ADDRESS'), $webname)
            ->subject(__('mail.login_failed.title'))
            ->view('mail.login-failed');
    }
}
