<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class TransactionPaidBrokerNotification extends Mailable implements ShouldQueue
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

        return $this->view('emails.marketplace.payment_made_broker')
            ->subject('The funds have been safely deposited into our trust account.')->with($this->rdata);
    }
}
