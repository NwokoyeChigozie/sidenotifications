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

                <p>Hello, {{ $payload->sender->firstname ?? $payload->sender->email_address }}, </p>

                <p>Unfortunately, your transaction ({{ $payload->transaction->transaction_id }}) was not accepted. Please discuss with them to make sure the terms of the transaction are correct. To create another transaction with updated terms, visit <a href="https://vesicash.com">https://vesicash.com</a></p>

                @include('emails.transactions.transaction_table')

                <p>If you haven’t, kindly provide your <b>Bank account information</b> so that the transaction funds can be refunded to you. Thank you</p>

                <p>If you have any questions, or require any assistance, call our hotline +234 802 080 9509 or send an email to support@vesicash.com.</p>

                <p>Thank you for using Vesicash 😊</p>

            </div>
            <!-- <a href="https://vesicash.com/login" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">View Transaction</a> -->
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