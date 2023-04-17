<?php

namespace App\Http\Controllers\Plugins;

use App\Models\PluginEvent;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class EventNotificationController extends Controller
{
    //
    public function sendPluginEventNotification(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'url'            => ['required'],
            'email'          => ['required'],
            'event'          => ['required'],
            'plugin_name'    => ['required'],
            'plugin_details' => ['required'],
        ]);

        if ( $validator->fails() ) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }
        try {
            $plugin_event = new PluginEvent();
            $plugin_event->url = $request->url;
            $plugin_event->email = $request->email;
            $plugin_event->event = $request->event;
            $plugin_event->plugin_name = $request->plugin_name;
            $plugin_event->plugin_details = $request->plugin_details;
            $plugin_event->save();

            return $this->response('ok', 'success.', ['plugin_event' => $plugin_event], 200);

        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}

