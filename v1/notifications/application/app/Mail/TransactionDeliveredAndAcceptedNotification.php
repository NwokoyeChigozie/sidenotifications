<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class TransactionDeliveredAndAcceptedNotification extends Mailable implements ShouldQueue
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

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return $this->view('emails.instantescrow.delivery_accepted')
                ->subject('🎉Good news! Your delivery was accepted!.')->with($this->rdata);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return true;
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail = $this->view('emails.transactions.transaction_delivered_accepted')
                        ->subject('Your delivery was accepted!')->with($this->rdata);

                case "social_commerce":
                    $buyer_n = $this->payload->buyer->firstname ?? $this->payload->buyer->email_address;
                    $mail = $this->view('emails.social_commerce.delivery_accepted')
                        ->subject("{$buyer_n} just accepted your delivery")->with($this->rdata);

                default:
                    $mail = $this->view('emails.transactions.transaction_delivered_accepted')
                        ->subject('Your delivery was accepted!')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_delivered_accepted')
                ->subject('Your delivery was accepted!')->with($this->rdata);
        }


        return $mail;
    }
}
