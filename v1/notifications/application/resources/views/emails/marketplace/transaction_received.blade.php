@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p style="margin-top: 0px;">Hi {{ $payload->recipient->firstname ?? $payload->recipient->email_address}}</p>
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
                    <th scope="col">Deadline</th>
                    <th scope="col">Inspection</th>
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

        <p>You can <a href="{{ $payload->business->website ?? env('SITE_URL') . '/login?account-id=' . $payload->recipient->account_id }}">login here</a> to accept the terms
        @if($payload->recipient->email_address == $payload->buyer->email_address) and make payment @endif if it meets your initial agreement.</p>

        <p>
            If you have any question or request, please send an e-mail to support@vesicash.com and we will respond promptly.
        </p>
</div>
@endsection
