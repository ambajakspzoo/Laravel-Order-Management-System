@extends('layouts.app')

@section('title', 'Order '.$order->order_number)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $order->order_number }}</h1>
        <p>{{ $order->customer->name }} · <span class="badge badge-{{ $order->status->value }}">{{ $order->status->label() }}</span></p>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">← Back to Orders</a>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Line Items</div>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                <tr>
                    <td>{{ $item->product->sku }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">${{ $item->unit_price }}</td>
                    <td class="text-right">${{ $item->subtotal }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted">No items yet.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal</strong></td>
                    <td class="text-right">${{ $order->subtotal }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Tax</td>
                    <td class="text-right">${{ $order->tax_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">Shipping</td>
                    <td class="text-right">${{ $order->shipping_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total</strong></td>
                    <td class="text-right"><strong>${{ $order->total_amount }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div>
        @if ($order->isEditable())
        <div class="card">
            <div class="card-header">Add Item</div>
            <div class="card-body">
                <form method="post" action="{{ route('orders.add-item', $order) }}" class="form-grid">
                    @csrf
                    <div class="form-group">
                        <label for="product_id">Product</label>
                        <select id="product_id" name="product_id" required>
                            <option value="">Select product...</option>
                            @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }}) — ${{ $product->price }} · stock: {{ $product->stock_quantity }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Submit Order</div>
            <div class="card-body">
                <p class="text-muted">Submit the order when all items are added. Status will change to Pending.</p>
                <form method="post" action="{{ route('orders.submit', $order) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" @disabled($order->items->isEmpty())>Submit Order</button>
                </form>
            </div>
        </div>
        @endif

        @if (!empty($statuses))
        <div class="card">
            <div class="card-header">Update Status</div>
            <div class="card-body">
                <form method="post" action="{{ route('orders.update-status', $order) }}" class="inline-form">
                    @csrf
                    @foreach ($statuses as $status)
                    <button type="submit" name="status" value="{{ $status->value }}" class="btn btn-sm btn-secondary">{{ $status->label() }}</button>
                    @endforeach
                </form>
            </div>
        </div>
        @endif

        @if ($order->notes)
        <div class="card">
            <div class="card-header">Notes</div>
            <div class="card-body">{{ $order->notes }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
