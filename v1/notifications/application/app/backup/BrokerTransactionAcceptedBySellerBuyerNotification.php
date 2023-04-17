<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\User;

class BrokerTransactionAcceptedBySellerBuyerNotification extends Notification implements ShouldQueue
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
        $seller = User::where('account_id', $this->payload->transaction->parties->seller->account_id)->first();
        $sender = User::where('account_id', $this->payload->transaction->parties->sender->account_id)->first();

        // send to the seller to inform him to accept the transaction too
        return (new MailMessage)
            ->subject($seller->email_address . ' has accepted the escrow transaction by.' . $sender->email_address . '.')
            ->view('emails.broker.transaction_accepted_by_seller_to_buyer', [
                'user' => $notifiable,
                'payload' => $this->payload
            ]);
    }

    /**
     * Get the database representation of the notification.
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
