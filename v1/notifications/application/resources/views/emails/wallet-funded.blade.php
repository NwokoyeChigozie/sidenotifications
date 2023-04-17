@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $user->firstname ?? $user->email_address }}, </p>
    <p>Your Vesicash wallet has been successfully funded. Below is a summary of your transaction for your review:</p>
    <p>Funding Amount: {{ $payload->currency }} {{ $payload->amount }}</p>
    <p>Timestamp: {{ $payload->transaction->updated_at }}</p>
    <p>Do contact us if you have nay questions or issues with this transaction. Thank you,</p>
    <br>
    <p>Best,</p>
    <p>The Vesicash Team</p>
    <!-- <p>Your funds ( {{ $payload->currency }} {{ $payload->amount }}) @if(!$payload->transaction == null) for transaction {{ $payload->transaction->title ?? $payload->transaction->transaction_id }} @endif has been successful disbursed into your Vesicash wallet. Head over to your dashboard {{ $payload->business->website ?? env('SITE_URL') }} to see your wallet balance.
    </p>
    <p>
        If you need further help, check our FAQ page <a href="{{ env('SITE_URL') }}/faq">FAQ</a> or send an email to support@vesicash.com</a>.
    </p> -->
</div>
@endsection