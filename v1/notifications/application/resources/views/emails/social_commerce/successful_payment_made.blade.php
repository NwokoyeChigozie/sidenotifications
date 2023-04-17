@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $payload->buyer->firstname }}, </p>
    <p>Please find below the receipt for your Trizact escrow payment.</p>
    <p>
        You can manage your transaction from your Dashboard <a href="{{ env('SITE_URL') . '/login?account-id=' . $payload->buyer->account_id }}">Dashboard</a>
    </p>
</div>
@endsection
