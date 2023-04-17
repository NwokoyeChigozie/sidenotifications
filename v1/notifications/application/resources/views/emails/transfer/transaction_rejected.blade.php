@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>
        Unfortunately, your funds transfer request ({{ $payload->transaction->transaction_id ?? '' }}) was not accepted by {{ $payload->recipient->email_address ?? '' }}, Please discuss with them to make sure the terms of the payment terms are correct. To create another payment with updated terms, visit <a href="https://transfer.vesicash.com">https://transfer.vesicash.com</a>
    </p>

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
                    <td>{{ $payload->transaction->currency ?? '' }} {{ number_format($payload->transaction->amount) }}</td>
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
    <p>
        If you haven’t, kindly provide your bank account information so that the transaction funds can be refunded to you. Thank you.
    </p>
    <p>
        You can view and manage your transaction by visiting your <a href="{{ env('SITE_URL') }}/login?customer-phone={{ $payload->sender->phone_number ?? '' }}&customer-email={{ $payload->sender->email_address ?? '' }}">dashboard</a>

<br>
If you have any questions, or require any assistance, call our hotline +234 802 080 9509 or send an email to support@vesicash.com.
<br>
Thank you for using Vesicash 😊

    </p>

</div>
@endsection
