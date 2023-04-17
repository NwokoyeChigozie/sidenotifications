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

                    <p style="margin-top: 0px;">Hello, {{ $payload->buyer->firstname ?? $payload->buyer->email_address}}</p>
                    <p style="margin-top: 0px;"> {{ $payload->sender->firstname ?? $payload->sender->email_address}} has sent you a new transaction "{{ $payload->transaction->title ?? $payload->transaction->transaction_id }}"</p>
                    <p>Here is the full details:</p>
                    <table class="table table-bordered">

                        @if ($payload->transaction->type == 'product')
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payload->transaction->products as $product)
                            <tr>
                                <td scope="col">{{ $product->title ?? '' }}</td>
                                <td scope="col">{{ $product->quantity ?? '' }}</td>
                                <td scope="col">{{ $payload->transaction->currency ?? '' }} {{ $product->amount ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endif

                        @if ($payload->transaction->type == 'oneoff')
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payload->transaction->title ?? '' }}</td>
                                <td>{{ $payload->transaction->description ?? '' }}</td>
                                <td>{{ $payload->transaction->currency ?? '' ?? '' }} {{ number_format($payload->transaction->amount ?? 0) }}</td>
                                <td>{{ $payload->transaction->status ?? '' }}</td>
                            </tr>
                        </tbody>
                        @endif


                        @if ($payload->transaction->type == 'milestone')
                            <thead>
                                <tr>
                                    <th scope="col">Task</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Inspection Days</th>
                                    <th scope="col">Deadline</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($payload->transaction->milestones as $milestone)
                                <tr>
                                    <td scope="col">{{ $milestone->title ?? '' }}</td>
                                    <td scope="col">{{ $milestone->currency ?? '' }} {{ $milestone->amount ?? '' }}</td>
                                    <td scope="col">
                                    {{ Carbon\Carbon::createFromTimestamp($milestone->inspection_period)->toDateTimeString() ?? '' }}
                                    </td>
                                    <td scope="col">
                                    {{ Carbon\Carbon::createFromTimestamp($milestone->due_date)->toDateTimeString() ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>sss
                        @endif

                        </table>

                    <p>
                        Please review and accept the transaction as soon as possible. To accept the transaction, Log in at <a href="{{ env('SITE_URL') . '/login?account-id=' . $payload->recipient->account_id }}">{{ env('SITE_URL') }}</a> with your phone number and email address as shown above.
<br>
<div>
<a style="background: green; padding: 10px; color: white; text-align: center; text-decoration: none; display:block;" href="{{ env('SITE_URL') . '/login?account-id=' . $payload->recipient->account_id }}">Accept Escrow Transaction</a>
&nbsp;
<a style="background: red; padding: 10px; color: white; text-align: center; text-decoration: none; display:block;" href="{{ env('SITE_URL') . '/login?account-id=' . $payload->recipient->account_id }}">Reject this transaction</a>
</div>
<br>
For questions or inquiries, please call our hotline +234 802 080 9509 or visit our FAQ Page <a href="https://vesicash.com/faq">https://vesicash.com/faq</a>
<br>
Thank you for using Vesicash 😊

                    </p>
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
