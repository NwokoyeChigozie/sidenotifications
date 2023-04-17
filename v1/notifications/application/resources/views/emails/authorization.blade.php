@extends('emails.default')
@section('contents')
    <div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
        <h1 style="margin-top: 0px;">Hi {{ $user->firstname ?? $user->email_address }}</h1>
        <div style="color: #636363; font-size: 14px;">
            <p>
                Your account is being accessed from an unknown device or location.
            </p>
            <p>
                We need your authorization.
            </p>
            <ul>
                <li>IP Address: {{ $payload->ip }}</li>
                <li>Device: {{ $payload->device }}</li>
                <li>Location: {{ $payload->location }}</li>
            </ul>
        </div>
        <a href="{{ env('APP_URL') }}/auth/authorize/{{ $payload->token }}" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Authorize</a>

        <a href="{{ env('APP_URL') }}/auth/authorize/decline/{{ $payload->token }}" style="padding: 8px 20px; background-color: red; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Decline</a>
    </div>
@endsection