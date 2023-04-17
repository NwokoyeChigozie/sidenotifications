<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionReceivedSellerNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user, $payload;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($user, $payload)
    {
        $this->payload = $payload;
        $this->user = $user;
    }

    public function build()
    {
        $siteUrl = env('SITE_URL');
        $data = [
            'user' => $this->user,
            'payload' => $this->payload,
            'links' => (object) [
                'faq' => $siteUrl . '/faq',
                "dashboard" => $siteUrl . '/login'
            ]
        ];



        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            $pdf = PDF::loadView('emails.instantescrow.transaction_received_seller', $data);
            return $this->view('emails.instantescrow.transaction_received_seller')->with($data)
                ->subject('You just received an Escrow transaction')->attachData($pdf->output(), "transaction_received.pdf", [
                    'mime' => 'application/pdf',
                ]);
        }
        $mail = '';

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $pdf = PDF::loadView('emails.marketplace.transaction_received_seller', $data);
                    $mail = $this->view('emails.marketplace.transaction_received_seller')->with($data)
                        ->subject('You have a new escrow transaction')->attachData($pdf->output(), "transaction_received.pdf", [
                            'mime' => 'application/pdf',
                        ]);

                case "social_commerce":
                    $pdf = PDF::loadView('emails.transactions.transaction_received', $data);
                    $mail = $this->view('emails.transactions.transaction_received_seller')->with($data)
                        ->subject('You have a new escrow transaction')->attachData($pdf->output(), "transaction_received.pdf", [
                            'mime' => 'application/pdf',
                        ]);

                default:
                    $pdf = PDF::loadView('emails.transactions.transaction_received_seller', $data);
                    $mail = $this->view('emails.transactions.transaction_received_seller')->with($data)
                        ->subject('You have a new escrow transaction')->attachData($pdf->output(), "transaction_received.pdf", [
                            'mime' => 'application/pdf',
                        ]);
            }
        } else {
            $pdf = PDF::loadView('emails.transactions.transaction_received_seller', $data);
            $mail = $this->view('emails.transactions.transaction_received_seller')->with($data)
                ->subject('You have a new escrow transaction')->attachData($pdf->output(), "transaction_received.pdf", [
                    'mime' => 'application/pdf',
                ]);
        }
        return $mail;
    }
}
