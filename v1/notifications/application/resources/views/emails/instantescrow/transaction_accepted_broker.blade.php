@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hello {{ $payload->sender->email_address ?? '' }}, </p>
    <p>
        Your escrow transaction ({{ $payload->transaction->transaction_id ?? '' }}) from {{ $payload->sender->email_address ?? '' }}. They will be expecting you to deliver as agreed.
    </p>

    <p>
    You can view and manage your transaction by visiting your <a href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->seller->phone_number ?? '' }}&customer-email={{ $payload->seller->email_address ?? '' }}">dashboard</a>

<br>
If you have any questions, or require any assistance, call our hotline +234 802 080 9509 or send an email to support@vesicash.com.
<br>
Thank you for using Vesicash 😊

    </p>

</div>
@endsection
