<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;

class TransactionSellerDeliveredNotification extends Mailable implements ShouldQueue
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

        $mail = "";

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {

                case "marketplace":
                    $mail = $this->view('emails.transactions.transaction_delivered_seller')
                        ->subject('Have you delivered as promised??')->with($this->rdata);

                case "social_commerce":
                    $mail = $this->view('emails.transactions.transaction_delivered_seller')
                        ->subject('Have you delivered as promised??')->with($this->rdata);

                default:
                    $mail = $this->view('emails.transactions.transaction_delivered_seller')
                        ->subject('Have you delivered as promised??')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_delivered_seller')
                ->subject('Have you delivered as promised??')->with($this->rdata);
        }

        return $mail;
    }
}
