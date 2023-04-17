<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\DisputeOpenedNotification;
use Illuminate\Support\Facades\Mail;


class DisputeOpenedNotificationController extends Controller
{

    use SmsNotificationTrait;

    /**
     * @OA\Post(
     *     path="/email/send/dispute_opened",
     *     summary="Dispute opened email notification",
     *     operationId="Dispute opened email notification",
     *     tags={"Dispute Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="transaction_id",
     *         in="query",
     *         description="Transaction ID",
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

        $this->processNotificationData($request);

        // Notification slug
        $slug = 'dispute-opened';
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

            if ($this->data->transaction->type == 'broker') {
                // broker
                if ($this->data->broker != null) :
                    Mail::to($this->data->broker->email_address)->send(new DisputeOpenedNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Disputed', 'Transaction (' . $request->transaction_id . ') has been disputed.', 'transaction');
                    endif;

                    $this->inAppNotification($this->data->broker->account_id, 'Transaction (' . $request->transaction_id . ') has been disputed',  [
                        'transaction_id' => $request->transaction_id
                    ]);

                endif;

                // buyer
                if ($this->data->buyer != null) :
                    Mail::to($this->data->buyer->email_address)->send(new DisputeOpenedNotification($this->data->buyer, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                        $token = json_decode($this->data->buyer->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Disputed', 'Transaction (' . $request->transaction_id . ') has been disputed.', 'transaction');
                    endif;

                    $this->inAppNotification($this->data->buyer->account_id, 'Transaction (' . $request->transaction_id . ') has been disputed',  [
                        'transaction_id' => $request->transaction_id
                    ]);

                endif;

                if ($this->data->seller != null) :
                    Mail::to($this->data->seller->email_address)->send(new DisputeOpenedNotification($this->data->seller, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                        $token = json_decode($this->data->seller->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Disputed', 'Transaction (' . $request->transaction_id . ') has been disputed.', 'transaction');
                    endif;

                    $this->inAppNotification($this->data->seller->account_id, 'Transaction (' . $request->transaction_id . ') has been disputed',  [
                        'transaction_id' => $request->transaction_id
                    ]);

                endif;

                return response()->json([
                    'status' => 'ok',
                    'message' => 'Dispute Opened Notification for all parties involved has been sent',
                    'data' => ['broker' => true]
                ], 200);
            }

            // Dispute Opened notification for the seller
            if ($this->data->seller != null) :
                Mail::to($this->data->seller->email_address)->send(new DisputeOpenedNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Disputed', 'Transaction (' . $request->transaction_id . ') has been disputed.', 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, 'Transaction (' . $request->transaction_id . ') has been disputed',  [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('error', 'Transaction Seller Party Does Not Exist', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->seller->phone_number != null) {

                $this->sendSms($this->data->seller, $this->data, 'dispute-opened');
            }

            return response()->json([
                'status' => 'ok',
                'message' => 'Dispute Opened Notification for seller has been sent'
            ], 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
