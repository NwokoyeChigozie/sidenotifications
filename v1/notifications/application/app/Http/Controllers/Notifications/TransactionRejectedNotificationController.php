<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use App\Mail\TransactionRejectedNotification;
use App\Mail\BrokerTransactionRejectedByBuyerToSeller;
use App\Mail\BrokerTransactionRejectedByBuyerToBroker;
use App\Mail\BrokerTransactionRejectedBySellerToBuyer;
use App\Mail\BrokerTransactionRejectedBySellerToBroker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;


class TransactionRejectedNotificationController extends Controller
{

    use SmsNotificationTrait;

    /**
     *
     * Send Notification when transaction has been rejected
     */

    /**
     * @OA\Post(
     *     path="/email/send/transaction_rejected",
     *     summary="Send Notification when a transaction has been rejected",
     *     operationId="Send Notification when a transaction has been rejected",
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
    public function sendTransactionRejectedNotification(Request $request)
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

        $note = 'Transaction (' . $request->transaction_id . ') has been rejected.';

        $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'transaction-rejected';
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
            if ($this->data->transaction->type == "broker") {
                $buyerHasRejected = $this->data->transaction->broker->is_buyer_accepted === FALSE;
                $sellerHasRejected = $this->data->transaction->broker->is_seller_accepted === FALSE;

                if ($buyerHasRejected) {
                    // notify sender
                    if ($this->data->sender != null) {
                        Mail::to($this->data->sender->email_address)->send(new BrokerTransactionRejectedByBuyerToBroker($this->data->sender, $this->data));
                        // Firebase Notification
                        if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                            $token = json_decode($this->data->sender->meta)->fcmToken;
                            $this->fireBaseNotfication($token, 'Transaction Rejected', $note, 'transaction');
                        endif;

                        $this->inAppNotification($this->data->sender->account_id, $note, [
                            'transaction_id' => $request->transaction_id
                        ]);
                    }

                    // notify seller
                    if ($this->data->seller != null) {
                        Mail::to($this->data->seller->email_address)->send(new BrokerTransactionRejectedByBuyerToSeller($this->data->seller, $this->data));
                        // Firebase Notification
                        if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                            $token = json_decode($this->data->seller->meta)->fcmToken;
                            $this->fireBaseNotfication($token, 'Transaction Rejected', $note, 'transaction');
                        endif;

                        $this->inAppNotification($this->data->seller->account_id, $note, [
                            'transaction_id' => $request->transaction_id
                        ]);
                    }
                }

                if ($sellerHasRejected) {
                    // notify buyer
                    if ($this->data->buyer != null) {
                        Mail::to($this->data->buyer->email_address)->send(new BrokerTransactionRejectedBySellerToBuyer($this->data->buyer, $this->data));
                        // Firebase Notification
                        if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                            $token = json_decode($this->data->buyer->meta)->fcmToken;
                            $this->fireBaseNotfication($token, 'Transaction Rejected', $note, 'transaction');
                        endif;

                        $this->inAppNotification($this->data->buyer->account_id, $note, [
                            'transaction_id' => $request->transaction_id
                        ]);
                    }

                    // notify sender(broker)
                    if ($this->data->sender != null) {
                        Mail::to($this->data->sender->email_address)->send(new BrokerTransactionRejectedBySellerToBroker($this->data->sender, $this->data));
                        // Firebase Notification 
                        if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                            $token = json_decode($this->data->sender->meta)->fcmToken;
                            $this->fireBaseNotfication($token, 'Transaction Rejected', $note, 'transaction');
                        endif;

                        $this->inAppNotification($this->data->sender->account_id, $note, [
                            'transaction_id' => $request->transaction_id
                        ]);
                    }
                }
            } else {

                if ($this->data->sender != null) :
                    // Transaction Rejected Notification for the sender
                    Mail::to($this->data->sender->email_address)->send(new TransactionRejectedNotification($this->data->sender, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                        $token = json_decode($this->data->sender->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Rejected', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->sender->account_id, $note, [
                        'transaction_id' => $request->transaction_id
                    ]);

                else :
                    return $this->response('error', 'Transaction Sender Party Does Not Exist.', null, 400);
                endif;
            }

            // Send Sms Notification
            if ($this->data->sender->phone_number != null) {
                $this->sendSms($this->data->sender, $this->data, 'transaction-rejected');
            }

            return $this->response('ok', 'Transaction Rejected Notification Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
