<?php

namespace App\Http\Controllers\Login;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Mail\SendOneTimePassword;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Traits\SmsNotificationTrait;
use Illuminate\Support\Facades\Validator;
use Propaganistas\LaravelPhone\PhoneNumber;
use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class OtpNotificationController extends Controller
{

    use SmsNotificationTrait;

    /**
     * @OA\Post(
     *     path="/send_otp",
     *     summary="Send OTP Notification ",
     *     operationId="Send OTP Notification",
     *     tags={"OTP Notifications"},
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
     *     @OA\Parameter(
     *         name="otp_token",
     *         in="query",
     *         description="OTP Token",
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
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required'],
            'otp_token'  => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        $user = DB::table('users')->where('account_id', $request->account_id)->first();
        $profile = '';
        $country = '';

        if ($user->account_type == 'individual' || $user->account_type == 'moderator' || $user->account_type == 'admin') {
            $profile = DB::table('user_profiles')->where('account_id', $request->account_id)->first();

            $country = DB::table('countries')->where('name', $profile->country ?? 'Nigeria')->orWhere('country_code', $profile->country ?? 'NG')->first()->country_code;
        } elseif ($user->account_type == 'business') {
            $profile = DB::table('business_profiles')->where('account_id', $request->account_id)->first();

            $country = DB::table('countries')->where('name', $profile->country ?? 'Nigeria')->orWhere('country_code', $profile->country ?? 'NG')->first()->country_code;
        } else {
            return $this->response('error', 'User Does Not Have An Account Type', null, 400);
        }

        try {
            // $client = new Client(['base_uri' => env('RC_BASE_URL')]);

            if ($user->phone_number !== null):

                $this->sendRawSms($user, $country, 'Hello ' . $user->firstname . ', Your One-Time Password is: ' . $request->otp_token);

            endif;

            Mail::to($user->email_address)->send(new SendOneTimePassword($user, $request->otp_token));
        } catch (\Exception $e) {
            // return $this->response('error', 'Otp Notification was not sent, posible reason: ' . $e->getMessage() . ' on line: ' . $e->getLine(), 400);
            Log::error('Otp Notification was not sent, posible reason: ' . $e->getMessage() . ' on line: ' . $e->getLine());
        }

        return $this->response('ok', 'Otp Notification has been sent', null, 200);
    }
}
