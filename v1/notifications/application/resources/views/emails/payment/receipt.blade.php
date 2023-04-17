
<!DOCTYPE html>
<html lang="eng">
<head>

<style>
/* -------------------------------------
    GLOBAL
    A very basic CSS reset
------------------------------------- */
* {
    margin: 0;
    padding: 0;
    font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    box-sizing: border-box;
    font-size: 14px;
}

img {
    max-width: 100%;
}

body {
    -webkit-font-smoothing: antialiased;
    -webkit-text-size-adjust: none;
    width: 100% !important;
    height: 100%;
    line-height: 1.6;
}

/* Let's make sure all tables have defaults */
table td {
    vertical-align: top;
}

/* -------------------------------------
    BODY & CONTAINER
------------------------------------- */
body {
    background-color: #f6f6f6;
}

.body-wrap {
    background-color: #f6f6f6;
    width: 100%;
}

.container {
    display: block !important;
    max-width: 600px !important;
    margin: 0 auto !important;
    /* makes it centered */
    clear: both !important;
}

.content {
    max-width: 600px;
    margin: 0 auto;
    display: block;
    padding: 20px;
}

/* -------------------------------------
    HEADER, FOOTER, MAIN
------------------------------------- */
.main {
    background: #fff;
    border: 1px solid #e9e9e9;
    border-radius: 3px;
}

.content-wrap {
    padding: 20px;
}

.content-block {
    padding: 0 0 20px;
}

.header {
    width: 100%;
    margin-bottom: 20px;
}

.footer {
    width: 100%;
    clear: both;
    color: #999;
    padding: 20px;
}
.footer a {
    color: #999;
}
.footer p, .footer a, .footer unsubscribe, .footer td {
    font-size: 12px;
}

/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
    font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    color: #000;
    margin: 40px 0 0;
    line-height: 1.2;
    font-weight: 400;
}

h1 {
    font-size: 32px;
    font-weight: 500;
}

h2 {
    font-size: 24px;
}

h3 {
    font-size: 18px;
}

h4 {
    font-size: 14px;
    font-weight: 600;
}

p, ul, ol {
    margin-bottom: 10px;
    font-weight: normal;
}
.p-desc{
    color: #777;
}
p li, ul li, ol li {
    margin-left: 5px;
    list-style-position: inside;
}

/* -------------------------------------
    LINKS & BUTTONS
------------------------------------- */
a {
    color: #44b669;
    text-decoration: underline;
}

.btn-primary {
    text-decoration: none;
    color: #FFF;
    background-color: #44b669;
    border: solid #44b669;
    border-width: 5px 10px;
    line-height: 2;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 5px;
    text-transform: capitalize;
}

/* -------------------------------------
    OTHER STYLES THAT MIGHT BE USEFUL
------------------------------------- */
.last {
    margin-bottom: 0;
}

.first {
    margin-top: 0;
}

.aligncenter {
    text-align: center;
}

.alignright {
    text-align: right;
}

.alignleft {
    text-align: left;
}

.clear {
    clear: both;
}

/* -------------------------------------
    ALERTS
    Change the class depending on warning email, good email or bad email
------------------------------------- */
.alert {
    font-size: 16px;
    color: #fff;
    font-weight: 500;
    padding: 20px;
    text-align: center;
    border-radius: 3px 3px 0 0;
}
.alert a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
}
.alert.alert-warning {
    background: #f8ac59;
}
.alert.alert-bad {
    background: #ed5565;
}
.alert.alert-good {
    background: #44b669;
}

/* -------------------------------------
    INVOICE
    Styles for the billing table
------------------------------------- */
.invoice {
    margin: 40px auto;
    text-align: left;
    width: 80%;
}
.invoice td {
    padding: 5px 0;
}
.invoice .invoice-items {
    width: 100%;
}
.invoice .invoice-items td {
    border-top: #eee 1px solid;
}
.invoice .invoice-items .total td {
    border-top: 2px solid #333;
    border-bottom: 2px solid #333;
    font-weight: 700;
}
.invoice span{
    text-align: center;
}

/* -------------------------------------
    RESPONSIVE AND MOBILE FRIENDLY STYLES
------------------------------------- */
@media only screen and (max-width: 640px) {
    h1, h2, h3, h4 {
        font-weight: 600 !important;
        margin: 20px 0 5px !important;
    }

    h1 {
        font-size: 22px !important;
    }

    h2 {
        font-size: 18px !important;
    }

    h3 {
        font-size: 16px !important;
    }

    .container {
        width: 100% !important;
    }

    .content, .content-wrap {
        padding: 10px !important;
    }

    .invoice {
        width: 100% !important;
    }
}

</style>

</head>
<body>
	<table class="body-wrap">
	    <tbody>
	    	<tr>
	        <td></td>
	        <td class="container" width="600">
	            <div class="content">
	                <table class="main" width="100%" cellpadding="0" cellspacing="0">
	                    <tbody><tr>
	                        <td class="content-wrap aligncenter">
	                            <table width="100%" cellpadding="0" cellspacing="0">
	                                <tbody>

                                            <tr>
                                                <td class="content-block">
                                                    <h2>Payment Receipt</h2>
                                                    <p class="p-desc">Your	payment	was	successfully	received in	Vesicash Escrow</p>
                                                    @php
                                                    $broker_charge = 0;
                                                    $shipping_charge = 0;

                                                    if(!empty($payload->transaction)):
                                                        if($payload->transaction_type == 'broker' && $payload->transaction->parties->buyer->account_id == $payload->transaction->parties->broker_charge_bearer->account_id) {
                                                            $broker_charge = $payload->broker_charge;
                                                        }

                                                        if($payload->transaction_type == 'broker' && $payload->transaction->parties->buyer->account_id == $payload->transaction->parties->shipping_charge_bearer->account_id) {
                                                            $shipping_charge = $payload->shipping_fee;
                                                        }
                                                    endif;

                                                    @endphp
                                                    <h2>{{ $payload->currency ?? 'NGN' }}  {{ number_format(($payload->escrow_charge + $broker_charge + $shipping_charge + $payload->amount) ?? 0) }}</h2>
                                                </td>
                                            </tr>

	                                <tr>
	                                    <td class="content-block">
	                                        <table class="invoice">
	                                            <tbody>
	                                            <tr>
	                                                <td>
	                                                    <table class="invoice-items" cellpadding="0" cellspacing="0">
	                                                        <tbody>
	                                                        <tr>
	                                                            <td>Amount Paid</td>
	                                                            <td class="alignright">{{ $payload->currency ?? 'NGN' }}  {{ number_format($payload->amount ?? 0) }}</td>
                                                            </tr>
                                                            <tr>
	                                                            <td>Escrow Charge</td>
	                                                            <td class="alignright">{{ $payload->currency ?? 'NGN' }}  {{ number_format($payload->escrow_charge ?? 0) }}</td>
                                                            </tr>

                                                            @if($payload->transaction->source != 'transfer')
                                                            <tr>
	                                                            <td>Shipping Charge</td>
	                                                            <td class="alignright">{{ $payload->currency ?? 'NGN' }}  {{ number_format($payload->shipping_fee ?? 0) }}</td>
                                                            </tr>
                                                            @endif

                                                            @if($payload->transaction_type == 'broker')
                                                            <tr>
	                                                            <td>Broker Charge</td>
	                                                            <td class="alignright">{{ $payload->currency ?? 'NGN' }}  {{ number_format($payload->broker_charge ?? 0) }}</td>
                                                            </tr>
                                                            @endif
	                                                        <tr>
	                                                            <td>Title</td>
	                                                            <td class="alignright">{{ $payload->title ?? '-' }}</td>
	                                                        </tr>
                                                            @if($payload->transaction->source != 'transfer')
	                                                        <tr>
	                                                            <td>Expected delivery</td>
	                                                            <td class="alignright">{{ $payload->expected_delivery ?? '-' }}</td>
                                                            @endif
	                                                        </tr>
                                                            @if($payload->transaction->source != 'transfer')
	                                                        <tr>
	                                                            <td>Inspection Period</td>
	                                                            <td class="alignright">{{ $payload->inspection_period_formatted ?? '-' }}</td>
	                                                        </tr>
                                                            @endif
	                                                        <tr>
	                                                            <td>@if($payload->transaction->source != 'transfer')Buyer @else Sender @endif</td>
	                                                            <td class="alignright">{{ $payload->buyer->email_address ?? '' }}</td>
	                                                        </tr>
	                                                        <tr>
	                                                            <td>@if($payload->transaction->source != 'transfer') Seller @else Recipient @endif</td>
	                                                            <td class="alignright">{{ $payload->seller->email_address ?? '' }}</td>
	                                                        </tr>
	                                                        <tr class="total">
	                                                            <td width="50%">Transaction	Reference</td>
	                                                            <td class="alignright" width="50%">{{ $payload->transaction_id }}</td>
	                                                        </tr>
	                                                    </tbody>
	                                                </table>
	                                                </td>
	                                            </tr>
	                                        </tbody>
	                                    </table>
	                                    </td>
	                                </tr>
	                                {{-- <tr>
	                                    <td class="content-block">
	                                        <a href="{{ $payload->pdf_link ?? '' }}">Download PDF</a> or <a href="#">Share PDF</a>
	                                    </td>
	                                </tr> --}}
	                                <tr>
	                                    <td class="content-block">
	                                        <img class="logo-img" src="https://i.ibb.co/qRywKpr/Webp-net-resizeimage.png" alt="vesicash" width="30%">
	                                    </td>
	                                </tr>
	                            </tbody></table>
	                        </td>
	                    </tr>
	                </tbody></table>
	                <div class="footer">
	                    <table width="100%">
	                        <tbody><tr>
	                            <td class="aligncenter content-block">Questions? Email <a href="mailto:">support@vesicash.com</a></td>
	                        </tr>
	                    </tbody></table>
	                </div></div>
	        </td>
	        <td></td>
	    </tr>
	</tbody></table>
</body>
</html>
