<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'products' => Product::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(['active' => true, 'stock_quantity' => 0]),
            'title' => 'New Product',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:64', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        Product::query()->create([
            ...$data,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }
}
