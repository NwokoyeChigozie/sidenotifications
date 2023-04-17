<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionPaidSuccessNotification extends Mailable implements ShouldQueue
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
            return $this->view('emails.transactions.transaction_paid')
                ->subject('You have made payment for your transaction.')->with($this->rdata);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return $this->view('emails.transactions.transaction_paid')
                ->subject('You have made payment for your transaction.')->with($this->rdata);
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {

                case "marketplace":
                    $mail = $this->view('emails.social_commerce.successful_payment_made')
                        ->subject('You have made payment for your transaction.')->with($this->rdata);

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
                    $mail = $this->view('emails.social_commerce.successful_payment_made')
                        ->subject('Trizact payment receipt ' . $this->payload->transaction->transaction_id . '.')->with($data)->attachData($pdf->output(), "payment_receipt.pdf", [
                            'mime' => 'application/pdf',
                        ]);;

                default:
                    $mail = $this->view('emails.transactions.transaction_paid')
                        ->subject('You have made payment for your transaction.')->with($this->rdata);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_paid')
                ->subject('You have made payment for your transaction.')->with($this->rdata);
        }

        return $mail;
    }
}
