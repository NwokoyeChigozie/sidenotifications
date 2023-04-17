<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeNotification extends Mailable implements ShouldQueue
{
    protected $user, $payload, $rdata, $links;
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $payload)
    {
        $siteUrl = env('SITE_URL');
        $this->payload = $payload;
        $this->user = $user;
        $this->links = (object) [
            'faq' => $siteUrl . '/faq',
            "dashboard" => $siteUrl . '/login?account-id=' . $user->account_id
        ];
        $this->rdata = ['user' => $this->user, 'payload' => $this->payload, 'links' => $this->links];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = '';

        if (!empty($this->payload->business)) {
            switch ($this->payload->business->business_type) {
                case "marketplace":
                    $mail = $this->view('emails.marketplace.welcome')
                        ->subject('Welcome on board!🎉')
                        ->with($this->rdata);
                    break;

                case "social_commerce":
                    $mail = $this->view('emails.social_commerce.welcome')
                        ->subject('Welcome on board!🎉')
                        ->with($this->rdata);
                    break;

                default:
                    $mail = $this->view('emails.welcome-email')
                        ->subject('Welcome on board!🎉')
                        ->with($this->rdata);
                    break;
            }
        } else {
            $mail = $this->view('emails.welcome-email')->subject('Welcome on board!🎉')->with($this->rdata);
        }


        return $mail;
    }
}
