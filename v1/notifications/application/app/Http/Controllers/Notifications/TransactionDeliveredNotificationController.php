<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionDeliveredNotification;
use App\Mail\TransactionDeliveredBuyerNotification;
use App\Mail\TransactionDeliveredBrokerNotification;
use Illuminate\Support\Facades\Mail;

class TransactionDeliveredNotificationController extends Controller
{
    protected $data = null;
    use SmsNotificationTrait;

    /**
     * Notify the buyer when the seller has delivered the product/service
     *
     */

    /**
     * @OA\Post(
     *     path="/email/send/transaction_delivered",
     *     summary="Send Notification when a transaction has been marked as delivered",
     *     operationId="Send Notification when a transaction has been marked as delivered",
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
    public function sendTransactionDeliveredNotification(Request $request)
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

        $note = 'Transaction (' . $request->transaction_id . ') has been marked as Delivered.';
        $d = $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-delivered';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }

        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }
        // dd($d);

        try {

            if ($this->data->transaction->type == 'broker') {

                if ($this->data->buyer != null) {

                    Mail::to($this->data->seller->email_address)->send(new TransactionDeliveredBuyerNotification($this->data->buyer, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                        $token = json_decode($this->data->buyer->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->buyer->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);

                    // Send Sms Notification
                    if ($this->data->buyer->phone_number != null) {

                        $this->sendSms($this->data->buyer, $this->data, 'transaction-delivered');
                    }
                }

                if ($this->data->broker != null) {

                    Mail::to($this->data->broker->email_address)->send(new TransactionDeliveredBrokerNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->broker->account_id, $note,  [
                        'transaction_id' => $request->transaction_id
                    ]);
                }

                if ($this->data->seller != null) {

                    Mail::to($this->data->seller->email_address)->send(new TransactionDeliveredNotification($this->data->seller, $this->data));

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
                Mail::to($this->data->buyer->email_address)->send(new TransactionDeliveredBuyerNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Delivered', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note,  [
                    'transaction_id' => $request->transaction_id
                ]);

                // Send Sms Notification
                if ($this->data->buyer->phone_number != null) {

                    $this->sendSms($this->data->buyer, $this->data, 'transaction-delivered');
                }

                return $this->response('ok', 'Transaction Delivered Notification Has Been Sent.', null, 200);
            } else {
                return $this->response('error', 'Transaction Delivered Notification Failed to Send, Possible Reason: Buyer Party Does Not Exist.', null, 400);
            }
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
