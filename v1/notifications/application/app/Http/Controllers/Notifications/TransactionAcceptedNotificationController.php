<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\TransactionAcceptedNotification;
use App\Mail\BrokerTransactionAcceptedByBuyerBuyerNotification;
use App\Mail\BrokerTransactionAcceptedByBuyerSellerNotification;
use App\Mail\BrokerTransactionAcceptedByBuyerBrokerNotification;
use App\Mail\BrokerTransactionAcceptedBySellerBuyerNotification;
use App\Mail\BrokerTransactionAcceptedBySellerSellerNotification;
use App\Mail\BrokerTransactionAcceptedBySellerBrokerNotification;
use Illuminate\Support\Facades\Mail;


class TransactionAcceptedNotificationController extends Controller
{

    use SmsNotificationTrait;
    protected $data;

    /**
     *
     * Send Notification when transaction has been accepted
     */

    /**
     * @OA\Post(
     *     path="/email/send/transaction_accepted",
     *     summary="Send Notification when a transaction has been accepted",
     *     operationId="Send Notification when a transaction has been accepted",
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
    public function sendTransactionAcceptedNotification(Request $request)
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

        try {

            // Check if this transaction exists!

            if (!$this->transactionExists($request->transaction_id)) {
                return $this->response('ok', 'This transaction does not exist.', null, 404);
            }

            $note = 'Transaction (' . $request->transaction_id . ') has been accepted.';
            $this->processNotificationData($request, $note);

            if ($this->data == null) {
                return $this->response('error', 'No Transaction Found', ['transaction_id' => $request->transaction_id], 400);
            }

            // Notification slug
            $slug = 'transaction-accepted';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $this->data->business)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($this->data->business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            if ($this->data->transaction->type == 'broker') {

                $buyerHasAccepted = $this->data->transaction->broker->is_buyer_accepted;
                $sellerHasAccepted = $this->data->transaction->broker->is_seller_accepted;

                // Check if the buyer has accepted, send notification to the broker and seller
                if ($this->data->buyer != null && $buyerHasAccepted && !$sellerHasAccepted) {
                    // send to the seller and broker
                    // $this->data->buyer->notify(new BrokerTransactionAcceptedByBuyerBuyerNotification($this->data));

                    Mail::to($this->data->seller->email_address)->send(new BrokerTransactionAcceptedByBuyerSellerNotification($this->data->seller, $this->data));

                    Mail::to($this->data->sender->email_address)->send(new BrokerTransactionAcceptedByBuyerBrokerNotification($this->data->sender, $this->data));

                    // Firebase Notification
                    if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                        $token = json_decode($this->data->buyer->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Accepted', $note, 'transaction');
                    endif;

                    if (isset(json_decode($this->data->seller->meta)->fcmToken)) :
                        $token = json_decode($this->data->seller->meta)->fcmToken;
                        $this->fireBaseNotfication($token, 'Transaction Accepted', $note, 'transaction');
                    endif;

                    $this->inAppNotification($this->data->buyer->account_id, $note, [
                        'transaction_id' => $request->transaction_id
                    ]);

                    $this->inAppNotification($this->data->seller->account_id, $note, [
                        'transaction_id' => $request->transaction_id
                    ]);

                    // Send Sms Notification
                    if ($this->data->buyer->phone_number !== null) {
                        $this->sendSms($this->data->buyer, $this->data, 'transaction-accepted');
                    }
                    // seller
                    if ($this->data->seller->phone_number !== null) {
                        $this->sendSms($this->data->seller, $this->data, 'transaction-accepted');
                    }
                }

                // Check if the seller has accepted, send notification to the broker and buyer
                if ($this->data->seller != null && $buyerHasAccepted && $sellerHasAccepted) {

                    // $this->data->seller->notify(new BrokerTransactionAcceptedBySellerSellerNotification($this->data));
                    Mail::to($this->data->buyer->email_address)->send(new BrokerTransactionAcceptedBySellerBuyerNotification($this->data->buyer, $this->data));

                    Mail::to($this->data->sender->email_address)->send(new BrokerTransactionAcceptedBySellerBrokerNotification($this->data->sender, $this->data));
                }

                return $this->response('ok', 'Transaction Accepted Notification has been sent', ['broker' => true], 200);
            }

            if ($this->data->recipient != null) {
                // Transaction Accepted Notification for the recipient
                Mail::to($this->data->recipient->email_address)->send(new TransactionAcceptedNotification($this->data->recipient, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                    $token = json_decode($this->data->sender->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Accepted', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

                // Send Sms Notification
                if ($this->data->seller->phone_number !== null) {
                    $this->sendSms($this->data->sender, $this->data, 'transaction-accepted');
                }
            } else {
                return $this->response('error', 'Transaction Recipient Party Does Not Exist.', null, 400);
            }

            if ($this->data->sender != null) {
                // Transaction Accepted Notification for the recipient
                Mail::to($this->data->sender->email_address)->send(new TransactionAcceptedNotification($this->data->sender, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->sender->meta)->fcmToken)) :
                    $token = json_decode($this->data->sender->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Accepted', $note, 'transaction');
                endif;

                $this->inAppNotification($this->data->seller->account_id, $note, [
                    'transaction_id' => $request->transaction_id
                ]);

                // Send Sms Notification
                if ($this->data->seller->phone_number !== null) {
                    $this->sendSms($this->data->sender, $this->data, 'transaction-accepted');
                }
            } else {
                return $this->response('error', 'Transaction Sender Party Does Not Exist.', null, 400);
            }


            return $this->response('ok', 'Transaction Accepted Notification Has Been Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage() . ' on line: ' . $e->getLine(), 400);
        }
    }
}
