@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
        Hi {{ $payload->seller->firstname ?? $payload->seller->email_address }},
        <br><br>
        You have just received a payment via your payment link. Please proceed to process the delivery as agreed.
        <br><br>
        <table class="main" width="100%" cellpadding="0" cellspacing="0">
            <tbody><tr>
                <td class="content-wrap aligncenter">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tbody>


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
                                                    <td class="alignright">{{ $payloads->currency ?? 'NGN' }}  {{ number_format($payloads->amount ?? 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Escrow Charge</td>
                                                    <td class="alignright">{{ $payloads->currency ?? 'NGN' }}  {{ number_format($payloads->escrow_charge ?? 0) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Title</td>
                                                    <td class="alignright">{{ $payloads->title ?? '-' }}</td>
                                                </tr>
                                                @if($payload->transaction->source != 'transfer')
                                                <tr>
                                                    <td>Expected delivery</td>
                                                    <td class="alignright">{{ date('Y-m-d', $payloads->expected_delivery) ?? '-' }}</td>
                                                </tr>
                                                @endif
                                                @if($payload->transaction->source != 'transfer')
                                                <tr>
                                                    <td>Inspection Period</td>
                                                    <td class="alignright">{{ date('Y-m-d', $payloads->inspection_period) ?? '-' }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td>Buyer</td>
                                                    <td class="alignright">{{ $payloads->buyer->email_address ?? '' }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Seller</td>
                                                    <td class="alignright">{{ $payloads->seller->email_address ?? '' }}</td>
                                                </tr>
                                                <tr class="total">
                                                    <td width="50%">Transaction	Reference:</td>
                                                    <td class="alignright" width="50%">{{ $payloads->transaction_id }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

        <br><br>
        <a href="{{ env('SITE_URL') . '/login?account-id=' . $payload->seller->account_id }}" style="background: green; color: #fff; text-align: center; padding: 10px; margin-top: 5px;">My Transactions</a>
    </p>
</div>
@endsection
