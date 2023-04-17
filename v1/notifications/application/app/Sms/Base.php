<?php

namespace App\Sms;

use GuzzleHttp\Client;
use Propaganistas\LaravelPhone\PhoneNumber;

class Base
{
    protected $_endpoint;
    protected $_apiKey;
    protected $_service;
    protected $_phone;
    protected $_appKey;
    protected $_country;
    protected $_message;

    public function __construct()
    {
        $this->_endpoint = env('RC_BASE_URL');
        $this->_apiKey = env('RC_API_KEY');
        $this->_service = 'SMS';
        $this->_appKey = env('RC_APP_KEY');
    }

    public function to($phone)
    {
        $this->_phone = $phone;
        return $this;
    }

    public function country($country = 'NG')
    {
        if (empty($country)) {
            $this->_country = 'NG';
        } else {
            $this->_country = $country;
        }
        return $this;
    }

    public function message($message)
    {
        $this->_message = $message;
        return $this;
    }

    public function send()
    {
        $client = new Client(['base_uri' => env('RC_BASE_URL')]);
        $phone = (string) PhoneNumber::make($this->_phone, $this->_country);
        $response = $client->request('POST', $this->_appKey . '/sms', [
            'form_params' => [
                'api_key' => $this->_apiKey,
                'phone'   => $phone,
                'message' => $this->_message
            ]
        ]);

        return true;
    }
}
