<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOneTimePassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user, $otp_token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $otp_token)
    {
        $this->user = $user;
        $this->otp_token = $otp_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() 
    {
        return $this->view('emails.send_otp')
            ->subject('Secure Login: Your OTP Code Is: '.$this->otp_token)
            ->with(['user' => $this->user, 'otp_token' => $this->otp_token]);
    }
}
