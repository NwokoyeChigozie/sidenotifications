@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $user->firstname ?? $user->email_address }}, <br>
    <p>Thank you for successfully signing up on Vesicash, we’re glad to have you. Our support team is on standby to offer any guidance you may require.
    </p>
    <p>P.s. <a href="{{ $links->faq }}" target="_blank">Our FAQs</a> can be found here</p>
    <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>

    <br>
    <p>Sincerely, <br>The Vesicash Team.</p>
</div>
@endsection