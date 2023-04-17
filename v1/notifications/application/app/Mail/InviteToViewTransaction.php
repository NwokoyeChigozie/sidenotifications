<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InviteToViewTransaction extends Mailable
{
    use Queueable, SerializesModels;
    protected $transaction_id, $transaction_link, $payload, $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $siteUrl = env('SITE_URL');
        $this->payload = $payload;
        $this->user = $user;
        $this->transaction_id = $payload->transaction->transaction_id;
        $this->transaction_link = $siteUrl . '/transactions/payment-details?transaction_id=' . $this->transaction_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.transactions.invite-to-view-transaction')
            ->subject('You have been invited to View an Escrow Transaction ' . $this->transaction_id)
            ->with(['user' => $this->user, 'payload' => $this->payload, 'link' => $this->transaction_link]);
    }
}
