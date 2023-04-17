<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewTransactionSent extends Mailable
{
    use Queueable, SerializesModels;

    public 
        $sender, 
        $recipient, 
        $transaction;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $recipient, $transaction)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.transaction_sent')
            ->subject('Your escrow transaction was created and sent successfully!');
    }
}
