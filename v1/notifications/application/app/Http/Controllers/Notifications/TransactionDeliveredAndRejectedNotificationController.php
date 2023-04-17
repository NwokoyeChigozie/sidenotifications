<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionDeliveredAndRejectedNotification;
use App\Mail\TransactionDeliveredAndRejectedBrokerNotification;
use Illuminate\Support\Facades\Mail;

class TransactionDeliveredAndRejectedNotificationController extends Controller
{
    use SmsNotificationTrait;


    /**
     * @OA\Post(
     *     path="/email/send/transaction_delivered_rejected",
     *     summary="Send Notification when a transaction has been delivered and rejected",
     *     operationId="Send Notification when a transaction has been delivered and rejected",
     *     tags={"Transaction Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="transaction_id",
     *         in="query",
     *         description="Transaction Id",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ok",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *     )
     * )
     * */
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        $note = 'Transaction (' . $request->transaction_id . ') delivery has been rejected.';

        $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-delivered-rejected';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }

        try {

            // Transaction Delivered and Rejected Notification for the seller
            if ($this->data->seller != null) :

                Mail::to($this->data->seller->email_address)->send(new TransactionDeliveredAndRejectedNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Delivery Rejected', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('error', 'Transaction Seller Party Does Not Exist.');
            endif;

            // Send Sms Notification
            if ($this->data->seller->phone_number != null) {

                $this->sendSms($this->data->seller, $this->data, 'transaction-delivered-rejected');
            }

            // Transaction Delivered and Rejected Notification for the 💔 
            if ($this->data->transaction->type == "broker") {
                if ($this->data->broker != null) :

                    Mail::to($this->data->broker->email_address)->send(new TransactionDeliveredAndRejectedBrokerNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Delivery Rejected', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->broker->account_id, $note, [
                        'transaction_id' => $request->transaction_id
                    ]);

                else :
                    return $this->response('error', 'Transaction Seller Party Does Not Exist.');
                endif;

                // Send Sms Notification
                if ($this->data->seller->phone_number != null) {

                    $this->sendSms($this->data->seller, $this->data, 'transaction-delivered-rejected');
                }
            }

            return $this->response('ok', 'Transaction Delivered but Rejected Notification Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
