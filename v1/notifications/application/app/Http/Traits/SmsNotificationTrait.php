<?php

namespace App\Http\Traits;

use GuzzleHttp\Client;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Sms\Base;
use AfricasTalking\SDK\AfricasTalking;


trait SmsNotificationTrait
{
    /**
     * Send sms without data types
     *
     * @param [type] $recipient
     * @param [type] $message
     * @return void
     */
    public function sendRawSms($recipient, $country = 'NG', $message)
    {

        // Termii Integration
        try {
            $client = new Client(['base_uri' => '']);

            $client->request('POST', 'https://termii.com/api/sms/send', [
                'form_params' => [
                    'api_key' => env('TERMII_API_KEY'),
                    'from'    => 'N-Alert',
                    'type'    => 'plain',
                    'channel' => 'dnd',
                    'to'      => (string) PhoneNumber::make($recipient->phone_number, $country),
                    'sms'     => $message
                ]
            ]);

            return true;
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
    /**
     * Globally initiate sending of sms with notifications
     *
     */
    public function sendSms($recipient, $data, $notification_type)
    {
        $message = $this->getMessage($notification_type, $data);

        // Termii Integration
        try {
            $client = new Client(['base_uri' => '']);

            $client->request('POST', 'https://termii.com/api/sms/send', [
                'form_params' => [
                    'api_key' => env('TERMII_API_KEY'),
                    'from'    => 'Vesicash',
                    'type'    => 'plain',
                    'channel' => 'generic',
                    'to'      => (string) PhoneNumber::make($recipient->phone_number, $recipient->getCountry()),
                    'sms'     => $message
                ]
            ]);

            return true;
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }

    public function getMessage($notification_type, $data)
    {
        $message = '';

        switch ($data->transaction->type) {
            case 'product':
                $message = $this->setMessage($notification_type, $data);
                break;

            case 'milestones':
                // TODO: return message of the milestone transaction
                $message = $this->setMessage($notification_type, $data);
                break;

            case 'oneoff':
                // TODO: return message of the oneoff transaction
                $message = $this->setMessage($notification_type, $data);
                break;
        }

        return $message;
    }

    public function setMessage($notification_type, $data)
    {
        $message = '';
        switch ($notification_type) {
            case 'transaction-sent':
                $message = 'Your escrow transaction has been sent and you will be notified when it has been paid for.';
                break;
            case 'transaction-received':
                $message = 'You have received a new Escrow transaction from ' . $data->sender->email_address . '. Kindly check your email for full details.';
                break;
            case 'transaction-accepted':
                $message = 'Your transaction (' . $data->transaction->transaction_id . ') on Vesicash Escrow has been accepted.';
                break;
            case 'transaction-rejected':
                $message = 'Your transaction (' . $data->transaction->transaction_id . ') on Vesicash Escrow has been rejected by' . $data->recipient->email_address . '. Kindly check your email for full details.';
                break;
            case 'transaction-paid':
                $message = 'Your transaction (' . $data->transaction->transaction_id . ') on Vesicash Escrow has been paid for. Please go ahead with the delivery.';
                break;
            case 'transaction-delivered':
                $message = 'Did you receive a shipment for transaction (' . $data->transaction->transaction_id . ') on Vesicash?. Kindly check your email for full details';
                break;
            case 'transaction-delivered-accepted':
                $message = 'Your transaction (' . $data->transaction->transaction_id . ') on Vesicash has been accepted. Seller will receive escrow funds shortly.';
                break;
            case 'transaction-delivered-rejected':
                $message = 'Your transaction (' . $data->transaction->transaction_id . ') on Vesicash has been rejected by ' . $data->buyer->email_address;
                break;
            case 'escrow-disbursed-seller':
                $message = 'Dear ' . $data->seller->email_address . ', Vesicash has just disbursed funds for the transaction (' . $data->transaction->transaction_id . ') into your account.';
                break;
            case 'escrow-disbursed-buyer':
                $message = 'Vesicash Escrow has just disbursed funds for the transaction (' . $data->transaction->transaction_id . ').';
                break;
            case 'transaction-closed-seller' || 'transaction-closed-buyer':
                $message =  'Your transaction (' . $data->transaction->transaction_id . ') has been fulfilled and will be closed shortly.';
                break;
                // case 'transaction-closed-buyer':
                //     $message =  'Your transaction ('. $data->transaction->transaction_id .') has been fulfilled and will be closed shortly.';
                //     break;
            case 'dispute-opened':
                $message =  'A dispute has been opened by ' . $data->buyer->email_address . ' on your transaction - (' . $data->transaction->transaction_id . '). Kindly check your email for full details.';
                break;
            case 'due-date-extension':
                $message = 'Dear ' . $data->seller->email_address . ', ' . $data->buyer->email_address . ' has extended the due date of your transaction (' . $data->transaction->transaction_id . '). Check your email for full details.';
                break;
            case 'due-date-proposal':
                $message = 'Dear ' . $data->buyer->email_address . ', ' . $data->seller->email_address . ' wants you to extend the due date for transaction (' . $data->transaction->transaction_id . '). Check your email for full details.';
                break;
            case 'successful-refund':
                $message = 'Dear ' . $data->buyer->email_address . ', you have been refunded the sum of ' . $data->transaction->currency . ' ' . $data->payment->total_amount . ' for the transaction (' . $data->transaction->transaction_id . ').';
                break;
        }
        return $message;
    }

    // public function setOneoffMessage() {}

    // public function setMilestoneMessage() {}
}
