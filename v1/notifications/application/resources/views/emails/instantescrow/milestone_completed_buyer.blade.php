@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    >Hi {{ $payload->buyer->first_name ?? $payload->buyer->email_address }}, </p>
   <>A milestone on Escrow Transaction {{ $payload->transaction->transaction_id ?? '' }} has been approved successfully. The next Milestone has commenced.</p>
   <><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
  <p>Sincerely,</p>
  <p>The Vesicash Team</p>

</div>
@endsection