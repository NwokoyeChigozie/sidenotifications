<!DOCTYPE html>
<html>
    <body style="background-color: #222533; padding: 20px; font-family: font-size: 14px; line-height: 1.43; font-family: &quot;Helvetica Neue&quot;, &quot;Segoe UI&quot;, Helvetica, Arial, sans-serif;">
        <div style="max-width: 600px; margin: 10px auto 20px; font-size: 12px; color: #A5A5A5; text-align: center;">If you are unable to see this message,
            <a href="#" style="color: #A5A5A5; text-decoration: underline;">click here to view in browser</a>
        </div>
        <div style="max-width: 600px; margin: 0px auto; background-color: #fff; box-shadow: 0px 20px 50px rgba(0,0,0,0.05);">
            <table style="width: 100%;">
                <tr>
                    <td style="background-color: #fff;">
                         <img alt="Vesicash" src="{{ $payload->business->logo_uri ?? 'https://i.ibb.co/qRywKpr/Webp-net-resizeimage.png' }}">
                    </td>
                    <td style="padding-left: 50px; text-align: right; padding-right: 20px;">
                        <a href="https://vesicash.com/login" style="color: #261D1D; text-decoration: underline; font-size: 14px; letter-spacing: 1px;">Sign In</a>
                    </td>
                </tr>
            </table>
            <div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
                <h4 style="margin-top: 0px;">Hello {{ $payload->recipient->firstname ?? $payload->recipient->email_address }}</h4>
                <div style="color: #636363; font-size: 14px;">
                    <p>
                        You have just received a payment from {{ $payload->sender->firstname ?? $payload->sender->email_address }}.
                    </p>
                    <p>Transaction details</p>
                    <table class="table table-bordered" style="width:100%">
                        <tr>
                          <th>Title:</th>
                          <td>{{ $payload->transaction->title ?? '' }}</td>
                        </tr>
                        <tr>
                          <th>Date:</th>
                          <td>{{ $payload->transaction->created_at ?? '' }}</td>
                        </tr>
                        <tr>
                          <th>Description:</th>
                          <td>{{ $payload->transaction->description ?? '' }}</td>
                        </tr>
                        <tr>
                            <th>Amount:</th>
                            <td>{{ $payload->transaction->currency }} {{ $payload->transaction->amount ?? '' }}</td>
                          </tr>
                          <tr>
                            <th>Paid By:</th>
                            <td>{{ $payload->buyer->email_address ?? '' }}</td>
                          </tr>
                          <tr>
                            <th>Recipient's Phone Number:</th>
                            <td>{{ $payload->recipient->phone_number ?? '' }}</td>
                          </tr>
                          <tr>
                            <th>Recipient's Email:</th>
                            <td>{{ $payload->recipient->email_address ?? '' }}</td>
                          </tr>

                      </table>

                    <br>
                    <p>
                        Please review and accept the payment as soon as possible. To accept the transaction, Log in at https://vesicash.com/login with your phone number and email address as shown above and provide your Bank details to receive the funds.
                    </p>

                    <div>
                        <div>
                            <a style="background: green; padding: 10px; color: white; text-align: center; text-decoration: none; display:block; padding-bottom: 5px; margin-bottom: 5px;" href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->recipient->phone_number ?? '' }}&customer-email={{ $payload->recipient->email_address ?? '' }}">Accept Payment</a>

                            <a style="background: red; padding: 10px; color: white; text-align: center; text-decoration: none; display:block;" href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->recipient->phone_number ?? '' }}&customer-email={{ $payload->recipient->email_address ?? '' }}">Reject Payment</a>
                        </div>
                    </div>
                </div>
                <p>
                    If you have any question or request, please send an e-mail to support@vesicash.com and we will respond promptly.
                </p>
                <a href="{{ $payload->business->website ?? env('SITE_URL') }}" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">View Transaction</a>
            </div>
            <div style="background-color: #F5F5F5; padding: 40px; text-align: center;">
                <div style="color: #A5A5A5; font-size: 12px; margin-bottom: 20px; padding: 0px 50px;">
                    You are receiving this email because you signed up for Vesicash Escrow Serivices
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
                    <div style="color: #A5A5A5; font-size: 10px; margin-bottom: 5px;">16 Alhaji Mudashiru street, Osapa-London, Lekki, Lagos.</div>
                    <div style="color: #A5A5A5; font-size: 10px;">© Copyright <?php echo date("Y"); ?>, Vesicash Innovative Technologies. All rights reserved.</div>
                </div>
            </div>
        </div>
    </body>
</html>
