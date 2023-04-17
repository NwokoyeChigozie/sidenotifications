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
                <div style="color: #636363; font-size: 14px;">
                    <p style="margin-top: 0px;">Dear {{ $payload->seller->firstname ?? $payload->seller->email_address }},</p>
                    @if ($payload->transaction->type == 'product')
                    <p>
                        Your money is on its way! Your client <Strong>{{ $payload->buyer->firstname ?? $payload->buyer->email_address }}</Strong> has received the goods and has also confirmed that the funds be transferred from the escrow account into yours. You can always keep track of the transaction via <a href="{{ $payload->business->website ?? env('SITE_URL') }}">your dashboard</a>.
                    </p>
                    @else
                    <p>
                        Your money is on its way! Your client <Strong>{{ $payload->buyer->firstname ?? $payload->buyer->email_address }}</Strong> has confirmed that the funds be transferred from the escrow account into yours. You can always keep track of the transaction via <a href="{{ $payload->business->website ?? env('SITE_URL') }}">your dashboard</a>.
                    </p>
                    @endif
                    <p>
                        If you need further help, check our <a href="https://vesicash.com/faq">FAQ page </a> or send an email to <a href="mailto:support@vesicash.com">support@vesicash.com</a>.
                    </p>
                </div>
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
