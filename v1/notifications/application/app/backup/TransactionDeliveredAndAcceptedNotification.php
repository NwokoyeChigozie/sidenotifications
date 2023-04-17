<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionDeliveredAndAcceptedNotification extends Notification implements ShouldQueue
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
                ->subject('🎉Good news! Your delivery was accepted!.')
                ->view('emails.instantescrow.delivery_accepted', [
                    'user' => $notifiable,
                    'payload' => $this->payload
                ]);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {

            // return (new MailMessage)
            // ->subject('🎉Good news! Your delivery was accepted!.')
            // ->view('emails.instantescrow.delivery_accepted', [
            //     'user' => $notifiable,
            //     'payload' => $this->payload
            // ]);

            return true;
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail->view('emails.transactions.transaction_delivered_accepted', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Your delivery was accepted!');
                    break;

                case "social_commerce":
                    $buyer_n = $this->payload->buyer->firstname ?? $this->payload->buyer->email_address;
                    $mail->view('emails.social_commerce.delivery_accepted', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject("{$buyer_n} just accepted your delivery");
                    break;

                default:
                    $mail->view('emails.transactions.transaction_delivered_accepted', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Your delivery was accepted!');
                    break;
            }
        } else {
            $mail->view('emails.transactions.transaction_delivered_accepted', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject('Your delivery was accepted!');
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
