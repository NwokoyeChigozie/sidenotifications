@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
<p>
Hello {{ $payload->sender->firstname ?? $payload->sender->email_address ?? '' }},
<br>
{{ $payload->recipient->firstname ?? $payload->recipient->email_address }} just accepted your escrow transaction ({{ $payload->transaction->transaction_id }}). We will be expecting {{ $payload->recipient->firstname}} to deliver as agreed.
<br>
You can manage and view your transactions at <a href="{{ env('SITE_URL') }}">{{ env('SITE_URL') }}</a>
<br>
For questions or inquiries, please call our hotline +234 802 080 9509 or visit our FAQ Page <a href="{{ env('SITE_URL') }}/faq">{{ env('SITE_URL') }}/faq</a>
<br>
Thank you for using Vesicash 😊

</div>
@endsection
