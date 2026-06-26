<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\EntityNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly EntityNormalizer $normalizer)
    {
    }

    public function index(): JsonResponse
    {
        $products = Product::query()->orderBy('name')->get();

        return $this->jsonOk($products->map(fn (Product $p) => $this->normalizer->product($p))->values());
    }

    public function show(Product $product): JsonResponse
    {
        return $this->jsonOk($this->normalizer->product($product));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stockQuantity' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        $product = Product::query()->create([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock_quantity' => $data['stockQuantity'] ?? 0,
            'active' => $data['active'] ?? true,
        ]);

        return $this->jsonOk($this->normalizer->product($product), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'sku' => ['sometimes', 'string', 'max:64', 'unique:products,sku,'.$product->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stockQuantity' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        $product->update(array_filter([
            'sku' => $data['sku'] ?? null,
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'stock_quantity' => $data['stockQuantity'] ?? null,
            'active' => $data['active'] ?? null,
        ], fn ($v) => $v !== null));

        return $this->jsonOk($this->normalizer->product($product->fresh()));
    }
}
