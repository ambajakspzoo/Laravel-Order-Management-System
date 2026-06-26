@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Overview of your order management system</p>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">+ New Order</a>
</div>

<div class="grid-stats">
    <div class="stat-card">
        <div class="label">Total Orders</div>
        <div class="value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Customers</div>
        <div class="value">{{ $totalCustomers }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Active Products</div>
        <div class="value">{{ $totalProducts }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Pending Orders</div>
        <div class="value">{{ $statusCounts['pending'] ?? 0 }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Orders by Status</div>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th class="text-right">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statuses as $status)
                <tr>
                    <td><span class="badge badge-{{ $status->value }}">{{ $status->label() }}</span></td>
                    <td class="text-right">{{ $statusCounts[$status->value] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">Low Stock Alert</div>
        @if ($lowStock->isEmpty())
            <div class="card-body text-muted">All products are well stocked.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product</th>
                    <th class="text-right">Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lowStock as $product)
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-right">{{ $product->stock_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Orders</div>
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Status</th>
                <th class="text-right">Total</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentOrders as $order)
            <tr>
                <td><a href="{{ route('orders.show', $order) }}" style="color: var(--accent);">{{ $order->order_number }}</a></td>
                <td>{{ $order->customer->name }}</td>
                <td><span class="badge badge-{{ $order->status->value }}">{{ $order->status->label() }}</span></td>
                <td class="text-right">${{ $order->total_amount }}</td>
                <td class="text-muted">{{ $order->created_at->format('M j, Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-muted">No orders yet. <a href="{{ route('orders.create') }}">Create one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
