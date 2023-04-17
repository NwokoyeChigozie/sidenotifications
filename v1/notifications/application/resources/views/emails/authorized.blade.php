@extends('emails.default')
@section('contents')
    <div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
        <h1 style="margin-top: 0px;">Hi {{ $user->firstname ?? $user->email_address }}</h1>
        <div style="color: #636363; font-size: 14px;">
            <p>
                You accessed your account recently.
            </p>
            <p>
                Below you'll find the details of the device used.
            </p>
            <ul>
                <li>IP Address: {{ $payload->ip }}</li>
                <li>Device: {{ $payload->device }}</li>
                <li>Location: {{ $payload->location }}</li>
            </ul>
        </div>
        <p>If you did not carry out this action, kindly click the button below to suspend access to your account.</p>
        <a href="https://vesicash.com/auth/suspend/{{ $payload->account_id }}" style="padding: 8px 20px; background-color: red; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Suspend Access</a>
    </div>
@endsection