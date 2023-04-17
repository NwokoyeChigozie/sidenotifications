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
        <h1 style="margin-top: 0px;">Hi {{ $user->email_address }}</h1>
        <div style="color: #636363; font-size: 14px;">
            It appears that you have forgotten to complete your registration on our platform by verifying your phone number. Not to worry, you can still do that now by clicking this link <a href="https://vesicash.com">https://vesicash.com</a>, you can also copy and paste the link directly into your browser if you have issues clicking the button.

            We are always here to help you out, contact us on support@vesicash.com or visit our FAQ page <a href="https://vesicash.com/faq">https://vesicash.com/faq</a>.

        </div>
        <!-- <a href="https://vesicash.com/login" style="padding: 8px 20px; background-color: #3BB75E; color: #fff; font-weight: bolder; font-size: 16px; display: inline-block; margin: 20px 0px; margin-right: 20px; text-decoration: none;">Request for verification code.</a> -->
    </div>
    <div style="background-color: #F5F5F5; padding: 40px; text-align: center;">
        <!-- <div style="margin-bottom: 20px;"><a href="#" style="display: inline-block; margin: 0px 10px;"><img alt="" src="img/social-icons/twitter.png" style="width: 28px;"></a><a href="#" style="display: inline-block; margin: 0px 10px;"><img alt="" src="img/social-icons/facebook.png" style="width: 28px;"></a><a href="#" style="display: inline-block; margin: 0px 10px;"><img alt="" src="img/social-icons/linkedin.png" style="width: 28px;"></a><a href="#" style="display: inline-block; margin: 0px 10px;"><img alt="" src="img/social-icons/instagram.png" style="width: 28px;"></a>
        </div> -->
        <!-- <div style="margin-bottom: 20px;">
            <a href="#" style="text-decoration: underline; font-size: 14px; letter-spacing: 1px; margin: 0px 15px; color: #261D1D;">Contact Us</a>
            <a href="#" style="text-decoration: underline; font-size: 14px; letter-spacing: 1px; margin: 0px 15px; color: #261D1D;">Privacy Policy</a>
            <a href="#" style="text-decoration: underline; font-size: 14px; letter-spacing: 1px; margin: 0px 15px; color: #261D1D;">Unsubscribe</a>
        </div> -->
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
