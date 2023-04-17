@extends('emails.default')
@section('contents')
<div style="color: #636363; font-size: 14px;">
    <p>Hi {{ $payload->sender->firstname ?? $payload->sender->email_address }}, </p>
    <p>An Escrow Transaction ({{ $payload->transaction->transaction_id ?? '' }}), has been created successfully. You will be notified when any action is taken. Visit your Dashboard to manage all your transactions.
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
    <p><a href="{{ $links->dashboard }}" target="_blank">Visit your Dashboard</a></p>
    <p>Sincerely,</p>
    <p>The Vesicash Team</p>

</div>
@endsection