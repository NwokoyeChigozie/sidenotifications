@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
<p>
Hello {{ $payload->recipient->firstname ?? $payload->recipient->email_address ?? '' }},
<br>
You just accepted your escrow transaction ({{ $payload->transaction->transaction_id }}) from {{ $payload->sender->email_address ?? '' }}. They will be expecting you to deliver as agreed.
<br>
You can manage and view your transactions at <a href="{{ env('SITE_URL') }}">{{ env('SITE_URL') }}</a>
<br>
For questions or inquiries, please call our hotline +234 802 080 9509 or visit our FAQ Page <a href="{{ env('SITE_URL') }}/faq">{{ env('SITE_URL') }}/faq</a>
<br>
Thank you for using Vesicash 😊

</div>
@endsection
