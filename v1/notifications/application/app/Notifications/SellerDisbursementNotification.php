<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SellerDisbursementNotification extends Notification implements ShouldQueue
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

        if(isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {
            return (new MailMessage)
            ->subject('Disbursement Complete')
            ->view('emails.transfer.escrow_disbursed_seller', [
                'user' => $notifiable,
                'payload' => $this->payload
            ]);
        }

        return (new MailMessage)
            ->subject('Your client has approved disbursement of funds.')
            ->view('emails.transactions.escrow_disbursed_seller', [
                'user' => $notifiable,
                'payload' => $this->payload
            ]);
    }

    /**
     * Get the transactionbase representation of the notification.
     *58
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
