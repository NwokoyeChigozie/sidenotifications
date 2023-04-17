@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
<p>Dear {{ $user->email_address }},</p>

<p>You still haven’t verified your email address. Please endeavor to do so as soon as possible in order to enjoy the full payment security benefits that using Vesicash offers you.</p>

<p>Click on <a href="{{ env('SITE_URL') }}/email-verify/{{ $user->account_id }}/{{ $token ?? 0 }}">this link </a>to verify your email.
<p>If you have any questions be sure to check our FAQ page https://vesicash.com/faq or send an email to support@vesicash.com.</p>

@endsection