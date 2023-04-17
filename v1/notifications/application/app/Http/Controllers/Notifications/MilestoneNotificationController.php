<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\MilestoneTransactionDeliveredNotification;
use App\Mail\MilestoneTransactionDeliveredBuyerNotification;
use App\Mail\MilestoneTransactionDeliveredBrokerNotification;
use App\Mail\MilestoneTransactionCompletedBuyerNotification;
use App\Mail\MilestoneTransactionCompletedBrokerNotification;
use App\Mail\MilestoneTransactionCompletedNotification;
use Illuminate\Support\Facades\Mail;



class MilestoneNotificationController extends Controller
{
    protected $data = null;
    use SmsNotificationTrait;

    public function sendMarkedAsDone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check If Customer Notification is true
        $custom_notification = $request->custom_notification;

        if (isset($custom_notification) && $custom_notification === true) {
            return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $note = 'Milestone Transaction (' . $request->transaction_id . ') has been marked as Done.';
        $d = $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-delivered';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Milestone Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Milestone Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }
        // dd($d);

        try {

            if ($this->data->transaction->type == 'broker') {

                if ($this->data->buyer != null) {

                    Mail::to($this->data->buyer->email_address)->send(new MilestoneTransactionDeliveredBuyerNotification($this->data->buyer, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                        $token = json_decode($this->data->buyer->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->buyer->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                if ($this->data->broker != null) {

                    Mail::to($this->data->broker->email_address)->send(new MilestoneTransactionDeliveredBrokerNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Milestone Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->broker->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                if ($this->data->seller != null) {

                    Mail::to($this->data->seller->email_address)->send(new MilestoneTransactionDeliveredNotification($this->data->seller, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                        $token = json_decode($this->data->seller->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Milestone Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->seller->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                return $this->response('ok', 'Milestone Transaction Delivered Notification Has Been Sent.', ['broker' => true], 200);
            }

            // Transaction delivered Notification
            if ($this->data->buyer != null) {

                Mail::to($this->data->buyer->email_address)->send(new MilestoneTransactionDeliveredBuyerNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note,  [
                    'transaction_id' => $request->transaction_id
                ]);


                return $this->response('ok', 'Milestone Transaction Delivered Notification Has Been Sent.', null, 200);
            } else {
                return $this->response('error', 'Milestone Transaction Delivered Notification Failed to Send, Possible Reason: Buyer Party Does Not Exist.', null, 400);
            }
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }


    public function sendMilestoneCompleted(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check If Customer Notification is true
        $custom_notification = $request->custom_notification;

        if (isset($custom_notification) && $custom_notification === true) {
            return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $note = 'Milestone Transaction (' . $request->transaction_id . ') has been completed.';
        $d = $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'milestone-completed';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Milestone Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Milestone Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }
        // dd($d);

        try {

            if ($this->data->transaction->type == 'broker') {

                if ($this->data->buyer != null) {

                    Mail::to($this->data->buyer->email_address)->send(new MilestoneTransactionCompletedBuyerNotification($this->data->buyer, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                        $token = json_decode($this->data->buyer->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Milestone Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->buyer->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                if ($this->data->broker != null) {

                    Mail::to($this->data->broker->email_address)->send(new MilestoneTransactionCompletedBrokerNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Milestone Transaction completed', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->broker->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                if ($this->data->seller != null) {

                    Mail::to($this->data->seller->email_address)->send(new MilestoneTransactionCompletedNotification($this->data->seller, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                        $token = json_decode($this->data->seller->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->seller->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                return $this->response('ok', 'Transaction Delivered Notification Has Been Sent.', ['broker' => true], 200);
            }

            // Transaction delivered Notification
            if ($this->data->buyer != null) {

                Mail::to($this->data->buyer->email_address)->send(new MilestoneTransactionCompletedBuyerNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note,  [
                    'transaction_id' => $request->transaction_id
                ]);


                return $this->response('ok', 'Milestone Transaction Completed Notification Has Been Sent.', null, 200);
            } else {
                return $this->response('error', 'Milestone Transaction Completed Notification Failed to Send, Possible Reason: Buyer Party Does Not Exist.', null, 400);
            }
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
