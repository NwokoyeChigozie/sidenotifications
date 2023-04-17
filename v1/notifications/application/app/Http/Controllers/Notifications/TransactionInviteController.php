<?php

namespace App\Http\Controllers\Notifications;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Mail\InviteToViewTransaction;
use App\Mail\InviteToManageTransaction;
use Illuminate\Support\Facades\Mail;

class TransactionInviteController extends Controller
{
    public function sendInviteToViewTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $note = 'Invited to View Transaction (' . $request->transaction_id . ')';

        $d = $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'invite-to-view-transaction';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }


        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }

        $user = User::find($request->input('account_id'));

        if (!$user) :
            return $this->response('error', 'User does not exist', null, 400);
        endif;

        try {

            Mail::to($user->email_address)->send(new InviteToViewTransaction($user, $d));

            return $this->response('ok', 'Invite To View Transaction Email Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
    public function sendInviteToManageTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => ['required', 'exists:users'],
            'transaction_id' => ['required']
        ]);

        if ($validator->fails()) {
            return $this->response('error', 'Validation Error', $validator->errors(), 400);
        }

        // Check if this transaction exists!

        if (!$this->transactionExists($request->transaction_id)) {
            return $this->response('ok', 'This transaction does not exist.', null, 404);
        }

        $note = 'Invited to Manage Transaction (' . $request->transaction_id . ')';

        $d = $this->processNotificationData($request, $note);

        // Notification slug
        $slug = 'invite-to-view-transaction';
        // Make sure business ignored notifications is not null
        if ($this->ignoreNotification($slug, $this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Ignored.', null, 200);
            exit;
        }


        if ($this->disabledNotifications($this->data->business)) {
            return $this->response('ok', 'Transaction Notification Has Been Disabled.', null, 200);
            exit;
        }

        $user = User::find($request->input('account_id'));

        if (!$user) :
            return $this->response('error', 'User does not exist', null, 400);
        endif;

        try {

            Mail::to($user->email_address)->send(new InviteToManageTransaction($user, $d));

            return $this->response('ok', 'Invite To Manage Transaction Email Sent.', null, 200);
        } catch (\Exception $e) {
            return $this->response('error', 'Internal Error', $e->getMessage(), 400);
        }
    }
}
