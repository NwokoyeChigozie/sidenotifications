<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Notification;
use App\Mail\DueDateProposalNotification;
use Illuminate\Support\Facades\Mail;


class DueDateProposalNotificationController extends Controller
{

    use SmsNotificationTrait;

    /**
     * Send Notification to buyer about the new due date proposal by the seller
     *
     */

    /**
     * @OA\Post(
     *     path="/email/send/due_date_proposal",
     *     summary="Send Notification to the buyer about the proposal of a new due date",
     *     operationId="Send Notification to the buyer about the proposal of a new due date",
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
     *     @OA\Parameter(
     *         name="note",
     *         in="query",
     *         description="Short note on why there should be a new due date",
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
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => ['required'],
            'note' => ['nullable']
        ]);

        // $validator->after(function($validator) use ($request, $token) {
        //     // This is to check if a transaction_id is valid.
        //     $tx = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], $token))->message;

        //     if (empty($tx)) {
        //         $validator->errors()->add('transaction_id', 'Transaction does not exist.');
        //     }
        // });

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        // Check If Customer Notification is true
        $custom_notification = $request->custom_notification;

        if (isset($custom_notification) && $custom_notification === true) {
            return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
        }

        $this->processNotificationData($request, $request->note);

        // Notification slug
        $slug = 'due-date-proposal';
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
            // Due Date Extension Proposal Notification for the buyer
            if ($this->data->buyer != null) :
                Mail::to($this->data->buyer->email_address)->send(new DueDateProposalNotification($this->data->buyer, $this->data));

                // Firebase Notification
                if (isset(json_decode($this->data->buyer->meta)->fcmToken)) :
                    $token = json_decode($this->data->buyer->meta)->fcmToken;
                    $this->fireBaseNotfication($token, 'Transaction Due Date Proposal', 'Transaction (' . $request->transaction_id . ') new due date proposal has been requested.', 'transaction');
                endif;

                $this->inAppNotification($this->data->buyer->account_id, 'Transaction (' . $request->transaction_id . ') new due date proposal has been requested.', [
                    'transaction_id' => $request->transaction_id
                ]);

            else :
                return $this->response('error', 'Transaction Buyer Party Does Not Exist', null, 400);
            endif;

            // Send Sms Notification
            if ($this->data->buyer->phone_number != null) {

                $this->sendSms($this->data->buyer, $this->data, 'due-date-proposal');
            }

            return $this->response('ok', 'Due date proposal notification for buyer has been sent', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
