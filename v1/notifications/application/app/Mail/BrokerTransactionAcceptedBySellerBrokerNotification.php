<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class BrokerTransactionAcceptedBySellerBrokerNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payload, $user,  $links, $subject, $rdata;


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

        $seller = User::where('account_id', $this->payload->transaction->parties->seller->account_id)->first();

        return $this->view('emails.broker.transaction_accepted_by_seller_to_broker')
            ->subject('Your escrow transaction was accepted by ' . $seller->email_address . '.')->with($this->rdata);
    }
}
