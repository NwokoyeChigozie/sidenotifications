@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
    Hi {{ $payload->seller->first_name ?? $payload->seller->email_address }}.
    <br><br>
    Your delivery for order {{ $payload->transaction->transaction_id }} has just been accepted. The transaction funds will be disbursed immediately.
    <br><br>
    <a href="{{ env('SITE_URL') . '/login?account-id=' . $payload->seller->account_id }}">My Transactions</a>
    </p>

</p>
</div>
@endsection
