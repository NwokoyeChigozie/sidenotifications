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
        @endif
        </tbody>
    </table>