@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $payload->seller->firstname ?? '' }}, </p>
    <p>
        Unfortunately there was a problem with your recent delivery {{ $payload->transaction->transaction_id }} and buyer {{ '('.$payload->buyer->firstname.')' }} has rejected your delivery. Please contact {{ $payload->buyer->firstname ?? $payload->buyer->email_address }} in order to resolve this dispute as previously agreed.
        <br><br>
        The transaction funds will be released to your provided bank account once the dispute is resolved.
        <br><br>
        <a href="{{ env('SITE_URL') . '/login?account-id=' . $payload->seller->account_id }}">My Transactions</a>
    </p>
</div>
@endsection
