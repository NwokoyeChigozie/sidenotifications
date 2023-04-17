<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\BuyerDisbursementNotification;
use App\Mail\SellerDisbursementNotification;
use Illuminate\Support\Facades\Mail;

class BuyerDisbursementNotificationController extends Controller
{
    use SmsNotificationTrait;

    /**
     * @OA\Post(
     *     path="/email/send/escrow_disbursed_buyer",
     *     summary="Send disbursement mail to buyer",
     *     operationId="Send disbursement mail to buyer",
     *     tags={"Disbursement"},
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
    public function sendDisbursementNotification(Request $request)
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

        $this->processNotificationData($request);

        try {
            // Escrow disbursed Notification for the buyer
            if ($this->data->buyer != null) :
                Mail::to($this->data->buyer->email_address)->send(new BuyerDisbursementNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Funds Disbursed To Seller', 'Transaction (' . $request->transaction_id . ') funds has been disbursed.', 'transaction');
                endif;

            else :
                return $this->response('error', 'Transaction Buyer Party Does Not Exist', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->buyer->phone_number != null) {

                $this->sendSms($this->data->buyer, $this->data, 'escrow-disbursed-buyer');
            }

            return $this->response('ok', 'Escrow Disbursement Notification for buyer has been sent', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/escrow_disbursed_failed",
     *     summary="Send disbursement failed mail to buyer",
     *     operationId="Send disbursement failed mail to buyer",
     *     tags={"Disbursement"},
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
     *     @OA\Parameter(
     *         name="recipient_id",
     *         in="query",
     *         description="Recipient ID",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
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
    public function sendDisbursementFailedNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required'],
            'recipient_id'   => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $this->processNotificationData($request);

        try {
            // Escrow disbursed Notification for the seller
            Mail::to($this->data->seller->email_address)->send(new SellerDisbursementNotification($this->data->seller, $this->data));

            // Firebase Notification
            if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                $token = json_decode($this->data->buyer->meta)->fcmToken;
                $this->fireBaseNotfication($token, 'Transaction Disputed', 'Transaction (' . $request->transaction_id . ') disbursement failed.', 'transaction');
            endif;

            $this->inAppNotification($this->data->buyer->account_id, 'Transaction (' . $request->transaction_id . ') disbursement failed.', [
                'transaction_id' => $request->transaction_id
            ]);

            // Send Sms Notification
            if ($this->data->buyer->phone_number != null) {

                $this->sendSms($this->data->buyer, $this->data, 'escrow-disbursed-failed');
            }

            return $this->response('ok', 'Escrow Disbursement Failed Notification has been sent', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
