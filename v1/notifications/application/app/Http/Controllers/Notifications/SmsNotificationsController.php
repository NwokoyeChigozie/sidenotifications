<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use App\Sms\Base;
use App\UserProfile;
use App\Models\Countries;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SmsNotificationsController extends Controller
{
    use SmsNotificationTrait;

    /**
     * @OA\Post(
     *     path="/phone/send/sms_verification",
     *     summary="Send Phone verification sms",
     *     operationId="Send Phone verification sms",
     *     tags={"Sms Notifications"},
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
     *         description="The message to be sent",
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
    public function sendPhoneVerificationSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'message'    => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $user = User::find($request->input('account_id'));

            try {
                $this->sendRawSms($user, $this->countryCode($user->account_id), $request->input('message'));

                return $this->response('ok', 'SMS Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    public function sendSmsToPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'message'    => ['required'],
            'phone_number'    => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            $user = User::find($request->input('account_id'));
            Log::info(["type" => "user obj", "id" => $request->input('account_id'), "obj" => $user]);

            $user->phone_number = $request->input('phone_number');
            $country = $this->countryCode($user->account_id) ?? "NG";
            try {
                $this->sendRawSms($user, $country, $request->input('message'));

                return $this->response('ok', 'SMS Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/phone/send/sms_verified",
     *     summary="Send Phone Number Verified mail",
     *     operationId="Send Phone Number Verified mail",
     *     tags={"Sms Notifications"},
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
    public function sendPhoneNumberVerified(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->account_id);

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;

            $message = 'Hi ' . $user->email_address . ', your phone number is now verified. Login and complete your profile.';

            try {
                $this->sendRawSms($user, $this->countryCode($user->account_id), $message);

                return $this->response('ok', 'SMS Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/phone/send/reset_password",
     *     summary="Send Reset Password Sms",
     *     operationId="Send Reset Password Sms",
     *     tags={"Sms Notifications"},
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
     *         name="token",
     *         in="query",
     *         description="Token",
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
    public function sendResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users', 'numeric'],
            'token'      => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = DB::table('users')->where('account_id', $request->account_id)->first();

            if (!$user) :
                return $this->response('error', 'User does not exist', null, 400);
            endif;
            $profile = '';
            $country = '';

            if ($user->account_type == 'individual' || $user->account_type == 'moderator' || $user->account_type == 'admin') {
                $profile = DB::table('user_profiles')->where('account_id', $request->account_id)->first();
                $get_country = DB::table('countries')->where('name', $profile->country ?? 'Nigeria')->orWhere('country_code', $profile->country ?? 'NG')->first();
                if (isset($get_country)) {
                    $country = $get_country->country_code;
                } else {
                    $country = 'NG';
                }
            } elseif ($user->account_type == 'business') {
                $profile = DB::table('business_profiles')->where('account_id', $request->account_id)->first();

                $get_country = DB::table('countries')->where('name', $profile->country ?? 'Nigeria')->orWhere('country_code', $profile->country ?? 'NG')->first();
                if (isset($get_country)) {
                    $country = $get_country->country_code;
                } else {
                    $country = 'NG';
                }
            } else {
                return $this->response('error', 'User Does Not Have An Account Type', null, 400);
            }

            try {
                // Send Reset Password
                if ($user->phone_number !== null) {

                    $this->sendRawSms($user, $country, "Hi  " . $user->firstname . ", Your password reset code is: " . $request->token  . ". Update Password Link: - " . env('SITE_URL') . "/" . "reset-password/" . $user->account_id);
                } else {
                    return $this->response('error', 'User Does Not Have A Phone Number', null, 400);
                }

                return $this->response('ok', 'Password Reset SMS Sent.', ['user' => $user], 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/phone/send/reset_password/done",
     *     summary="Send Reset Password Done Mail",
     *     operationId="Send Reset Password Done Mail",
     *     tags={"Sms Notifications"},
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
    public function sendResetPasswordDone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->account_id);

            $message = 'Hi ' . $user->email_address . ', your password has been updated securely.';

            try {

                $this->sendRawSms($user, $this->countryCode($user->account_id), $message);

                return $this->response('ok', 'Password Reset Done SMS Sent.', null, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     path="/phone/send/welcome",
     *     summary="send Welcome Sms",
     *     operationId="send Welcome Sms",
     *     tags={"Sms Notifications"},
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
    public function sendWelcomeSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $user = User::find($request->account_id);

            $message = 'Hello ' . $user->phone_number . ', Welcome To Vesicash, Your account has been registered on our platform and you can access it at any time.';

            try {
                $this->sendRawSms($user, $this->countryCode($user->account_id), $message);

                return $this->response('ok', 'Welcome SMS Sent.', null, 200);
            } catch (\Exception $e) {
                // Silent
                // return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }

    public function countryCode($account_id)
    {
        $user = User::where('account_id', $account_id)->first();
        $profile = '';

        if ($user->account_type == 'individual') {
            $profile = UserProfile::where('account_id', $account_id)->first();
        } elseif ($user->account_type == 'business') {
            $profile = BusinessProfile::where('account_id', $account_id)->first();
        } else {
            $profile = UserProfile::where('account_id', $account_id)->first();
        }

        if ($profile->country == '' || $profile->country == NULL) {
            return 'NG';
        }

        $countries = Countries::where('name', $profile->country)->orWhere('country_code', $profile->country)->first();

        return $countries->country_code;
    }
}
