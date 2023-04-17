<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class BrokerTransactionRejectedBySellerToBuyer extends Mailable implements ShouldQueue
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
        $sender = User::where('account_id', $this->payload->transaction->parties->sender->account_id)->first();

        return $this->view('emails.broker.transaction_rejected_by_seller_to_buyer')
            ->subject($seller->email_address . ' has rejected the escrow transaction by ' . $sender->email_address . '.')->with($this->rdata);
    }
}
