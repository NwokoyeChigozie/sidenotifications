<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;

class TransactionDeliveredAndRejectedNotification extends Mailable implements ShouldQueue
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
                    $mail = $this->view('emails.marketplace.welcome')
                        ->subject('Your delivery was rejected by the buyer')->with($this->rdata);

                case "social_commerce":
                    $mail = $this->view('emails.social_commerce.delivery_rejected')
                        ->subject('There is a problem with your recent delivery')->with($this->rdata);

                default:
                    $mail = $this->view('emails.transactions.transaction_rejected')
                        ->subject('Your delivery was rejected by the buyer')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_rejected')
                ->subject('Your delivery was rejected by the buyer')->with($this->rdata);
        }

        return $mail;
    }
}
