<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemindToVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $this->payload = $payload;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $mail = '';
        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail = $this->view('emails.marketplace.email-verify-reminder')
                        ->subject('You are a few steps away from enjoying this platform')
                        ->with(['user' => $this->user, 'payload' => $this->payload]);
                    break;

                case "social_commerce":
                    $mail = $this->view('emails.marketplace.email-verify-reminder')
                        ->subject('Verify Your E-mail Address')
                        ->with(['user' => $this->user, 'payload' => $this->payload]);
                    break;

                default:
                    $mail = $this->view('emails.verify-email-reminder')
                        ->subject('Verify Your E-mail Address')
                        ->with(['user' => $this->user, 'payload' => $this->payload]);
                    break;
            }
        } else {
            $mail = $this->view('emails.verify-email-reminder')->subject('Verify Your E-mail Address')->with(['user' => $this->user, 'payload' => $this->payload]);
        }


        return $mail;
    }
}
