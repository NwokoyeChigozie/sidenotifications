@extends('emails.default')
@section('contents')
<div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
    <h1 style="margin-top: 0px;">Dear {{ $user->email_address }}</h1>
    <div style="color: #636363; font-size: 14px;">

    <p>This is to inform you that the supplied {{ strtoupper($payload->type) }} could not be verified on our platform for these reasons: <br>
        {{ $payload->reason }}.</p>

    <p>Please login to your dashboard <a href="https://vesicash.com/account/settings">here</a> and supply your valid {{ strtoupper($payload->type) }}.</p> 

    <p>Thank you for choosing Vesicash.</p>

    If you have any questions, be sure to check our FAQ page (insert link) or send an email to support@vesicash.com
</div>
@endsection