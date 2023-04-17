<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WalletFunded extends Mailable
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
        return $this->view('emails.wallet-funded')
            ->subject('You’ve funded your Wallet successfully.')
            ->with(['user' => $this->user, 'payload' => $this->payload]);
    }
}
