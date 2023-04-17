<?php

namespace App\backup;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Barryvdh\DomPDF\Facade as PDF;

class TransactionReceivedBuyerNotification extends Notification implements ShouldQueue
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

        // $pdf = PDF::loadView('emails.transactions.transaction_received', $data);

        // return (new MailMessage)
        //     ->subject('You have a new escrow transaction')
        //     ->attachData($pdf->output(), "transaction_received.pdf")
        //     ->view('emails.transactions.transaction_received', $data);

        $mail = new MailMessage();


        if (isset($this->payload->transaction->source) && $this->payload->transaction->source == 'instantescrow') {
            $pdf = PDF::loadView('emails.instantescrow.transaction_received_buyer', $data);
            return $mail->view('emails.instantescrow.transaction_received_buyer', $data)
                ->subject('You just received an Escrow transaction')
                ->attachData($pdf->output(), "transaction_received.pdf");
        }

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $pdf = PDF::loadView('emails.marketplace.transaction_received_buyer', $data);
                    $mail->view('emails.marketplace.transaction_received_buyer', $data)
                        ->subject('You have a new escrow transaction')
                        ->attachData($pdf->output(), "transaction_received.pdf");
                    break;

                case "social_commerce":
                    $pdf = PDF::loadView('emails.transactions.transaction_received_buyer', $data);
                    $mail->view('emails.transactions.transaction_received_buyer', $data)
                        ->subject('You have a new escrow transaction')
                        ->attachData($pdf->output(), "transaction_received.pdf");
                    break;

                default:
                    $pdf = PDF::loadView('emails.transactions.transaction_received', $data);
                    $mail->view('emails.transactions.transaction_received_buyer', $data)
                        ->subject('You have a new escrow transaction')
                        ->attachData($pdf->output(), "transaction_received.pdf");
                    break;
            }
        } else {
            $pdf = PDF::loadView('emails.transactions.transaction_received', $data);
            $mail->view('emails.transactions.transaction_received_buyer', $data)
                ->subject('You have a new escrow transaction')
                ->attachData($pdf->output(), "transaction_received.pdf");
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
