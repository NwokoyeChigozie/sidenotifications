@extends('emails.default')
@section('contents')
<div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
    <h1 style="margin-top: 0px;">Dear {{ $user->email_address }}</h1>
    <div style="color: #636363; font-size: 14px;">

    <p>This is to inform you that the supplied {{ strtoupper($payload->type) }} has been successfully verified on our platform and your account has been moved to tier two.</p>

    <p>This means that all escrow fund disbursements to your account would happen seamless without any hindrance.
    </p> 

    <p>Thank you for choosing Vesicash.</p>

    If you have any questions, be sure to check our <a href='https://vesicash.com/faq'>FAQ page </a> or send an email to support@vesicash.com
</div>
@endsection