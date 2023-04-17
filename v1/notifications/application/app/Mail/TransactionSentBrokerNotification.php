<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionSentBrokerNotification extends Mailable
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

        $pdf = PDF::loadView('emails.transactions.transaction_sent_broker', $data);
        $subject = 'Your Escrow Transaction has been created successfully';

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return $this->view('emails.instantescrow.transaction_sent_broker')->with($data)
                ->subject($subject)->attachData($pdf->output(), 'transaction_sent.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }
        $mail = '';

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail = $this->view('emails.marketplace.transaction_sent_broker')->with($data)
                        ->subject($subject)->attachData($pdf->output(), 'transaction_sent.pdf', [
                            'mime' => 'application/pdf',
                        ]);

                case "social_commerce":
                    $mail = $this->view('emails.transactions.transaction_sent_broker')->with($data)
                        ->subject($subject)->attachData($pdf->output(), 'transaction_sent.pdf', [
                            'mime' => 'application/pdf',
                        ]);

                default:
                    $mail = $this->view('emails.transactions.transaction_sent_broker')->with($data)
                        ->subject($subject)->attachData($pdf->output(), 'transaction_sent.pdf', [
                            'mime' => 'application/pdf',
                        ]);
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_sent_broker')->with($data)
                ->subject($subject)->attachData($pdf->output(), 'transaction_sent.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }
        return $mail;
    }
}
