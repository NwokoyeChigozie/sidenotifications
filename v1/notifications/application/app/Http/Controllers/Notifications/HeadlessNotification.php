<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use App\Mail\HeadlessMail;
use App\Models\AccessTokens;
use Illuminate\Http\Request;
use App\Models\BusinessProfiles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Bridge\AccessToken;
use Illuminate\Support\Facades\Validator;

class HeadlessNotification extends Controller
{

    /**
     * @OA\Post(
     *     path="/email/send/custom",
     *     summary="Send Custom Notification",
     *     operationId="Send Custom Notification",
     *     tags={"Headless Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="notification_type",
     *         in="query",
     *         description="The type of notification",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="content_for_buyer[]",
     *         in="query",
     *         description="Content for a buyer",
     *         required=false,
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          ),
     *     ),
     *     @OA\Parameter(
     *         name="content_for_seller[]",
     *         in="query",
     *         description="Content for a seller",
     *         required=false,
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          ),
     *     ),
     *     @OA\Parameter(
     *         name="content_for_sender[]",
     *         in="query",
     *         description="Content for a sender",
     *         required=false,
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          ),
     *     ),
     *     @OA\Parameter(
     *         name="content_for_recipient[]",
     *         in="query",
     *         description="Content for a recipient",
     *         required=false,
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          ),
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
     *         name="content_for_business[]",
     *         in="query",
     *         description="Content for business",
     *         required=false,
     *         @OA\Schema(
     *              type="array",
     *              @OA\Items(type="string")
     *          ),
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
    public function sendNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id'            => ['nullable'],
            'payment_id'                => ['nullable'],
            'notification_type'         => ['required'],
            'content_for_sender'        => ['sometimes', 'array'],
            'content_for_recipient'     => ['sometimes', 'array'],
            'content_for_buyer'         => ['sometimes', 'array'],
            'content_for_seller'        => ['sometimes', 'array'],
            'content_for_business'      => ['sometimes', 'array']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $transaction_id             = $request->transaction_id;
            $payment_id                 = $request->payment_id;
            $notification_type          = $request->notification_type;
            $content_for_sender         = $request->content_for_sender;
            $content_for_recipient      = $request->content_for_recipient;
            $content_for_buyer          = $request->content_for_buyer;
            $content_for_seller         = $request->content_for_seller;
            $content_for_business       = $request->content_for_business;

            // If for transaction
            if (isset($transaction_id)) {
                // Get the transaction details
                $encodedTransaction = $this->callApi('/transactions/listById/' . $transaction_id, []);

                $transaction = json_decode($encodedTransaction)->data->transaction;

                if ($transaction == null) {
                    return $this->response('error', 'This transaction does not exist.', null, 404);
                }

                // Check if business is admin
                $business_id = $transaction->business_id;
                $check_if_admin = User::where('account_id', $business_id)->where('account_type', 'admin')->count();

                if($check_if_admin == 0) {

                    $business_prof = BusinessProfiles::where('account_id', $transaction->business_id)->first();

                    if(!$business_prof) {
                        return $this->response('error', 'Business Has No Business Profile.', null, 400);
                    } else {
                        // Update Units
                        $unit_update = BusinessProfiles::where('account_id', $transaction->business_id)->first();
                        $unit_update->units = ($unit_update->units - 1);
                        $unit_update->save();
                    }

                    // Units
                    $units = $business_prof->units;

                    if ($units == 0) {
                        // return $this->response('error', 'Insufficient Units.', null, 400);
                    }


                }

                // Fetch the parties based on the content passed

                // Sender
                if (isset($content_for_sender)) {

                    $sender_user = User::find($transaction->parties->sender->account_id);

                    $subject = $content_for_sender['subject'];
                    $content = $this->formatContent($content_for_sender['content'], $transaction_id);

                    $payload = (object) [
                        'subject' => $subject,
                        'content' => $content
                    ];

                    Mail::to($sender_user->email_address)->send(new HeadlessMail($sender_user, $payload));
                }

                // Recipient
                if (isset($content_for_recipient)) {

                    $recipient_user = User::find($transaction->parties->recipient->account_id);
                    $subject = $content_for_recipient['subject'];
                    $content = $this->formatContent($content_for_recipient['content'], $transaction_id);

                    $payload = (object) [
                        'subject' => $subject,
                        'content' => $content
                    ];

                    Mail::to($recipient_user->email_address)->send(new HeadlessMail($recipient_user, $payload));
                }

                // Buyer
                if (isset($content_for_buyer)) {

                    $buyer_user = User::find($transaction->parties->buyer->account_id);
                    $subject = $content_for_buyer['subject'];
                    $content = $this->formatContent($content_for_buyer['content'], $transaction_id);

                    $payload = (object) [
                        'subject' => $subject,
                        'content' => $content
                    ];

                    Mail::to($buyer_user->email_address)->send(new HeadlessMail($buyer_user, $payload));
                }

                // Seller
                if (isset($content_for_seller)) {

                    $seller_user = User::find($transaction->parties->seller->account_id);
                    $subject = $content_for_buyer['subject'];
                    $content = $this->formatContent($content_for_buyer['content'], $transaction_id);

                    $payload = (object) [
                        'subject' => $subject,
                        'content' => $content
                    ];

                    Mail::to($buyer_user->email_address)->send(new HeadlessMail($seller_user, $payload));
                }

                // Business
                if (isset($content_for_business)) {

                    $business_user = User::find($transaction->business_id);
                    $subject = $content_for_business['subject'];
                    $content = $this->formatContent($content_for_business['content'], $transaction_id);

                    $payload = (object) [
                        'subject' => $subject,
                        'content' => $content
                    ];

                    Mail::to($business_user->email_address)->send(new HeadlessMail($business_user, $payload));
                }
            }

            return $this->response('ok', 'Mail sent', null, 200);
        }
    }

    public function formatContent($content, $transaction_id = null)
    {
        $org_text = $content;
        $shortcodes_a = [];
        $shortcodes_b = [];

        if ($transaction_id <> null) {
            // Get the transaction details
            $encodedTransaction = $this->callApi('/transactions/listById/' . $transaction_id, [], null);

            $transaction = json_decode($encodedTransaction)->data->transaction;

            $shortcodes_a =  array('[transaction_id]', '[transaction_title]');
            $shortcodes_b =  array($transaction->transaction_id, $transaction->title);
        }

        $new_text = str_replace($shortcodes_a, $shortcodes_b, $org_text);

        return $new_text;
    }

    public function headless(Request $request) {
        $validator = Validator::make($request->all(), [
            'account_id'             => ['required'],
            'subject'                => ['required'],
            'content'                => ['nullable']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::where('account_id', $request->account_id)->first();
            $content = $request->content;
            $subject = $request->subject;

            $token = $request->header('v-private-key');
            $business = (new AccessTokens())->getBusiness($token);

            $payload = (object) [
                'subject' => $subject,
                'content' => $content,
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new HeadlessMail($user, $payload));

            return $this->response('ok', 'E-mail Sent', null, 200);
        }
    }
}
