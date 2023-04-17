<?php

namespace App\backup;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionPaidSuccessNotification extends Notification implements ShouldQueue
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

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return (new MailMessage)
                ->subject('You have made payment for your transaction.')
                ->view('emails.transactions.transaction_paid', [
                    'user' => $notifiable,
                    'payload' => $this->payload
                ]);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return (new MailMessage)
                ->subject('You have made payment for your transaction.')
                ->view('emails.transactions.transaction_paid', [
                    'user' => $notifiable,
                    'payload' => $this->payload
                ]);
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail->view('emails.social_commerce.successful_payment_made', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('You have made payment for your transaction.');
                    break;

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

                    $mail->view('emails.social_commerce.successful_payment_made', ['user' => $notifiable, 'payload' => $this->payload])->attachData($pdf->output(), "payment_receipt.pdf")
                        ->subject('Trizact payment receipt ' . $this->payload->transaction->transaction_id . '.');

                    break;

                default:
                    $mail->view('emails.transactions.transaction_paid', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('You have made payment for your transaction.');
                    break;
            }
        } else {
            $mail->view('emails.transactions.transaction_paid', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject('You have made payment for your transaction.');
        }



        return $mail;
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
