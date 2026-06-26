@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="page-header">
    <div>
        <h1>Customers</h1>
        <p>Manage customer records</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">+ New Customer</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Location</th>
                <th>Since</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?? '—' }}</td>
                <td>{{ $customer->city ?? '—' }}@if($customer->country), {{ $customer->country }}@endif</td>
                <td class="text-muted">{{ $customer->created_at->format('M j, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-muted">No customers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
