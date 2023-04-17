<?php

namespace App\Http\Controllers\Contact;

use App\Models\AccessTokens;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Mail\ContactFormNotification;

class ContactFormNotificationController extends Controller
{

    /**
     * @OA\Post(
     *     path="/contact/send",
     *     summary="Send Mail to the Vesicash Support",
     *     operationId="Send Support mail",
     *     tags={"Vesicash Support"},
     *     security={{ "v-public-key": "" }},
     *     @OA\Parameter(
     *         name="firstname",
     *         in="query",
     *         description="Firstname",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="lastname",
     *         in="query",
     *         description="lastname",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Email Address",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="business_type",
     *         in="query",
     *         description="Business type",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="Country",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="website_url",
     *         in="query",
     *         description="Website Url",
     *         required=true,
     *         @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *         name="message",
     *         in="query",
     *         description="Message - body of the mail",
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
    public function sendContactFormMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname'     => 'required|string|max:50',
            'lastname'      => 'required|string|max:50',
            'email'         => 'required|string|email',
            'business_type' => 'required|string|max:255',
            'country'       => 'required|string',
            'website_url'   => 'required|string',
            'message'       => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        try {

            $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

            $accounts = (new AccessTokens())->getBusiness($token);

            $business_profile = BusinessProfile::where('account_id', $accounts->account_id)->first();

            $payload = (object) [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'business_type' => $request->business_type,
                'country' => $request->country,
                'website_url' => $request->website_url,
                'message' => $request->message,
                'business' => $business_profile
            ];

            Mail::to('support@vesicash.com')->send(new ContactFormMail($payload));

            return $this->response('ok', 'Contact Form Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
