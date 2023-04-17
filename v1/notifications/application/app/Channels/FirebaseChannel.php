<?php

namespace App\Channels;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toPush($notifiable);

        // Send notification to the $notifiable instance...
    }

    public function toPush($notifiable) {
        $ctrl = new Controller();

        // Prepare Notification
        $ctrl->fireBaseNotfication($notifiable, );
    }
}
