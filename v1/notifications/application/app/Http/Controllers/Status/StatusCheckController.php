<?php

namespace App\Http\Controllers\Status;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusCheckController extends Controller
{
    public function statusCheck(){
        $url = config("vesicash.notification_base_url");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        $httpCode = $info['http_code'];

        if ($httpCode >= 200 && $httpCode < 300){
            return $this->response('ok', 'Server is up', null, $httpCode);
        }else{
            return $this->response('failed', 'Server is down', null, $httpCode);
        }
    }
}
