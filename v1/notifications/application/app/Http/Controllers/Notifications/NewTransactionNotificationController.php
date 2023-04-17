<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use App\Sms\Base;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use App\Mail\TransactionSentNotification;
use App\Mail\TransactionReceivedNotification;
use App\Mail\TransactionSentBrokerNotification;
use App\Mail\TransactionReceivedBuyerNotification;
use App\Mail\TransactionReceivedSellerNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewTransactionNotificationController extends Controller
{
    use SmsNotificationTrait;

    /**
     * Send (E-Mail, Database, Sms) Notification to users after creating a new transaction
     */


    public function sendNewTransactionNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        $this->data = $this->processNotificationData($request);

        if (isset($this->data->error) and $this->data->error === TRUE) {
            return $this->response('ok', 'An error ocurred.', $this->data->errors, 400);
        }

        $note = 'You have a new transaction (' . $request->transaction_id . ')';

        // Check If Customer Notification is true
        $custom_notification = $request->custom_notification;

        if (isset($custom_notification) && $custom_notification === true) {
            return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        if (!isset($this->data->business)) {
            return $this->response('ok', 'Failed to retrieve transactions business profile.', null, 400);
        }

        // Notification slug
        $slug = 'transaction-sent';

        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }

        // Notification slug
        $slug = 'transaction-received';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        try {


            // if ($this->data->sender != null) :

            //     Mail::to($this->data->sender->email_address)->send(new TransactionSentBrokerNotification($this->data->sender, $this->data));

            //     // Firebase Notification
            //     if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
            //         $token = json_decode($this->data->sender->meta)->fcmToken;
            //         $this->fireBaseNotfication($token, 'New Vesicash Transaction', 'You just sent a new transaction (' . $request->transaction_id . ') to: ' . $this->data->recipient->firstname, 'transaction');
            //     endif;

            //     $this->inAppNotification($this->data->sender->account_id, 'You just sent a new transaction ' . $request->transaction_id . ' to: ' . $this->data->recipient->firstname, [
            //         'transaction_id' => $request->transaction_id
            //     ]);

            // endif;

            if ($this->data->buyer != null) :
                $this->data->sender = $this->data->seller;
                $this->data->recipient = $this->data->buyer;
                Mail::to($this->data->buyer->email_address)->send(new TransactionReceivedBuyerNotification($this->data->buyer, $this->data));


                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'New Vesicash Transaction', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);
            endif;

            if ($this->data->seller != null) :
                $this->data->sender = $this->data->buyer;
                $this->data->recipient = $this->data->seller;
                Mail::to($this->data->seller->email_address)->send(new TransactionReceivedSellerNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'New Vesicash Transaction', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);
            endif;

            // Send Sms Notification

            if (!empty($this->data->seller)) {
                if (!empty($this->data->seller->phone_number)) {
                    $this->sendSms($this->data->seller, $this->data, 'transaction-received');
                }
            }


            if (!empty($this->data->buyer)) {
                if (!empty($this->data->buyer->phone_number)) {
                    $this->sendSms($this->data->buyer, $this->data, 'transaction-received');
                }
            }

            // Transaction Sent Notification for the sender
            // if ($this->data->sender != null) :
            //     Mail::to($this->data->sender->email_address)->send(new TransactionSentNotification($this->data->sender, $this->data));

            //     // Firebase Notification
            //     if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
            //         $token = json_decode($this->data->sender->meta)->fcmToken;
            //         $this->fireBaseNotfication($token, 'New Vesicash Transaction', 'You just sent a new transaction (' . $request->transaction_id . ') to: ' . $this->data->recipient->firstname, 'transaction');
            //     endif;

            //     $this->inAppNotification($this->data->sender->account_id, 'You just sent a new transaction ' . $request->transaction_id . ' to: ' . $this->data->recipient->firstname, [
            //         'transaction_id' => $request->transaction_id
            //     ]);
            // endif;

            // Transaction Received Notification for the recipient
            // if ($this->data->recipient != null) :
            //     Mail::to($this->data->recipient->email_address)->send(new TransactionReceivedNotification($this->data->recipient, $this->data));

            //     // Firebase Notification
            //     if (isset(json_decode($this->data->recipient->meta)->fcmToken)) :
            //         $token = json_decode($this->data->recipient->meta)->fcmToken;
            //         $this->fireBaseNotfication($token, 'New Vesicash Transaction', $note, 'transaction');
            //     endif;

            //     $this->inAppNotification($this->data->recipient->account_id, $note, [
            //         'transaction_id' => $request->transaction_id
            //     ]);
            // endif;

            // Send Sms Notification
            // if ($this->data->sender->phone_number != null && !empty($this->data->sender->phone_number)) {
            //     $this->sendSms($this->data->seller, $this->data, 'transaction-sent');
            // }

            // if (!empty($this->data->recipient)) {
            //     if (!empty($this->data->recipient->phone_number)) {
            //         $this->sendSms($this->data->recipient, $this->data, 'transaction-received');
            //     }
            // }


            return $this->response('ok', 'New Transaction Notification has been sent', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
