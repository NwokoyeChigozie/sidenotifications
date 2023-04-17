<!DOCTYPE html>
<html>
<body style="background-color: #222533; padding: 20px; font-family: font-size: 14px; line-height: 1.43; font-family: &quot;Helvetica Neue&quot;, &quot;Segoe UI&quot;, Helvetica, Arial, sans-serif;">
<div style="max-width: 600px; margin: 10px auto 20px; font-size: 12px; color: #A5A5A5; text-align: center;">
    If you are unable to see this message,
    <a href="#" style="color: #A5A5A5; text-decoration: underline;">click here to view in browser</a>
</div>
<div style="max-width: 600px; margin: 0px auto; background-color: #fff; box-shadow: 0px 20px 50px rgba(0,0,0,0.05);">
    <table style="width: 100%;">
        <tr>
            <td style="background-color: #fff;">
                <img alt="Vesicash" src="https://i.ibb.co/qRywKpr/Webp-net-resizeimage.png" width="50px" height="auto">
            </td>
            <td style="padding-left: 50px; text-align: right; padding-right: 20px;">
                <a href="https://vesicash.com/login" style="color: #261D1D; text-decoration: underline; font-size: 14px; letter-spacing: 1px;">Sign In</a>
            </td>
        </tr>
    </table>
    <div style="padding: 20px 0px; border-top: 1px solid rgba(0,0,0,0.05);">
        <h1 style="margin-top: 0px;">Welcome to Vesicash!</h1>
        @if($user->account_type == 'business')
            @if($user->business->business_type == 'social_commerce')
            <div style="color: #636363; font-size: 14px;">
                <p>Dear {{ @$user->business->business_name ?? @$user->email_address }},</p>
                <p>We are thrilled to have you onboard the Trizact platform. This allows you to have fast, seamless and transparent transactions at all times.
                </p><p>
                    Here is your unique payment link <a href="{{ env('SITE_URL') }}/paylink/{{$user->username}}">{{ env('SITE_URL') }}/paylink/{{$user->username}}</a> that can be shared with your customers at all times.
                </p><p>
                We’re happy to have you onboard and look forward to helping you grow your business.
                </p>
            </div>
            @else
            <div style="color: #636363; font-size: 14px;">
                <p>Hi {{ @$user->business->business_name ?? @$user->email_address }}, <br>
                    You are our business partner number {{ @$user->account_id }}.</p>
                <p>Vesicash is an instant digital escrow system that secures payment for everyone involved in a transaction, ensuring the satisfaction and fulfillment of all terms and agreement of a transaction.</p>
                <p>Vesicash ensures that you get paid for your goods and services every time.</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">How it works:</h4>
                <p>You can create an escrow transaction with your customers by entering their details and the transaction detail.</p>
                <p>The transaction is sent to them for review and prompt them to make the payment into the Vesicash Escrow.</p>
                <p>Once the payment is done, you get notified and you can facilitate the delivery of the goods or service you are rendering. <br>
                    After completion of the transaction, the payment is released to you.</p>

            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">What if there is a dispute?</h4>
                <p>For product based transactions,the buyer has a  24 hour inspection window to either confirm or open a dispute and for service based  transactions the transacting parties can agree on an inspection period in their contract.</p>
                <p>When a dispute is opened on an escrow transaction by one person, the other person is notified and the dispute is expected to be resolved within 72hrs.</p>
                <p>If there is a failure to  reach a resolution within 72 hours, the dispute is escalated to our partner arbitrators and the payment is bore by the defaulting party.</p>
                <p>Consequently, we provide an insurance package for high-value transactions, so you can be confident that you are never going to lose out.</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">Settlement</h4>
                <p>You can initiate a withdrawal on your balance at any time and you get settled instantly.</p>
                <p>For pending transactions that have not been concluded, you can only withdraw after the transactions have been completed.</p>
            </div>

            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">Escrow Charge</h4>
                <p>
                    Every escrow payment carries a certain % charge as greed with {{ $payload->business->business_name ?? '[Business Name]' }}.
                </p>
            </div>

            <div style="color: #636363; font-size: 14px;">
                <p>Fill in all additional information about your business as soon as you can, so that we can lift the limitations on your account.</p>
                <p>Thank you for joining Vesicash, we’re excited to have you on board. Let’s grow your business together!</p>
            </div>
            @endif
        @else
            <div style="color: #636363; font-size: 14px;">
                <p>Hi {{$user['firstname']}},</p>
                <p>Now you can pay or get paid with confidence and without the fear of losing your money. With Vesicash, buying and selling online can be a worry-free activity. </p>
                <p>Things to know:</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">How it works:</h4>
                <p> You create an escrow transaction by entering the details of the transaction, the transaction amount and the details of the other person.</p>
                <p>The transaction is sent to the other person for review and approval for the payment to be made into the Vesicash Escrow by the paying party.</p>
                <p>After the transaction has been fulfilled to the satisfaction of all parties involved, the payment is released to the recipient.</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">What if there is a dispute?</h4>
                <p>
                    For <strong> product based transactions</strong>,the buyer has a  24 hour inspection window to either confirm or open a dispute and for <strong>service based  transactions </strong>the transacting parties can agree on an inspection period in their contract .
                </p>
                <p>When a dispute is opened on an escrow transaction by one person, the other person is notified and the dispute is expected to be resolved within 72 hours.</p>
                <p>
                    If there is failure to reach a resolution within 72 hours, the dispute is escalated to our partner arbitrators and the payment is borne by the defaulting party.
                </p>
                <p>Consequently, we provide an insurance package for high-value transactions, so you can be confident that you are never going to lose out.</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">Payment Settlement:</h4>
                <p>You can initiate a withdrawal on your balance at any time and get settled instantly.</p>
                <p>For pending transactions that have not been concluded, you can only withdraw after the transactions have been completed.</p>
            </div>
            <div style="border: 2px solid #3bb75e; padding: 40px; margin: 40px 0px;">
                <h4 style="margin-bottom: 20px; margin-top: 0px; font-size: 18px; display: inline-block; border-bottom: 1px dotted #111;">Charges:</h4>
                <p>Every escrow payment carries a certain % charge, as agreed with {{ $payload->business->business_name ?? '[Business Name]' }}. Please reachout to them to find out.</p>
            </div>

            <div style="color: #A5A5A5; font-size: 12px;">
                <p>Thank you for joining Vesicash, we’re excited to have you on board.</p>
            </div>
        @endif
        <p>Sincerely, <br>The Vesicash Team.</p>
    </div>
    <div style="background-color: #F5F5F5; padding: 40px; text-align: center;">
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
            <div style="color: #A5A5A5; font-size: 10px; margin-bottom: 5px;">16 Alhaji Mudashiru street, Osapa-London, Lekki, Lagos.</div>
            <div style="color: #A5A5A5; font-size: 10px;">© Copyright <?php echo date("Y"); ?>, Vesicash Innovative Technologies. All rights reserved.</div>
        </div>
    </div>
</div>
</body>
</html>
