@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $user->email_address ?? '' }}, </p>
    <p>Your client {{ $payload->buyer->firstname ?? $payload->buyer->email_address }} has successfully paid {{ $payload->transaction->currency ?? '' }}{{ $payload->transaction->amount }} into the escrow account. Kindly proceed with the delivery and track its progress <a href="{{ env('SITE_URL') . '/login?account-id=' . $user->account_id }}">here</a>.</p>
    <p>If you haven't uploaded your Banks Details or Identity document, do well to do so to receive your funds when the transaction is completed.</p>
</div>
@endsection
