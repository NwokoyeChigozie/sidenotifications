@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p style="margin-top: 0px;">Hi {{ $user->firstname ?? $user->email_address }},</p>

    <p>You have been invited to view an Escrow Transaction {{ $payload->transaction->transaction_id ?? $payload->transaction->title }}. Kindly click the link below to get started.</p>
    <p><a href="{{ $link }}" target="_blank">View Transaction</a></p>
    <p>Sincerely,</p>
    <p>The Vesicash Team</p>
</div>
@endsection