<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionPaidNotification extends Mailable implements ShouldQueue
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

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return $this->view('emails.transfer.transaction_paid')
                ->subject($this->payload->sender->email_address . ' just sent you a payment.')->with($this->rdata);
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {

                case "marketplace":
                    $mail = $this->view('emails.marketplace.payment_made')
                        ->subject('Your funds have been safely deposited into our trust account.')->with($this->rdata);

                case "social_commerce":

                    $payload = (object) [
                        'payment_id'        => $this->payload->payment->payment_id,
                        'transaction_id'    => $this->payload->transaction->transaction_id,
                        'buyer'             => $this->payload->buyer,
                        'seller'            => $this->payload->seller,
                        'inspection_period' => $this->payload->transaction->inspection_period,
                        'inspection_period_formatted' => $this->payload->transaction->inspection_period_formatted,
                        'expected_delivery' => $this->payload->transaction->due_date,
                        'title'             => $this->payload->transaction->title,
                        'amount'            => $this->payload->payment->total_amount,
                        'escrow_charge'     => $this->payload->payment->escrow_charge ?? 0,
                        'currency'          => $this->payload->transaction->currency,
                        'reference'         => $this->payload->payment->payment_id,
                        'transaction_type'  => $this->payload->transaction->type,
                        'transaction'       => $this->payload->transaction,
                        'broker_charge'     => $this->payload->payment->broker_charge ?? 0
                    ];

                    $pdf = PDF::loadView('emails.payment.receipt', ['payload' => $payload]);

                    $data = ['user' => $this->user, 'payload' => $payload, 'links' => $this->links];
                    $mail = $this->view('emails.social_commerce.payment_made')
                        ->subject($this->payload->buyer->firstname . ' just paid ' . $this->payload->transaction->currency . ' ' . $this->payload->transaction->amount . ' via your payment link.')->with($data)->attachData($pdf->output(), "payment_receipt.pdf", [
                            'mime' => 'application/pdf',
                        ]);;

                default:
                    $mail = $this->view('emails.marketplace.payment_made')
                        ->subject('Your funds have been safely deposited into our trust account.')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.marketplace.payment_made')
                ->subject('Your funds have been safely deposited into our trust account.')->with($this->rdata);
        }

        return $mail;
    }
}
