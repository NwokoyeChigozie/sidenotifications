<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerification extends Mailable
{
    protected $user, $code, $token;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload, $code, $token)
    {
        $this->user = $user;
        $this->code = $code;
        $this->token = $token;
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.email_verification')
            ->subject("Please verify your email address")
            ->with([
                'user' => $this->user,
                'code' => $this->code,
                'token' => $this->token,
                'payload' => $this->payload
            ]);
        // ->with([
        //     'user' => auth()->user()
        // ]);
    }
}
