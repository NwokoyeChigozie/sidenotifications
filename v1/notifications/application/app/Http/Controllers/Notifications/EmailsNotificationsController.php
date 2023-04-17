<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use App\Mail\IdVerified;
use App\Mail\VerifyEmail;
use App\Mail\WalletFunded;
use App\Mail\WithdrawalSuccessful;
use App\Mail\BankAccountAdded;
use App\Mail\EmailVerified;
use App\Mail\IdNotVerified;
use App\Models\AccessTokens;
use Illuminate\Http\Request;
use App\Mail\EmailNotification;
use App\Mail\EmailVerification;
use App\Models\BusinessProfile;
use App\Mail\DisbursementFailed;
use App\Mail\PasswordResetEmail;
use App\Mail\WelcomeNotification;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetEmailDone;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomePasswordNotification;
use Illuminate\Support\Facades\Validator;

class EmailsNotificationsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/email/send/email_verification",
     *     summary="Send Email Verification mail",
     *     operationId="Email Verification",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *      @OA\Parameter(
     *         name="code",
     *         in="query",
     *         description="verification code",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="verification token",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verification mail sent",
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
    public function sendEmailVerificationMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'code'       => ['required'],
            'token'      => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            // Check If Customer Notification is true
            $custom_notification = $request->custom_notification;

            if (isset($custom_notification) && $custom_notification === true) {
                return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
            }

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'email-verification';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($user->email_address)->send(new EmailVerification($user, $payload, $request->input('code'), $request->input('token')));

                return $this->response('ok', 'Verification E-mail sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    public function sendEmailVerificationMail2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'email_address'       => ['required'],
            'code'       => ['required'],
            'token'      => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            // Check If Customer Notification is true
            $custom_notification = $request->custom_notification;

            if (isset($custom_notification) && $custom_notification === true) {
                return $this->response('ok', 'This Notification Has Been Ignored For A Custom One.', null, 200);
            }

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'email-verification';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            $user = User::find($request->input('account_id'));
            $user->email_address = $request->email_address;

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($request->email_address)->send(new EmailVerification($user, $payload, $request->input('code'), $request->input('token')));

                return $this->response('ok', 'Verification E-mail sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/welcome",
     *     summary="Send welcome mail",
     *     operationId="SendWelcomeMail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="welcome mail sent",
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
    public function sendWelcomeMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::where('account_id', $request->input('account_id'))->first();
            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $business_profile = BusinessProfile::where('account_id', $user->business_id)->first();

            // Notification slug
            $slug = 'welcome';

            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            try {
                // dd(env('MAIL_DRIVER'), env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_USERNAME'), env('MAIL_PASSWORD'), env('MAILGUN_DOMAIN'), env('MAILGUN_SECRET'), env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                Mail::to($user->email_address)->send(new WelcomeNotification($user, $payload));
                return $this->response('ok', 'Welcome E-mail sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 500);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/welcome_password_message",
     *     summary="Send welcome password mail",
     *     operationId="sendWelcomePasswordMail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="welcome password mail sent",
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
    public function sendWelcomePasswordMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'token'      => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'welcome-password';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile,
                'token'    => $request->token
            ];

            $user = User::where('account_id', $request->input('account_id'))->first();

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {
                Mail::to($user->email_address)->send(new WelcomePasswordNotification($user, $payload));
                return $this->response('ok', 'Welcome Password E-mail Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/email_verified",
     *     summary="Send email verified mail",
     *     operationId="SendEmailVerifiedMail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="email verified mail sent",
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
    public function sendEmailVerifiedMail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'email-verified';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile
            ];

            $user = User::where('account_id', $request->input('account_id'))->first();

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {
                Mail::to($user->email_address)->send(new EmailVerified($user, $payload));
                return $this->response('ok', 'Email Verified.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/email/send/reset-password",
     *     summary="Send reset password mail",
     *     operationId="Send Reset password mail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="reset token",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="reset password mail sent",
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
    public function sendResetPasswordEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'token'      => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            $payload = (object) [
                'business' => $business_profile,
                'token'    => $request->token
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($user->email_address)->send(new PasswordResetEmail($user, $payload));

                return $this->response('ok', 'Password Reset E-mail sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/reset-password/done",
     *     summary="Send reset password completed mail",
     *     operationId="Send Reset password completed mail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="reset password mail sent",
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
    public function sendResetPasswordEmailDone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            $payload = (object) [
                'business' => $business_profile,
                'ip'       => $request->ip,
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($user->email_address)->send(new PasswordResetEmailDone($user, $payload));

                return $this->response('ok', 'Password Reset Done E-mail sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/email/disbursement/failed",
     *     summary="Send disbursement failed email",
     *     operationId="Send disbursement failed email",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="reason",
     *         in="query",
     *         description="Reason",
     *         required=false,
     *         @OA\Schema(
     *              type="integer"
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
     *         name="payment_id",
     *         in="query",
     *         description="Payment ID",
     *         required=false,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="reset password mail sent",
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
    public function sendDisbursementFailed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required'],
            'reason'     => ['nullable'],
            'transaction_id' => ['nullable'],
            'payment_id' => ['nullable']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'disbursement-failed';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $transaction = null;
            if (!empty($request->transaction_id)) :
                $transaction = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], null))->data;
            endif;

            $payment = null;
            if (!empty($request->payment_id)) :
                $payment = json_decode($this->callApi('/payment/list/' . $request->payment_id, [], null))->data;
            endif;

            $payload = (object) [
                'business'      => $business_profile,
                'transaction'   => $transaction,
                'payment'       => $payment,
                'reason'        => $request->reason ?? 'No reason was generated, kindly contact us via admin@vesicash.com'
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            Mail::to($user->email_address)->send(new DisbursementFailed($user, $payload));

            return $this->response('ok', 'Disbursement Failed E-mail sent.', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/wallet_funded",
     *     summary="Send wallet funded mail",
     *     operationId="Send wallet funded mail",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         description="Amount in the wallet",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
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
     *     @OA\Response(
     *         response=200,
     *         description="Wallet funded mail sent",
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
    public function sendWalletFunded(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'     => ['required'],
            'amount'         => ['required'],
            'transaction_id' => ['nullable', 'exists:transaction.transactions']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'wallet-funded';

            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $transaction_request = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], null));

            if (!$transaction_request) {
                return $this->response('ok', 'Transaction Not Found.', null, 404);
            }

            $transaction = $transaction_request->data->transaction;

            $payload = (object) [
                'business' => $business_profile,
                'amount'   => $request->amount,
                'currency'  => $transaction->currency ?? '',
                'transaction' => $transaction
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            Mail::to($user->email_address)->send(new WalletFunded($user, $payload));

            return $this->response('ok', 'Wallet Funded E-mail Sent.', null, 200);
        }
    }

    public function sendWithdrawalSuccessful(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'     => ['required'],
            'amount'         => ['required'],
            'transaction_id' => ['nullable', 'exists:transaction.transactions']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'withdrawal-successful';

            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $transaction_request = json_decode($this->callApi('/transactions/listById/' . $request->transaction_id, [], null));

            if (!$transaction_request) {
                return $this->response('ok', 'Transaction Not Found.', null, 404);
            }

            $transaction = $transaction_request->data->transaction;

            $payload = (object) [
                'business' => $business_profile,
                'amount'   => $request->amount,
                'currency'  => $transaction->currency ?? '',
                'transaction' => $transaction
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            Mail::to($user->email_address)->send(new WithdrawalSuccessful($user, $payload));

            return $this->response('ok', 'Withdrawal Successful E-mail Sent.', null, 200);
        }
    }

    public function sendBankAccountAdded(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'     => ['required'],
            'bank'         => ['required'],
            'account_name'         => ['required'],
            'account_number'         => ['required'],
            'currency'         => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'bank-added';

            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business' => $business_profile,
                'bank'   => $request->bank,
                'account_name'   => $request->account_name,
                'account_number'   => $request->account_number,
                'currency'   => $request->currency,
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            Mail::to($user->email_address)->send(new BankAccountAdded($user, $payload));

            return $this->response('ok', 'Bank Account Added E-mail Sent.', null, 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/verification_successful",
     *     summary="Send ID Verification Successful mail",
     *     operationId="Send ID Verification Successful",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type",
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
    public function sendIDVerificationSuccessful(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'     => ['required'],
            'type'           => ['nullable']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'id-verified';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business'  => $business_profile,
                'type'      => $request->type,
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($user->email_address)->send(new IdVerified($user, $payload));

                return $this->response('ok', 'Identity Verification Successful E-mail Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/email/send/verification_failed",
     *     summary="Send ID Verification failed mail",
     *     operationId="Send ID Verification failed",
     *     tags={"Email Notifications"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Account ID of the user",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Type",
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
    public function sendIDVerificationFailed(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id'     => ['required'],
            'type'           => ['nullable']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            // Notification slug
            $slug = 'id-not-verified';
            // Make sure business ignored notifications is not null
            if ($this->ignoreNotification($slug, $business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
                exit;
            }

            if ($this->disabledNotifications($business_profile)) {
                return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
                exit;
            }

            $payload = (object) [
                'business'  => $business_profile,
                'type'      => $request->type,
                'reason'    => $request->reason
            ];

            $user = User::find($request->input('account_id'));

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            try {

                Mail::to($user->email_address)->send(new IdNotVerified($user, $payload));

                return $this->response('ok', 'Identity Verification Not Successful E-mail Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }
}
