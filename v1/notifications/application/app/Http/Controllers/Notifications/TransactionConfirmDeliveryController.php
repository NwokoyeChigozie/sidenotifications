<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionConfirmDelivery;
use Illuminate\Support\Facades\Mail;

class TransactionConfirmDeliveryController extends Controller
{
    use SmsNotificationTrait;
    // protected $data;

    /**
     * @OA\Post(
     *     path="/email/send/transaction_confirm_delivery",
     *     summary="Send Notification when a delivery has been confirmed",
     *     operationId="Send Notification when a delivery has been confirmed",
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
    public function sendTransactionConfirmDeliveryNotification(Request $request)
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

        try {

            $note = 'Transaction (' . $request->transaction_id . ') needs your confirmation, have you received your product/service? .';
            $this->processNotificationData($request, $note);

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

            if ($this->data == null) {
                return $this->response('error', 'No Transaction Found', ['transaction_id' => $request->transaction_id], 400);
            }

            if ($this->data->seller != null) {
                // Delivery Confrimation Notification for the seller

                Mail::to($this->data->seller->email_address)->send(new TransactionConfirmDelivery($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Confirm Transaction Delivery', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

                // Send Sms Notification
                if ($this->data->seller->phone_number !== null) {
                    $this->sendSms($this->data->seller, $this->data, 'transaction-delivered');
                }
            } else {
                return $this->response('error', 'Transaction Seller Party Does Not Exist.', null, 200);
            }

            return $this->response('ok', 'Transaction Delivery Confirmation Notification Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage() . ' on line: ' . $e->getLine(), 400);
        }
    }
}
