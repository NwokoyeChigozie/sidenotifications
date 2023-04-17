<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class TransactionAcceptedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payload, $user,  $links, $rdata;


    public function __construct($user, $payload)
    {
        $this->payload = $payload;
        $siteUrl = env('SITE_URL');
        $this->links = (object) [
            'faq' => $siteUrl . '/faq',
            "dashboard" => $siteUrl . '/login'
        ];
        $this->user = $user;
        $this->rdata = ['user' => $this->user, 'payload' => $this->payload, 'links' => $this->links];
    }

    public function build()
    {

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {

            $seller = User::where('account_id', $this->payload->transaction->parties->seller->account_id)->first();

            return $this->view('emails.instantescrow.transaction_accepted')
                ->subject('Your escrow transaction was accepted by ' . $seller->email_address . '.')->with($this->rdata);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {

            $recipient = User::where('account_id', $this->payload->transaction->parties->recipient->account_id)->first();

            return $this->view('emails.transfer.transaction_accepted')
                ->subject('Your funds transfer request was just accepted by ' . $recipient->email_address . '.')->with($this->rdata);
        }

        if ($this->payload->transaction->parties->sender->account_id == $this->user->account_id) {

            return $this->view('emails.transactions.transaction_accepted_sender')
                ->subject('The other party has accepted the transaction you created.')->with($this->rdata);
        }

        return $this->view('emails.transactions.transaction_accepted')
            ->subject('The other party just accepted the transaction you sent.')->with($this->rdata);
    }
}
