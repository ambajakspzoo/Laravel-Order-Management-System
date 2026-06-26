<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Support\EntityNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly EntityNormalizer $normalizer)
    {
    }

    public function index(): JsonResponse
    {
        $customers = Customer::query()->orderBy('name')->get();

        return $this->jsonOk($customers->map(fn (Customer $c) => $this->normalizer->customer($c))->values());
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->jsonOk($this->normalizer->customer($customer, true));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address.line1' => ['nullable', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:100'],
            'address.postalCode' => ['nullable', 'string', 'max:20'],
            'address.country' => ['nullable', 'string', 'max:100'],
        ]);

        $customer = Customer::query()->create($this->mapCustomerData($data));
        return $this->jsonOk($this->normalizer->customer($customer), 201);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:customers,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'address.line1' => ['nullable', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:100'],
            'address.postalCode' => ['nullable', 'string', 'max:20'],
            'address.country' => ['nullable', 'string', 'max:100'],
        ]);

        $customer->update($this->mapCustomerData($data));

        return $this->jsonOk($this->normalizer->customer($customer->fresh()));
    }

    /** @param array<string, mixed> $data */
    private function mapCustomerData(array $data): array
    {
        $address = $data['address'] ?? [];

        return array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address_line1' => $address['line1'] ?? null,
            'address_line2' => $address['line2'] ?? null,
            'city' => $address['city'] ?? null,
            'postal_code' => $address['postalCode'] ?? null,
            'country' => $address['country'] ?? null,
        ], fn ($v) => $v !== null);
    }
}
