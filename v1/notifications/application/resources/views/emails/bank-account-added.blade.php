@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Your Bank Details have been added successfully; below are the details for your review;</p>
    <p>Bank: {{ $payload->bank }}</p>
    <p>Account Name: {{ $payload->account_name }}</p>
    <p>Account Number: {{ $payload->account_number }}</p>
    <p>Currency: {{ $payload->currency }}</p>
    <p>Do contact us if you have nay questions or issues with this transaction. Thank you,</p>
    <br>
    <p>Best,</p>
    <p>The Vesicash Team</p>
</div>
@endsection