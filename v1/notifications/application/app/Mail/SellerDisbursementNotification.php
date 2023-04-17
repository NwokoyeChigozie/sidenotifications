<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class SellerDisbursementNotification extends Mailable implements ShouldQueue
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

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return $this->view('emails.transfer.escrow_disbursed_seller')
                ->subject('Disbursement Complete')->with($this->rdata);
        }
        return $this->view('emails.transactions.escrow_disbursed_seller')
            ->subject('Your client has approved disbursement of funds.')->with($this->rdata);
    }
}
