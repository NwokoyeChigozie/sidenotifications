@extends('emails.default')
@section('contents')
<div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
    <h1 style="margin-top: 0px;">Hi {{ $user->email_address }}</h1>
    <div style="color: #636363; font-size: 14px;">

    <p>You can pick up from where you left off! Complete your profile now to start making secure and transparent payments that {{ $payload->business->business_name }} has to offer. Click here <a href="{{ $payload->business->website ?? env('SITE_URL') . '/login?account-id=' . $user->account_id }}">{{ $payload->business->website ?? env('SITE_URL') }}</a> to finish updating your profile.</p>

    If you have any questions, be sure to check our <a href="https://vesicash.com/faq">FAQ page</a> or send an email to support@vesicash.com

    <!-- <a href="https://vesicash.com/login" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Complete my profile.</a> -->
</div>
@endsection
