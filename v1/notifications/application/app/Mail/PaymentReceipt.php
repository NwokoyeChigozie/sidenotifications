<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade as PDF;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $this->payload = $payload;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = PDF::loadView('emails.payment.receipt', ['payload' => $this->payload]);

        return $this->view('emails.payment.receipt')
            ->subject('Receipt from Vesicash Innovative Technologies ['.$this->payload->reference.'].')
            ->attachData($pdf->output(), "payment_receipt.pdf")
            ->with(['user' => $this->user, 'payload' => $this->payload]);
    }
}
