@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hello {{ $payload->seller->firstname ?? $payload->seller->email_address }}</p>
    <p>
        Your escrow transaction ({{ $payload->transaction->transaction_id ?? '' }}) is now complete and disbursement of your funds will take place shortly. To avoid any delay, ensure that you have provided your Bank Account information. You can do so by visiting the settings tab on your Dashboard <a href="https://vesicash.com/login">https://vesicash.com/login</a>
    </p> 
  
    <p>
        For questions or inquiries, please call our hotline +234 802 080 9509 or visit our <a href="https://vesicash.com/faq">FAQ Page</a>
    </p>
    <p>
        Thank you for using Vesicash 😊
    </p>
</div>
@endsection