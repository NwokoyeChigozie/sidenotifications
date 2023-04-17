@extends('emails.default')
@section('contents')
    <div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
        <h1 style="margin-top: 0px;">Dear {{ $user->email_address }}</h1>
        <div style="color: #636363; font-size: 14px;">

            <p>We are glad you’ve come this far. However, you’re just a couple of steps away from being a verified customer on Vesicash.
                Click the button below to complete your CAC Reg Verification Process.
                <br>
                <a href="{{ env('SITE_URL') }}/dashboard" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Verify My Business</a>

            </p>

            If you have any questions, be sure to check our <a href='https://vesicash.com/faq'>FAQ page </a> or send an email to support@vesicash.com
        </div>
    </div>
@endsection
