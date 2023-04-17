<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use App\Models\BankDetails;
use Illuminate\Support\Str;
use App\Models\AccessTokens;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\AppNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * @OA\SecurityScheme(
 *   securityScheme="v-public-key",
 *   type="apiKey",
 *   name="v-public-key",
 *   in="header"
 * )
 */

/**
 * Class Controller
 * @package App\Http\Controllers
 * @OA\OpenApi(
 *     @OA\Info(
 *      version="1.0.0",
 *      title="Vesicash Internal Notifications API Documentation",
 *      description="Internal documentation of notifications service of the  Vesicash API's",
 *       @OA\License(name="MIT")
 *     ),
 *     @OA\Server(
 *         description="API server",
 *         url="/v1/notifications",
 *     ),
 * )
 */
class Controller extends BaseController
{
    protected $data;

    public function response($status = 'error', $message = 'Status not defined', $data = [], $code = '400')
    {
        return \response()->json(['status' => $status, 'code' => $code, 'message' => $message, 'data' => $data], $code);
    }

    public function callApi($uri, $data, $token = null)
    {

        $privatekey = $_SERVER['HTTP_V_PRIVATE_KEY'] ?? $token ??  AccessTokens::all()->first()->private_key;

        try {
            $headers = array('Accept' => 'application/json', 'v-private-key' => $privatekey);

            $client = new Client([
                'headers' => $headers,
                'http_errors' => false
            ]); //GuzzleHttp\Client

            // dd($client);

            $result = $client->post(env('APP_URL') . $uri, [
                'json' => $data
            ]);

            return $result->getBody()->getContents() ?? NULL;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        //        try {
        //            $headers = array('Accept' => 'application/json', 'v-private-key' => @$_SERVER['HTTP_V_PRIVATE_KEY'], 'v-public-key' => @$_SERVER['HTTP_V_PUBLIC_KEY']);
        //
        //            $client = new Client([
        //                'headers' => $headers,
        //                'http_errors' => false
        //            ]); //GuzzleHttp\Client
        //
        //            $result = $client->post(env('APP_URL') . $uri, [
        //                'json' => $data
        //            ]);
        //
        //            return $result->getBody()->getContents();
        //
        //        } catch (\Exception $e) {
        //            Log::error($e->getMessage());
        //        }
    }

    public function fireBaseNotfication($token, $title = 'Vesicash Transaction Update', $body, $type = 'transaction')
    {

        try {
            $headers = array('Accept' => 'application/json', 'Authorization' => env('FIREBASE_KEY'));
            $data = [
                'notification'  => [
                    'title'     => $title,
                    'body'      => $body,
                    'icon'      => 'https://vesicash.com/vesicash.png',
                    // 'id'        => Str::random(),
                    'type'      => $type
                ],
                'to'            => $token
            ];

            $client = new Client([
                'headers'       => $headers,
                'http_errors'   => false
            ]);

            $result = $client->post(env('FIREBASE_URL'), [
                'json' => $data
            ]);

            return $result->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     *  Get all data needed to process the notification
     *
     * @param Request $request
     * @param null $note
     * @return object
     */
    public function processNotificationData(Request $request, $note = null)
    {
        $token = (empty($request->header('v-private-key')) ? $request->header('v-public-key') : $request->header('v-private-key'));

        $has_error = false;
        $error = [];

        // Get the transaction details
        $encodedTransaction = $this->callApi('/transactions/listById/' . $request->transaction_id, [], $token);

        $transaction = json_decode($encodedTransaction)->data->transaction;

        if ($transaction == null) {
            return $this->response('error', 'This transaction does not exist.', null, 404);
        }

        // Get the payment details of the transaction
        $encodedPayment = $this->callApi('/payment/list', ['transaction_id' => $request->transaction_id], $token);

        $payment = json_decode($encodedPayment)->data->payments;

        //        if ($payment == null) {
        //            return $this->response('error', 'Payment record does not exist.', null, 404);
        //        }

        // Is this notification sent on behalf of a business?

        $accounts = (new AccessTokens())->getBusiness($token);

        $business_profile = DB::table('business_profiles')->where('account_id', $transaction->business_id)->first();

        if (!$business_profile) {
            $has_error = true;
            $error[] = 'Transaction business owner has no business profile data.';
        }

        // dd($transaction->parties->buyer->id);

        if (empty($transaction->parties->buyer->account_id)) {
            $has_error = true;
            $error[] = 'Transaction has no buyer party';
        }

        if (empty($transaction->parties->seller->account_id)) {

            $has_error = true;
            $error[] = 'Transaction has no seller party';
        }

        //        if (empty($transaction->parties->recipient->account_id)) {
        //
        //            $has_error = true;
        //            $error[] = 'Transaction has no recipient party';
        //
        //        }
        //
        //        if (empty($transaction->parties->sender->account_id)) {
        //
        //            $has_error = true;
        //            $error[] = 'Transaction has no sender party';
        //
        //        }

        if ($has_error) {
            $data = [
                'error' => true,
                'errors' => $error
            ];

            return $this->data = (object) $data;
        }

        // Get the buyer, seller, recipient, sender details
        $buyer = User::where('account_id', $transaction->parties->buyer->account_id)->first();

        $seller = User::where('account_id', $transaction->parties->seller->account_id)->first();

        $seller_bank_details = BankDetails::where('account_id', $transaction->parties->seller->account_id)->first();

        //        $recipient = User::where('account_id', $transaction->parties->recipient->account_id)->first();
        //
        //        $sender = User::where('account_id', $transaction->parties->sender->account_id)->first();

        $broker = [];
        if ($transaction->type == 'broker') {
            $broker = User::where('account_id', $transaction->parties->broker->account_id)->first();
        }

        $action_button = $request->action_button;

        if ($action_button) {
            if (!\is_array($action_button)) {
                return $this->response('error', 'Action button value must be an array.', null, 404);
                exit;
            }

            if (\is_array($action_button)) {

                if (!isset($action_button['text'])) {
                    return $this->response('error', 'Action button array must be have a text.', null, 404);
                    exit;
                }

                if (!isset($action_button['link'])) {
                    return $this->response('error', 'Action button array must be have a link.', null, 404);
                    exit;
                }
            }
        }

        $data = [
            'buyer' => $buyer,
            'seller' => $seller,
            'seller_bank_details' => $seller_bank_details,
            'recipient' => null,
            'broker' => $broker,
            'sender' => null,
            'payment' => $payment,
            'transaction' => $transaction,
            'note' => $note,
            'business' => $business_profile,
            'action_button' => (object) $action_button
        ];

        return $this->data = (object) $data;
    }

    public function inAppNotification($user_id, $content, $payload = null)
    {
        $notification = new AppNotifications();
        $notification->account_id = $user_id;
        $notification->content = $content;
        $notification->payload = json_encode($payload);
        $notification->transaction_id = $payload['transaction_id'] ?? null;
        $notification->is_read = false;
        $notification->save();
    }

    /**
     * transactionExists
     * Find a transaction and if it returns null exit!
     * @param [type] $id
     * @return void
     */
    public function transactionExists($id)
    {
        // Get the transaction details
        $encodedTransaction = $this->callApi('/transactions/listById/' . $id, [], null);

        // check if data object is exists
        if (!is_object(json_decode($encodedTransaction)->data)) {
            return false;
        }

        $transaction = json_decode($encodedTransaction)->data->transaction;

        if ($transaction == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Ignore notifications
     */
    public function ignoreNotification($slug, $business)
    {
        if (isset($business->business_ignored_notifications)) {
            // Make sure buisness ignored notifications is not null
            if (@$business->business_ignored_notifications != NULL) {

                // Confirm that this notification is ignored
                $ignoredCheck = json_decode(@$business->business_ignored_notifications, true);

                if (in_array($slug, $ignoredCheck)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function disabledNotifications($business)
    {
        if (isset($business->business_disabled_notifications)) {
            if ($business->business_disabled_notifications === TRUE) {
                return true;
            } else {
                return false;
            }
        }
    }
}
