@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="page-header">
    <div>
        <h1>New Order</h1>
        <p>Create a draft order for a customer</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="{{ route('orders.store') }}" class="form-grid">
            @csrf
            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select id="customer_id" name="customer_id" required>
                    <option value="">Select customer...</option>
                    @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }} ({{ $customer->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        @if ($customers->isEmpty())
        <p class="text-muted mt-1">No customers yet. <a href="{{ route('customers.create') }}">Add a customer first</a>.</p>
        @endif
    </div>
</div>
@endsection
