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
                <table class="table table-bordered" style="width:100%">
                    <tr>
                      <th>Bank:</th>
                      <td>{{ $payload->seller_bank_details->bankName() ?? '' }}</td>
                    </tr>
                    <tr>
                      <th>Account Number:</th>
                      <td>{{ $payload->seller_bank_details->account_no ?? '' }}</td>
                    </tr>
                    <tr>
                      <th>Account Name:</th>
                      <td>{{ $payload->seller_bank_details->account_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Amount:</th>
                        <td>{{ $payload->transaction->amount ?? '' }}</td>
                      </tr>
                      <tr>
                        <th>Time:</th>
                        <td>{{ date('Y-m-d H:i:s', time()) ?? '' }}</td>
                      </tr>


                  </table>
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
