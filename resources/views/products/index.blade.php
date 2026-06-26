@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="page-header">
    <div>
        <h1>Products</h1>
        <p>Product catalog and inventory</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">+ New Product</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th class="text-right">Price</th>
                <th class="text-right">Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td><code>{{ $product->sku }}</code></td>
                <td>{{ $product->name }}</td>
                <td class="text-right">${{ $product->price }}</td>
                <td class="text-right">{{ $product->stock_quantity }}</td>
                <td>
                    @if ($product->active)
                        <span class="badge badge-delivered">Active</span>
                    @else
                        <span class="badge badge-cancelled">Inactive</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-muted">No products yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
