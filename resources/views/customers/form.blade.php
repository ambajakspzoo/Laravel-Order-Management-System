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
        <form method="post" action="{{ route('customers.store') }}" class="form-grid">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
            </div>
            <div class="form-group">
                <label for="address_line1">Address Line 1</label>
                <input type="text" id="address_line1" name="address_line1" value="{{ old('address_line1', $customer->address_line1) }}">
            </div>
            <div class="form-group">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2', $customer->address_line2) }}">
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}">
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}">
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" value="{{ old('country', $customer->country) }}">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
