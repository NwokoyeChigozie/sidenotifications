@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $user->email_address ?? $user->firstname }}, </p>

    <p> You are expected to deliver transaction "{{ $payload->transaction->title ?? $payload->transaction->transaction_id }}" to {{ $payload->buyer->firstname ?? $payload->buyer->email_address }} by {{ $payload->transaction->due_date ?? '' }}. If you have done this, kindly login <a href="{{ $payload->business->website ?? env('SITE_URL') . '/login?account-id=' . $user->account_id }}">here</a> to confirm delivery so that you can both proceed to the next phase of the transaction.</p>
    <p>
    If you need further help, check our <a href="https://vesicash.com/faq">FAQ page</a> or send an email to support@vesicash.com
    </p>
</div>
@endsection
