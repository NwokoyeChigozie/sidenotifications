<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionSentBrokerNotifications extends Notification implements ShouldQueue
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
        $siteUrl = env('SITE_URL');
        $data = [
            'user' => $notifiable,
            'payload' => $this->payload,
            'links' => (object) [
                'faq' => $siteUrl . '/faq',
                "dashboard" => $siteUrl . '/login'
            ]
        ];

        // $pdf = PDF::loadView('emails.transactions.transaction_sent', $data);

        // return (new MailMessage)
        //     ->subject('You just created a new transaction on your escrow account')
        //     ->attachData($pdf->output(), "transaction_sent.pdf")
        //     ->view('emails.transactions.transaction_sent', $data);
        // dd(count($this->payload->transaction->milestones));
        // dd($this->payload->transaction->milestones);

        $mail = new MailMessage();
        $pdf = PDF::loadView('emails.transactions.transaction_sent_broker', $data);
        $subject = 'Your Escrow Transaction has been created successfully';

        // If transaction source is instant escrow
        // send its own content and end the program here.
        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            return $mail->view('emails.instantescrow.transaction_sent_broker', $data)
                ->subject($subject)
                ->attachData($pdf->output(), "transaction_sent.pdf");
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail->view('emails.marketplace.transaction_sent_broker', $data)
                        ->subject($subject)
                        ->attachData($pdf->output(), "transaction_sent.pdf");
                    break;

                case "social_commerce":
                    $mail->view('emails.transactions.transaction_sent_broker', $data)
                        ->subject($subject)
                        ->attachData($pdf->output(), "transaction_sent.pdf");
                    break;

                default:
                    $mail->view('emails.transactions.transaction_sent_broker', $data)
                        ->subject($subject)
                        ->attachData($pdf->output(), "transaction_sent.pdf");
                    break;
            }
        } else {
            $mail->view('emails.transactions.transaction_sent_broker', $data)
                ->subject($subject)
                ->attachData($pdf->output(), "transaction_sent.pdf");
        }



        return $mail;
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
