@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $user->email_address ?? '' }}, </p>
    <p>Your client {{ $payload->buyer->firstname ?? $payload->buyer->email_address }} has successfully paid {{ $payload->transaction->currency ?? '' }}{{ $payload->transaction->amount }} into the escrow account. Kindly contact the seller to proceed with delivery and you can track its progress <a href="{{ env('SITE_URL') . '/login?account-id=' . $user->account_id }}">here</a>.</p>

</div>
@endsection
