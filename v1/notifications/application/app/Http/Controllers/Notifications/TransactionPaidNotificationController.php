<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionPaidNotification;
use App\Mail\TransactionPaidBrokerNotification;
use App\Mail\TransactionPaidSuccessNotification;
use Illuminate\Support\Facades\Mail;

class TransactionPaidNotificationController extends Controller
{
    use SmsNotificationTrait;

    /**
     *
     * Send Notification when transaction has been paid for
     */

    /**
     * @OA\Post(
     *     path="/email/send/transaction_paid",
     *     summary="Send Notification when a transaction has been paid for",
     *     operationId="Send Notification when a transaction has been paid for",
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
    public function sendTransactionPaidNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            // Check If Customer Notification is true
            $custom_notification = $request->custom_notification;

            if (isset($custom_notification) && $custom_notification === true) {
                return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
            }

            // Check if this transaction exists!

            if (!$this->transactionExists($request->transaction_id)) {
                return $this->response('ok', 'This transaction does not exist.', null, 404);
            }

            $note = 'Payment For Transaction (' . $request->transaction_id . ') has been received successfully';

            $this->processNotificationData($request, $note);

            // Notification slug
            $slug = 'transaction-paid';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $this->data->business)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($this->data->business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            // Send Notification To Broker 
            if ($this->data->transaction->type == 'broker') {
                if ($this->data->broker != null) :

                    Mail::to($this->data->broker->email_address)->send(new TransactionPaidBrokerNotification($this->data->broker, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->broker->meta)->fcmToken)) :
                        $token = json_decode($this->data->broker->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Payment', 'Transaction (' . $request->transaction_id . ') has been paid for.', 'transaction');

                        $this->inAppNotification($this->data->broker->account_id, 'Transaction (' . $request->transaction_id . ') has been paid for.', [
                            'transaction_id' => $request->transaction_id
                        ]);
                    endif;

                endif;
            }

            // Transaction Paid Notification for the seller
            if ($this->data->seller != null) :
                Mail::to($this->data->seller->email_address)->send(new TransactionPaidNotification($this->data->seller, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                    $token = json_decode($this->data->seller->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Payment', 'Transaction (' . $request->transaction_id . ') has been paid for.', 'transaction');

                    $this->inAppNotification($this->data->seller->account_id, 'Transaction (' . $request->transaction_id . ') has been paid for.', [
                        'transaction_id' => $request->transaction_id
                    ]);
                endif;

            else :
                return $this->response('error', 'Transaction Seller Party Does Not Exist', null, 400);
            endif;

            // Successful Payment of transaction notification for the buyer
            if ($this->data->buyer != null) :

                Mail::to($this->data->buyer->email_address)->send(new TransactionPaidSuccessNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                    $token = json_decode($this->data->sender->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Payment', 'Payment For Transaction (' . $request->transaction_id . ') has been received successfully.', 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('error', 'Transaction Buyer Party Does Not Exist', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->seller->phone_number != null) {
                $this->sendSms($this->data->seller, $this->data, 'transaction-paid');
            }

            return $this->response('ok', 'Transaction Paid Notification Has Been Sent.', null, 200);
        }
    }
}
