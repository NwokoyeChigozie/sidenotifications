<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadBvnDetails extends Mailable
{
    use Queueable, SerializesModels;
    private $user;
    private $payload;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $payload
     */
    public function __construct($user, $payload)
    {
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.upload-bvn-details')->with([
            'user'    => $this->user,
            'payload' => $this->payload
        ]);
    }
}
