@extends('emails.default')
@section('contents')
<div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
    <h1 style="margin-top: 0px;">Dear, {{ $user->firstname ?? $user->email_address }}</h1>
    <div style="color: #636363; font-size: 14px;">

       <p>
        Unfortunately, we could not complete your disbursement request for @if($payload->transaction->transaction !== null) transaction ({{ $payload->transaction->transaction->transaction_id ?? $payload->transaction->transaction->title }}) @elseif($payload->payment !== null) payment ({{ $payload->payment->payment_id }}) @endif. This could be because the details you provided are incorrect or missing.
       </p>
       <p>

        Please review your: {{ $payload->reason }} details and update the information provided so that your funds can be disbursed as soon as possible. Head over to <a href="{{ $payload->business->website }}">{{ $payload->business->website }}</a> or the vesicash <a href="{{ env('SITE_URL') . '/login?account-id=' . $user->account_id }}">dashboard</a>

        If you have any questions, be sure to check our FAQ page (insert link) or send an email to support@vesicash.com
       </p>
    <!-- <a href="https://vesicash.com/login" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Complete my profile.</a> -->
</div>
@endsection
