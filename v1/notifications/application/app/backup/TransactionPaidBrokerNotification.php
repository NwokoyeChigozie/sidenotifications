<?php

namespace App\backup;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionPaidBrokerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payload;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = new MailMessage();

        return (new MailMessage)->view('emails.marketplace.payment_made_broker', ['user' => $notifiable, 'payload' => $this->payload])
            ->subject('The funds have been safely deposited into our trust account.');

        // switch ($this->payload->business->business_type) {

        //     case "marketplace":
        //         return (new MailMessage)->view('emails.marketplace.payment_made', ['user' => $notifiable, 'payload' => $this->payload])
        //         ->subject('The funds have been safely deposited into our trust account.');
        //     break;

        //     case "social_commerce":

        //         $payload = (object) [
        //             'payment_id'        => $this->payload->payment->payment_id,
        //             'transaction_id'    => $this->payload->transaction->transaction_id,
        //             'buyer'             => $this->payload->buyer,
        //             'seller'            => $this->payload->seller,
        //             'inspection_period' => $this->payload->transaction->inspection_period,
        //             'inspection_period_formatted' => $this->payload->transaction->inspection_period_formatted,
        //             'expected_delivery' => $this->payload->transaction->due_date,
        //             'title'             => $this->payload->transaction->title,
        //             'amount'            => $this->payload->payment->total_amount,
        //             'escrow_charge'     => $this->payload->payment->escrow_charge ?? 0,
        //             'currency'          => $this->payload->transaction->currency,
        //             'reference'         => $this->payload->payment->payment_id,
        //         ];

        //         $pdf = PDF::loadView('emails.payment.receipt', ['payload' => $payload]);

        //         return (new MailMessage)->view('emails.social_commerce.payment_made', ['user' => $notifiable, 'payload' => $this->payload, 'payloads' => $payload])
        //         ->attachData($pdf->output(), "payment_receipt.pdf")
        //         ->subject($this->payload->buyer->firstname.' just paid '.$this->payload->transaction->currency . ' ' .$this->payload->transaction->amount.' via '.$this->payload->seller->firstname.'\'s payment link.');
        //     break;

        //     default:
        //         return (new MailMessage)->view('emails.marketplace.payment_made_broker', ['user' => $notifiable, 'payload' => $this->payload])
        //         ->subject('The funds have been safely deposited into our trust account.');
        //     break;

        // }
    }

    /**
     * Get the transactionbase representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function toDatabase($notifiable)
    {
        return [
            'user' => $notifiable,
            'payload' => $this->payload,
        ];
    }

    /**
     * Get the array representation of the broadcast.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'user' => $notifiable,
            'payload' => $this->payload,
        ]);
    }
}
