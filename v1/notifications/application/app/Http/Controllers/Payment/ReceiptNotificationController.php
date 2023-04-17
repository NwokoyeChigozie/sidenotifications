<?php

namespace App\Http\Controllers\Payment;

use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\PaymentReceipt;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReceiptNotificationController extends Controller
{


    /**
     * @OA\Post(
     *     path="/email/payment/receipt",
     *     summary="Send Notification for a new payment receipt",
     *     operationId="Send Notification for a new payment receipt",
     *     tags={"Payment Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="reference",
     *         in="query",
     *         description="Payment reference",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="payment_id",
     *         in="query",
     *         description="Payment ID",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="transaction_id",
     *         in="query",
     *         description="Transaction ID",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="buyer",
     *         in="query",
     *         description="Buyers ID",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="seller",
     *         in="query",
     *         description="Sellers ID",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="inspection_period_formatted",
     *         in="query",
     *         description="Inspection period formatted",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="expected_delivery",
     *         in="query",
     *         description="Expected date of delivery",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Title of the transaction",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="Amount of the transaction",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="escrow_charge",
     *         in="query",
     *         description="Escrow charge of the transaction",
     *         required=false,
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
    public function sendReceipt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference'         => ['required'],
            'payment_id'        => ['nullable'],
            'transaction_id'    => ['nullable'],
            'buyer'             => ['nullable'],
            'seller'            => ['nullable'],
            'inspection_period_formatted' => ['nullable'],
            'expected_delivery' => ['nullable'],
            'title'             => ['nullable'],
            'amount'            => ['nullable'],
            'escrow_charge'     => ['nullable']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            // Check If Customer Notification is true
            $custom_notification = $request->custom_notification;

            if (isset($custom_notification) && $custom_notification === true) {
                return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
            }

            // Notification slug
            $slug = 'payment-receipt';

            $buyer_user = User::where('account_id', $request->buyer)->first();
            $seller_user = User::where('account_id', $request->seller)->first();
            $broker_user = '';
            $trans = NULL;

            if ($request->transaction_id) :
                if (!$this->transactionExists($request->transaction_id)) {
                    return $this->response('error', 'Transaction does not exist', null, 404);
                }

                $transaction = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], null))->data->transaction;
                $trans = $transaction;

                $business = BusinessProfile::where('account_id', $transaction->business_id)->first();

                // get broker user
                if ($transaction->type == "broker") {
                    $broker_user = User::where('account_id', $transaction->parties->broker->account_id)->first();
                }

                // Make sure business ignored notifications is not null
                if ($this->ignoreNotification($slug, $business)) {
                    return $this->response('ok', 'Payment Notification Has Been Ignored.', null, 200);
                    exit;
                }
            endif;

            if (!$buyer_user) {
                return $this->response('error', 'Buyer User Does Not Exist', null, 400);
            }

            if (!$seller_user) {
                return $this->response('error', 'Seller User Does Not Exist', null, 400);
            }

            if (!$seller_user) {
                return $this->response('error', 'Seller User Does Not Exist', null, 400);
            }

            $payload = (object) [
                'payment_id'        => $request->payment_id,
                'transaction_id'    => $request->transaction_id,
                'transaction_type'  => $request->transaction_type,
                'transaction'       => $trans,
                'buyer'             => $buyer_user,
                'seller'            => $seller_user,
                'inspection_period_formatted' => $request->inspection_period_formatted,
                'expected_delivery' => $request->expected_delivery,
                'title'             => $request->title,
                'amount'            => $request->amount,
                'escrow_charge'     => $request->escrow_charge,
                'broker_charge'     => $request->broker_charge,
                'currency'          => $request->currency,
                'reference'         => $request->reference,
                'transaction_type'  => $request->transaction_type,
            ];

            // Mail
            try {

                // Mail::to($buyer_user->email_address)->send(new PaymentReceipt($buyer_user, $payload));

                $transaction = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], null))->data->transaction;

                $business = BusinessProfile::where('account_id', $transaction->business_id)->first();
                if ($business) {

                    $allowed = json_decode($business->business_given_notifications);

                    if (is_array($allowed) && in_array($slug, $allowed)) {
                        $business_user = User::where('account_id', $business->account_id)->first();

                        Mail::to([$buyer_user->email_address, $business_user->email_address])->send(new PaymentReceipt($buyer_user, $payload));
                    } else {

                        Mail::to($buyer_user->email_address)->send(new PaymentReceipt($buyer_user, $payload));
                    }

                    // send to broker also
                    if ($broker_user) {
                        Mail::to($broker_user->email_address)->send(new PaymentReceipt($broker_user, $payload));
                    }
                }

                return $this->response('ok', 'Payment Receipt Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage() . ' On Line: ' . $e->getLine() . ' File: ' . $e->getFile(), 400);
            }
        }
    }
}
