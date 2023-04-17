<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\User;

class MilestoneTransactionDeliveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payload, $links, $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($payload)
    {
        $this->payload = $payload;
        $siteUrl = env('SITE_URL');
        $this->links = (object) [
            'faq' => $siteUrl . '/faq',
            "dashboard" => $siteUrl . '/login'
        ];
        $this->subject = 'A milestone has been marked as Done, please review.';
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
                ->subject($this->subject)
                ->view('emails.instantescrow.milestone-delivered', [
                    'user' => $notifiable,
                    'payload' => $this->payload,
                    'links' => $this->links
                ]);
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {

                case "marketplace":
                    $mail->view('emails.transactions.milestone_transaction_delivered', ['user' => $notifiable, 'payload' => $this->payload, 'links' => $this->links])
                        ->subject($this->subject);
                    break;

                case "social_commerce":
                    $mail->view('emails.social_commerce.milestone_confirm_delivery', ['user' => $notifiable, 'payload' => $this->payload, 'links' => $this->links])
                        ->subject($this->subject);
                    break;

                default:
                    $mail->view('emails.transactions.milestone_transaction_delivered', ['user' => $notifiable, 'payload' => $this->payload, 'links' => $this->links])
                        ->subject($this->subject);
                    break;
            }
        } else {
            $mail->view('emails.transactions.milestone_transaction_delivered', ['user' => $notifiable, 'payload' => $this->payload, 'links' => $this->links])
                ->subject($this->subject);
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
