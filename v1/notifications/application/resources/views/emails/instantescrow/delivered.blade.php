@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
        Hi {{ $payload->seller->first_name ?? $payload->seller->email_address }}.
    </p>
    <p>Escrow Transaction {{ $payload->transaction->transaction_id ?? '' }} has been marked as Done. Kindly confirm, and update the status of the transaction. If no action is taken before {{ Carbon\Carbon::createFromTimestamp($payload->transaction->inspection_period)->toDateTimeString() ?? '' }}, the escrowed funds will be released.</p>

    <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
    <p>Sincerely,</p>
    <p>The Vesicash Team</p>

</div>
@endsection