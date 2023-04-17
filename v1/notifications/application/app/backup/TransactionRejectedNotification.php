<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionRejectedNotification extends Notification implements ShouldQueue
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
        // return (new MailMessage)
        //     ->subject('Your transaction has been rejected')
        //     ->view('emails.transactions.transaction_rejected', [
        //         'user' => $notifiable,
        //         'payload' => $this->payload
        //     ]);

        $mail = new MailMessage();

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return $mail->view('emails.instantescrow.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject($this->payload->seller->email_address . ' did not accept your escrow transaction.');
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return $mail->view('emails.transfer.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject($this->payload->recipient->email_address . ' did not accept your funds transfer transaction.');
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail->view('emails.transactions.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Your escrow transaction needs some ammendment.');
                    break;

                case "social_commerce":
                    $mail->view('emails.transactions.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('There is an issue with the transaction you initiated.');
                    break;

                default:
                    $mail = $this->view('emails.transactions.transaction_rejected')
                        ->subject($this->payload->seller->email_address ?? 'Seller' . 'did not accept your escrow transaction.')
                        ->with(['user' => $notifiable, 'payload' => $this->payload]);
                    break;
            }
        } else {
            $mail = $this->view('emails.transactions.transaction_rejected')
                ->subject($this->payload->seller->email_address ?? 'Seller' . 'did not accept your escrow transaction.')
                ->with(['user' => $notifiable, 'payload' => $this->payload]);
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
            'payload' => $this->payload
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
            'payload' => $this->payload
        ]);
    }
}
