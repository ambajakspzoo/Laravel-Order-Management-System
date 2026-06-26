@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $title }}</h1>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('products.store') }}" class="form-grid">
            @csrf
            <div class="form-group">
                <label for="sku">SKU</label>
                <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="active" value="1" @checked(old('active', $product->active))>
                    Active
                </label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
