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
                <h4 style="margin-top: 0px;">Hi {{ $payload->buyer->firstname ?? $payload->buyer->email_address }}</h4>
                <div style="color: #636363; font-size: 14px;">
                    <p>
                        You have successfully paid for "{{ $payload->transaction->title ?? $payload->transaction->transaction_id }}" from {{ $payload->seller->firstname ?? $payload->seller->email_address }}.
                    </p>
                    <table class="table table-bordered">
                    @if ($payload->transaction->type == 'product')
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Shipping Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payload->transaction->products as $product)
                            <tr>
                                <td>{{ $product->title ?? '' }}</td>
                                <td>{{ $product->quantity ?? '' }}</td>
                                <td>{{ $payload->transaction->currency ?? '' }} {{ $product->amount ?? '' }}</td>
                                <td>{{ $payload->transaction->currency ?? '' }} {{ $payload->transaction->shipping_fee ?? ''}}</td>
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
                                <td>{{ $payload->transaction->currency ?? '' ?? '' }} {{ number_format($payload->transaction->amount) }}</td>
                                <td>{{ $payload->transaction->status ?? '' }}</td>
                            </tr>
                        </tbody>
                        @endif

                        @if ($payload->transaction->type == 'broker')
                        <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Description</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Broker Charge</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $payload->transaction->title ?? '' }}</td>
                                <td>{{ $payload->transaction->description ?? '' }}</td>
                                <td>{{ $payload->transaction->currency ?? '' ?? '' }} {{ number_format($payload->transaction->amount) }}</td>
                                <td>{{ $payload->transaction->currency ?? '' ?? '' }} {{ number_format((int)$payload->transaction->broker->broker_charge) }}</td>
                                <td>{{ $payload->transaction->status ?? '' }}</td>
                            </tr>
                        </tbody>
                        @endif

                        @if ($payload->transaction->type == 'milestone')
                        <thead>
                            <tr>
                                <th scope="col">Task</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Deadline</th>
                                <th scope="col">Inspection End</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($payload->transaction->milestones as $milestone)
                            <tr>
                                <td scope="col">{{ $milestone->title ?? '' }}</td>
                                <td scope="col">{{ $milestone->currency ?? '' }} {{ $milestone->amount ?? '' }}</td>
                                <td scope="col">
                                {{ Carbon\Carbon::createFromTimestamp($milestone->due_date)->toDateTimeString() ?? '' }}
                                </td>
                                <td scope="col">
                                {{ Carbon\Carbon::createFromTimestamp($milestone->inspection_period)->toDateTimeString() ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        @endif
                    </table>

                    <br>
                    <p>
                        If you have any question or request, please send an e-mail to support@vesicash.com and we will respond promptly.
                    </p>
                </div>
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
