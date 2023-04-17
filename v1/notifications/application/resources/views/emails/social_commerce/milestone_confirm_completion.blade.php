@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi, {{ $payload->buyer->firstname ?? 'Buyer' }}, </p>
    <p>A milestone on Escrow Transaction {{ $payload->transaction->transaction_id ?? '' }} has been approved successfully. The next Milestone has commenced.</p>
    <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
    <p>Sincerely,</p>
    <p>The Vesicash Team</p>
</div>
@endsection