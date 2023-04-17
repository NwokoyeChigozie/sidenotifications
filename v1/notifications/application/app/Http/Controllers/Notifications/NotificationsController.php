<?php

namespace App\Http\Controllers\Notifications;

use App\Models\AppNotifications;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    public function list(Request $request) {
        $validator = Validator::make($request->all(), [
           'account_id' => ['required', 'exists:users,account_id']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $notifications = AppNotifications::where('account_id', $request->account_id)->where('is_read', false)->get();

            $data = [];
            foreach($notifications as $notification) {
                $data[] = $notification;
            }

            $array = array_values($data);

            return $this->response('ok', 'Data Retrieved', $notifications, 200);

        }
    }

    public function all(Request $request) {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users,account_id']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            $notifications = AppNotifications::where('account_id', $request->account_id)->where('is_read', true)->orderBy('created_at', 'DESC')->get();

            $data = [];
            foreach($notifications as $notification) {
                $data[] = $notification;
            }

            $array = array_values($data);

            return $this->response('ok', 'Data Retrieved', $notifications, 200);

        }
    }

    public function read(Request $request) {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users,account_id']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        } else {

            AppNotifications::where('account_id', $request->account_id)->where('is_read', false)->update([
                'is_read' => true
            ]);

            return $this->response('ok', 'Data Retrieved', ['updated' => true], 200);
        }
    }
}
