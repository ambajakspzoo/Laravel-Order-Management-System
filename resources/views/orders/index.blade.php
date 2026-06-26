@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="page-header">
    <div>
        <h1>Orders</h1>
        <p>Manage and track all customer orders</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">+ New Order</a>
</div>

<div class="card">
    <div class="card-body inline-form">
        <span class="text-muted">Filter:</span>
        <a href="{{ route('orders.index') }}" class="btn btn-sm {{ empty($currentStatus) ? 'btn-primary' : 'btn-secondary' }}">All</a>
        @foreach ($statuses as $status)
        <a href="{{ route('orders.index', ['status' => $status->value]) }}" class="btn btn-sm {{ $currentStatus === $status->value ? 'btn-primary' : 'btn-secondary' }}">{{ $status->label() }}</a>
        @endforeach
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th class="text-right">Items</th>
                <th class="text-right">Total</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td><a href="{{ route('orders.show', $order) }}" style="color: var(--accent);">{{ $order->order_number }}</a></td>
                <td>{{ $order->customer->name }}</td>
                <td><span class="badge badge-{{ $order->status->value }}">{{ $order->status->label() }}</span></td>
                <td class="text-right">{{ $order->items_count }}</td>
                <td class="text-right">${{ $order->total_amount }}</td>
                <td class="text-muted">{{ $order->updated_at->format('M j, Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-muted">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
