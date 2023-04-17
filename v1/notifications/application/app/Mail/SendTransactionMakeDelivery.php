<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransactionMakeDelivery extends Mailable
{
    use Queueable, SerializesModels;
    protected $payload, $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $this->user    = $user;
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.send-make-delivery')
            ->subject('Reminder: This escrow transaction needs your attention.')
            ->with(['user' => $this->user, 'payload' => $this->payload]);
    }
}
