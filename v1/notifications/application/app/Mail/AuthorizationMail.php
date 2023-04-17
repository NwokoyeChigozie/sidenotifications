<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthorizationMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user, $payload;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.authorization')->with([
            'user'      => $this->user,
            'payload'   => $this->payload
        ])->subject('Your account needs authorization.');
    }
}
