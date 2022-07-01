<?php

namespace App\Mail;

use App\Models\Feature\Configuration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventFormMail extends Mailable
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
        $webname = Configuration::value('website_name');
        $email = Configuration::value('system_email');
        
        $from = isset($this->data['request']['email']) ? $this->data['request']['email'] : $email;
        $name = isset($this->data['request']['name']) ? $this->data['request']['name'] : __('global.visitor');
        return $this->from($from, $webname)
            ->subject(__('mail.event.title', [
                'attribute' => $$name
            ]))->view('mail.event-form');
    }
}
