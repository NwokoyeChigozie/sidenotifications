<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\User;

class TransactionAcceptedSellerNotification extends Notification implements ShouldQueue
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
        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {

            $seller = User::where('account_id', $this->payload->transaction->parties->seller->account_id)->first();

            return (new MailMessage)
                ->subject('Your escrow transaction was accepted by ' . $seller->email_address . '.')
                ->view('emails.instantescrow.transaction_accepted', [
                    'user' => $notifiable,
                    'payload' => $this->payload
                ]);
        }

        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'transfer') {

            $seller = User::where('account_id', $this->payload->transaction->parties->seller->account_id)->first();

            return (new MailMessage)
                ->subject('Your funds transfer request was just accepted by ' . $seller->email_address . '.')
                ->view('emails.transfer.transaction_accepted', [
                    'user' => $notifiable,
                    'payload' => $this->payload
                ]);
        }

        return (new MailMessage)
            ->subject('Your Escrow transaction has begun.')
            ->view('emails.transactions.transaction_accepted', [
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