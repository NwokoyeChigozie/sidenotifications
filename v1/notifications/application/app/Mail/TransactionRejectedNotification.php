<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;

class TransactionRejectedNotification extends Mailable implements ShouldQueue
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
            $mail = $this->view('emails.instantescrow.transaction_rejected')
                ->subject($this->payload->seller->email_address . ' did not accept your escrow transaction.')->with($this->rdata);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            $mail = $this->view('emails.transfer.transaction_rejected')
                ->subject($this->payload->recipient->email_address . ' did not accept your funds transfer transaction.')->with($this->rdata);
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail = $this->view('emails.transactions.transaction_rejected')
                        ->subject('Your escrow transaction needs some ammendment.')->with($this->rdata);

                case "social_commerce":
                    $mail = $this->view('emails.transactions.transaction_rejected')
                        ->subject('There is an issue with the transaction you initiated.')->with($this->rdata);

                default:
                    $mail = $this->view('emails.transactions.transaction_rejected')
                        ->subject($this->payload->seller->email_address ?? 'Seller' . 'did not accept your escrow transaction.')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_rejected')
                ->subject($this->payload->seller->email_address ?? 'Seller' . 'did not accept your escrow transaction.')->with($this->rdata);
        }

        return $mail;
    }
}
