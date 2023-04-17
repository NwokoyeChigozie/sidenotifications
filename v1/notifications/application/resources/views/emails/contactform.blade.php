@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Dear Admin, </p>
    <p>A vesicash website visitor has sent you an email. See their details below:</p>

    <p><strong>Name: </strong>{{ $payload->firstname}} {{$payload->lastname}}</p>
    <p><strong>E-mail:  </strong>{{ $payload->email}}</p>
    <p><strong>Business Type: </strong>{{ $payload->business_type }}</p>
    <p><strong>Country: </strong>{{ $payload->country }}</p>
    <p><strong>Website: </strong>{{ $payload->website_url }}</p>
    <p><strong>Message: </strong>{{ $payload->firstname }}</p>
</div>
@endsection