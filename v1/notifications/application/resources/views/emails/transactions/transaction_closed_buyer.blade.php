@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $payload->buyer->firstname ?? $payload->buyer->email_address }},</p>

    <p>Escrow Transaction ({{ $payload->transaction->transaction_id ?? $payload->transaction->title }}) has been approved and is therefore completed. All transaction funds will be released from Escrow.</p>
    <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
    <p>Sincerely,</p>
    <p>The Vesicash Team</p>
</div>
@endsection