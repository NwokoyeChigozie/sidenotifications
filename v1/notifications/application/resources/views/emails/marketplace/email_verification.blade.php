@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
    	Hi {{ $user->email_address }},
    <p>
		Thanks for signing up for {{ $payload->business->business_name }}. We are happy to have you here. Let us get you started by verifying your email address. 
	</p>
  <a href="{{ env('SITE_URL') }}/email-verify/{{ $user->account_id }}/{{ $token }}" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Activate my account</a>
</p>
</div>
@endsection