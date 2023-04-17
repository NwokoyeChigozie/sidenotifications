<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;

class TransactionDeliveredNotification extends Mailable implements ShouldQueue
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
        $this->subject = 'Escrow Transaction has been marked as Done, please review.';
        $this->rdata = ['user' => $this->user, 'payload' => $this->payload, 'links' => $this->links];
    }

    public function build()
    {
        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return $this->view('emails.instantescrow.delivered')
                ->subject($this->subject)->with($this->rdata);
        }

        $mail = "";

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {

                case "marketplace":
                    $mail = $this->view('emails.transactions.transaction_delivered')
                        ->subject($this->subject)->with($this->rdata);
                case "social_commerce":
                    $mail = $this->view('emails.social_commerce.confirm_delivery')
                        ->subject($this->subject)->with($this->rdata);

                default:
                    $mail = $this->view('emails.transactions.transaction_delivered')
                        ->subject($this->subject)->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_delivered')
                ->subject($this->subject)->with($this->rdata);
        }

        return $mail;
    }
}
