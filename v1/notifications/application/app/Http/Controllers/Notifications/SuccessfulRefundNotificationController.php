<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\SuccessfulRefundNotification;
use Illuminate\Support\Facades\Mail;


class SuccessfulRefundNotificationController extends Controller
{
    use SmsNotificationTrait;

    /**
     *
     * Send Notification when transaction sum has been refunded
     */

    /**
     * @OA\Post(
     *     path="/email/send/successful_refund",
     *     summary="Send Notification when transaction sum has been refunded",
     *     operationId="Send Notification when transaction sum has been refunded",
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
    public function sendTransactionRefundNotification(Request $request)
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

        $note = 'Transaction (' . $request->transaction_id . ') funds has been refunded.';
        $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-refund';
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

            if ($this->data->buyer !== null) :
                // Transaction refunded Notification for the buyer
                Mail::to($this->data->buyer->email_address)->send(new SuccessfulRefundNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Funds Refunded', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

                // Send Sms Notification
                if ($this->data->buyer->phone_number !== null) :
                    $this->sendSms($this->data->buyer, $this->data, 'successful-refund');
                endif;
            else :
                return $this->response('error', 'Transaction Buyer Does Not Exist.', null, 200);
            endif;

            return $this->response('ok', 'Transaction Payment Refund Notification Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
