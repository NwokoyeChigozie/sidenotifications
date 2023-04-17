<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionClosedSellerNotification;
use Illuminate\Support\Facades\Mail;


class TransactionClosedSellerNotificationController extends Controller
{

    use SmsNotificationTrait;


    /**
     * @OA\Post(
     *     path="/email/send/transaction_closed_seller",
     *     summary="Send Notification to the seller when a transaction is closed",
     *     operationId="Send Notification to the seller when a transaction is closed",
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

        $note = 'Transaction (' . $request->transaction_id . ') has been closed.';
        $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-closed';
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
            // Escrow disbursed Notification for the seller
            if ($this->data->seller != null) :

                Mail::to($this->data->seller->email_address)->send(new TransactionClosedSellerNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Closed', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('ok', 'Transaction Seller Does Not Exist.', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->seller->phone_number != null) {

                $this->sendSms($this->data->seller, $this->data, 'transaction-closed-seller');
            }

            return $this->response('ok', 'Transaction Closed Notification For Seller Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
