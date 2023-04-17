@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
    Hello {{ $payload->seller->first_name ?? $payload->seller->email_address }}.
    <br>
    <p>
        Your escrow transaction ({{ $payload->transaction->transaction_id }}) is now complete and disbursement of your funds will take place shortly. To avoid any delay, ensure that you have provided your Bank Account information. You can do so by visiting the settings tab on your <a href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->seller->phone_number ?? '' }}&customer-email={{ $payload->seller->email_address ?? '' }}">dashboard</a>
    </p>

    <p>
        For questions or inquiries, please call our hotline +234 802 080 9509 or visit our FAQ Page

<br>
Thank you for using Vesicash 😊
    </p>
</p>
</div>
@endsection
