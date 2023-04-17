<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HeadlessMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $payload;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.headless')->subject($this->payload->subject)->with(['content' => $this->payload->content]);
    }
}
