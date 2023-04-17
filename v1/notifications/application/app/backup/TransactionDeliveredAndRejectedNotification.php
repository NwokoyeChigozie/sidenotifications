<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionDeliveredAndRejectedNotification extends Notification implements ShouldQueue
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

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    return (new MailMessage)
                        ->view('emails.marketplace.welcome', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Your delivery was rejected by the buyer');
                    break;

                case "social_commerce":
                    return (new MailMessage)
                        ->view('emails.social_commerce.delivery_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('There is a problem with your recent delivery');
                    break;

                default:
                    return (new MailMessage)
                        ->view('emails.transactions.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Your delivery was rejected by the buyer');
                    break;
            }
        } else {
            return (new MailMessage)
                ->view('emails.transactions.transaction_rejected', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject('Your delivery was rejected by the buyer');
        }

        // return (new MailMessage)
        //     ->subject('Your service has been rejected')
        //     ->view('emails.transactions.transaction_delivered_rejected', [
        //         'user' => $notifiable,
        //         'payload' => $this->payload
        //     ]);
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
