<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TransactionConfirmDelivery extends Notification implements ShouldQueue
{
    use Queueable;

    public $payload;

    /**
     * Create a new notification instance.
     *
     * @return voidds
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

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail->view('emails.marketplace.confirm_delivery', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Have you delivered as promised?');
                    break;

                case "social_commerce":
                    $mail->view('emails.social_commerce.confirm_delivery', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Please confirm Delivery to ' . $this->payload->buyer->email_address);
                    break;

                default:
                    $mail->view('emails.send-confirm-delivery', ['user' => $notifiable, 'payload' => $this->payload])
                        ->subject('Please confirm Delivery of escrow transaction');
                    break;
            }
        } else {
            $mail->view('emails.send-confirm-delivery', ['user' => $notifiable, 'payload' => $this->payload])
                ->subject('Please confirm Delivery of escrow transaction');
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
