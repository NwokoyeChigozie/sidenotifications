<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionPayment extends Mailable
{
    use Queueable, SerializesModels;

    public $payload, $user;

    public function __construct($user, $payload)
    {
        $this->payload = $payload;
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.send-payment')
            ->subject('Reminder: You’re yet to make payment for this escrow transaction.')
            ->with(['user' => $this->user, 'payload' => $this->payload]);
    }
}
