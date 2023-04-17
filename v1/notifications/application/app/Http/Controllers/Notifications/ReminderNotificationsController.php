<?php

namespace App\Http\Controllers\Notifications;

use App\Mail\SendAcceptDelivery;
use App\Mail\SendTransactionMakeDelivery;
use App\Mail\UploadBankDetails;
use App\Mail\UploadBusinessDetails;
use App\Mail\UploadBvnDetails;
use App\Mail\UploadIdCard;
use App\Models\Transactions;
use App\User;
use App\Sms\Base;
use App\Mail\VerifyEmail;
use App\Models\AccessTokens;
use Illuminate\Http\Request;
use App\Mail\CompleteProfile;
use App\Models\BusinessProfile;
use App\Mail\SendDraftTransaction;
use App\Mail\SendAcceptTransaction;
use App\Http\Controllers\Controller;
use App\Mail\SendTransactionPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendTransactionMakeAmendment;
use App\Mail\SendTransactionConfirmDelivery;
use App\Mail\SendTransactionConfirmSatisfactoryOfDelivery;
use App\Http\Traits\SmsNotificationTrait;

class ReminderNotificationsController extends Controller
{
    use SmsNotificationTrait;
    /**
     * @OA\Post(
     *     path="/email/send/reminder/complete_profile",
     *     summary="Send Complete Profile Reminder mail",
     *     operationId="Send Complete Profile Reminder mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="fields",
     *         in="query",
     *         description="The fields to be reminded about",
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
    public function sendCompleteProfileReminderEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'fields'       => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            $payload = (object) [
                'business' => $business_profile,
                'fields'   => $request->input('fields')
            ];

            Mail::to($user->email_address)->send(new CompleteProfile($user, $payload));

            return $this->response('ok', 'Complete Profile Reminder E-mail sent.', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/reminder/verify_email",
     *     summary="Send Verify Email Reminder Mail",
     *     operationId="Send Verify Email Reminder Mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id",
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
    public function sendVerifyEmailReminderEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' =>   ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            $payload = (object) [
                'business' => $business_profile
            ];

            Mail::to($user->email_address)->send(new VerifyEmail($user, $payload));
            return $this->response('ok', 'Verify Email Reminder E-mail sent.', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/reminder/verify_phone",
     *     summary="Send Verify Phone Reminder Mail",
     *     operationId="Send Verify Phone Reminder Mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="message",
     *         in="query",
     *         description="The message to be sent in the mail",
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
    public function sendVerifyPhoneReminderSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' =>   ['required', 'exists:users'],
            'message'    =>   ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $user = User::find($request->input('account_id'));

            $sms = new Base();
            $sms->to($user->phone_number)
                ->country($request->input('country'))
                ->message($request->input('message'))
                ->send();

            $this->sendRawSms($user, $request->input('country'), $request->input('message'));
            return $this->response('ok', 'Verify Phone Number SMS Sent..', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/reminder/transaction_send_draft",
     *     summary="Send Transaction Draft Reminder Mail",
     *     operationId="Send Transaction Draft Reminder Mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id",
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
    public function sendTransactionDraftReminderEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();


            $payload = (object) [
                'business' => $business_profile,
                'created_at' => $request->created_at
            ];


            Mail::to($user->email_address)->send(new SendDraftTransaction($user, $payload));
            return $this->response('ok', 'Transaction Draft Reminder E-mail sent.', null, 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/reminder/transaction_send_payment",
     *     summary="Send Transaction Payment Reminder Mail",
     *     operationId="Send Transaction Payment Reminder Mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id",
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
    public function sendTransactionPaymentReminderEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            $payload = (object) [
                'business' => $business_profile
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;


            Mail::to($user->email_address)->send(new SendTransactionPayment($user, $payload));
            return $this->response('ok', 'Transaction Payment Reminder E-mail sent.', null, 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/reminder/transaction_send_make_amendment",
     *     summary="Send Transaction Make Amendment Reminder Mail",
     *     operationId="Send Transaction Make Amendment Reminder Mail",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id of the user",
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
    public function sendTransactionMakeAmendmentReminderEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            @Mail::to($user->email_address)->send(new SendTransactionMakeAmendment($user, $payload));
            return $this->response('ok', 'Transaction Make Amendment Reminder E-mail Sent.', null, 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/reminder/transaction_confirm_delivery",
     *     summary="Send Reminder Mail to confirm delivery",
     *     operationId="Send Reminder Mail to confirm delivery",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account id of the user",
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
    public function sendTransactionConfirmDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            @Mail::to($user->email_address)->send(new SendTransactionConfirmDelivery($user, $payload));
            return $this->response('ok', 'Transaction Confirm Delivery Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendTransactionAcceptDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();
            $transaction = Transactions::where('transaction_id', $request->transaction_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business,
                'transaction' => $transaction
            ];

            Mail::to($user->email_address)->send(new SendAcceptDelivery($user, $payload));

            return $this->response('ok', 'Upload ID Card Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendTransactionConfirmSatisfactoryOfDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));
            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;


            $accounts = (new AccessTokens())->getBusiness($token);
            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new SendTransactionConfirmSatisfactoryOfDelivery($user, $payload));
            return $this->response('ok', 'Send Transaction Confirm Satisfactory Of Delivery Reminder E-mail sent.', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/reminder/transaction_accept",
     *     summary="Send Reminder Mail to accept transaction",
     *     operationId="Send Reminder Mail to accept transaction",
     *     tags={"Reminder Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account Id of the user",
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
    public function sendTransactionAcceptTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));
            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $account = (new AccessTokens())->getBusiness($token);
            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new SendAcceptTransaction($user, $payload));
            return $this->response('ok', 'Accept Transaction Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendUploadBankDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new UploadBankDetails($user, $payload));

            return $this->response('ok', 'Upload Bank Details Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendUploadBusinessDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new UploadBusinessDetails($user, $payload));

            return $this->response('ok', 'Upload Bank Details Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendUploadBVNDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new UploadBvnDetails($user, $payload));

            return $this->response('ok', 'Upload Bank Details Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendUploadIdCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business
            ];

            Mail::to($user->email_address)->send(new UploadIdCard($user, $payload));

            return $this->response('ok', 'Upload ID Card Reminder E-mail Sent.', null, 200);
        }
    }

    public function sendTransactionDeliverTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business = BusinessProfile::where('account_id', $user->business_id)->first();
            $transaction = Transactions::where('transaction_id', $request->transaction_id)->first();

            if ($this->disabledNotifications($business)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business,
                'transaction' => $transaction
            ];

            Mail::to($user->email_address)->send(new SendTransactionMakeDelivery($user, $payload));

            return $this->response('ok', 'Upload ID Card Reminder E-mail Sent.', null, 200);
        }
    }
}
