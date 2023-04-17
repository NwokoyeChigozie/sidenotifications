<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $_endpoint;
    protected $_apiKey;
    protected $_service;
    protected $_phone;
    protected $_appKey;
    protected $_country;
    protected $_message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $object)
    {
        $this->_endpoint = env('RC_BASE_URL');
        $this->_apiKey = env('RC_API_KEY');
        $this->_service = 'SMS';
        $this->_appKey = env('RC_APP_KEY');
        $this->_phone = $object->phone;
        $this->_country = $object->country;
        $this->_message = $object->message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
         $client = new Client(['base_uri' => env('RC_BASE_URL')]);
         $response = $client->request('POST', $this->_appKey . '/sms', [
             'form_params' => [
                 'api_key' => $this->_apiKey,
                 'phone'   => (string) PhoneNumber::make( $this->_phone, $this->_country ),
                 'message' => $this->_message
             ]
         ]);
    }
}
