@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 16px; line-height: 24px;">
    <p>Hello {{ $payload->seller->email_address ?? '' }}, </p>
    <p>
        You just accepted the escrow transaction - ({{ $payload->transaction->transaction_id ?? '' }}) with title "{{ $payload->transaction->title }}" from {{ $payload->sender->email_address ?? '' }}.
    </p>

    <p style="margin-bottom: 20px;">
        You can view and manage your transaction by visiting your <a href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->seller->phone_number ?? '' }}&customer-email={{ $payload->seller->email_address ?? '' }}">dashboard</a>
    </p>

    <p style="margin-bottom: 10px;">
        If you have any questions, or require any assistance, call our hotline +234 802 080 9509 or send an email to support@vesicash.com.
    </p>

    <p> Thank you for using Vesicash 😊</p>
</div>
@endsection