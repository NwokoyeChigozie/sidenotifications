@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
  <p>Hi {{ $payload->buyer->first_name ?? $payload->buyer->email_address }}, </p>
  <p>Escrow Transaction {{ $payload->transaction->transaction_id ?? '' }} has been marked as Done. Kindly confirm, and update the status of the transaction. If no action is taken before {{ Carbon\Carbon::createFromTimestamp($payload->transaction->inspection_period)->toDateTimeString() ?? '' }}, the escrowed funds will be released.</p>
  <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
  <p>Sincerely,</p>
  <p>The Vesicash Team</p>

  <!-- <p>
    Hello {{ $payload->buyer->first_name ?? $payload->buyer->email_address }}.
    <br>
  <table class="table table-bordered" style="width:100%">
    <tr>
      <th>Transaction Title:</th>
      <td>{{ $payload->transaction->title ?? '' }}</td>
    </tr>
    <tr>
      <th>Description:</th>
      <td>{{ $payload->transaction->description ?? '' }}</td>
    </tr>
    <tr>
      <th>Amount:</th>
      <td>{{ $payload->transaction->amount ?? '' }}</td>
    </tr>
    <tr>
      <th>Status:</th>
      <td>{{ $payload->transaction->status ?? '' }}</td>
    </tr>

  </table>
  <p>
    Has {{ $payload->seller->firstname ?? $payload->seller->email_address }} completed their end of transaction ({{ $payload->transaction->transaction_id ?? '' }}) as expected? ?If they have and you are satisfied with what you received, please follow this <a href="{{ env('INSTANT_ESCROW_URL') }}/selfservice/confirmTransaction/{{ $payload->transaction->transaction_id }}">link (click here)</a> to confirm your transaction and release your payment from escrow.
    <br>
    Your transaction will be marked as complete if you do not take any action within 24 hours of receiving this e-mail.
  </p>
  <p>
    If you have any questions, or require any assistance, call our hotline +234 802 080 9509 or send an email to support@vesicash.com.
    <br><br>
    Thank you for using Vesicash 😊
  </p>
  </p> -->
</div>
@endsection