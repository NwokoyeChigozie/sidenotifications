<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\SellerDisbursementNotification;
use Illuminate\Support\Facades\Mail;



class SellerDisbursementNotificationController extends Controller
{
    use SmsNotificationTrait;

    /**
     * @OA\Post(
     *     path="/email/send/escrow_disbursed_seller",
     *     summary="Send Disbursement Notification to the seller",
     *     operationId="Send Disbursement Notification to the seller",
     *     tags={"Disbursement"},
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

        // Notification slug
        $slug = 'disbursement-sent';

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $note = 'Transaction (' . $request->transaction_id . ') funds has been disbursed.';

        $this->processNotificationData($request, $note);


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

            $business = BusinessProfile::where('account_id', $this->data->transaction->business_id)->first();
            if ($business) {

                $allowed = json_decode($business->business_given_notifications);

                if (is_array($allowed) && in_array($slug, $allowed)) {
                    $business_user = User::where('account_id', $business->account_id)->first();

                    Mail::to($business_user->email_address)->send(new SellerDisbursementNotification($business_user, $this->data));
                }
            }

            // Escrow disbursed Notification for the seller
            if ($this->data->seller != null) :
                Mail::to($this->data->seller->email_address)->send(new SellerDisbursementNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Funds Disbursed', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('error', 'Transaction Seller Does Not Exist', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->seller->phone_number != null) {

                $this->sendSms($this->data->seller, $this->data, 'escrow-disbursed-seller');
            }
            return $this->response('ok', 'Escrow Disbursement For Seller Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
