<?php

namespace App\Http\Controllers\Login;

use App\Mail\AuthorizationMail;
use App\Mail\AuthorizedMail;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthorizationNotificationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/send_authorization",
     *     summary="Send Authorization",
     *     operationId="Send Authorization",
     *     tags={"Login Authorization Notifications"},
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
     *         name="token",
     *         in="query",
     *         description="Authorization Token",
     *         required=true,
     *         @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="ip",
     *         in="query",
     *         description="IP Address",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="The location",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="device",
     *         in="query",
     *         description="The user's device",
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
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'token'      => ['required'],
            'ip'         => ['required'],
            'location'   => ['required'],
            'device'     => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            try {
                $account_id = $request->account_id;
                $token = $request->token;
                $ip = $request->ip;
                $location = $request->location;
                $device = $request->device;

                $business = null;
                $user = User::where('account_id', $account_id)->first();

                $payload = (object) [
                    'business' => $business,
                    'account_id' => $account_id,
                    'ip' => $ip,
                    'token' => $token,
                    'location' => $location,
                    'device' => $device
                ];

                Mail::to($user->email_address)->send(new AuthorizationMail($user, $payload));

                return $this->response('ok', 'success', $payload, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }


    /**
     * @OA\Post(
     *     path="/send_authorized",
     *     summary="Send Authorized Notification",
     *     operationId="Send Authorized Notification",
     *     tags={"Login Authorization Notifications"},
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
     *         name="ip",
     *         in="query",
     *         description="IP Address",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="location",
     *         in="query",
     *         description="The location",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="device",
     *         in="query",
     *         description="The user's device",
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
    public function sendAuthorizedNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'ip'         => ['required'],
            'location'   => ['required'],
            'device'     => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {
            try {
                $account_id = $request->account_id;
                $ip = $request->ip;
                $location = $request->location;
                $device = $request->device;

                $business = null;
                $user = User::where('account_id', $account_id)->first();

                $payload = (object) [
                    'business' => $business,
                    'account_id' => $account_id,
                    'ip' => $ip,
                    'location' => $location,
                    'device' => $device
                ];

                Mail::to($user->email_address)->send(new AuthorizedMail($user, $payload));

                return $this->response('ok', 'success', $payload, 200);
            } catch (\Exception $e) {
                return $this->response('error', 'Internal Error', $e->getMessage(), 400);
            }
        }
    }
}
