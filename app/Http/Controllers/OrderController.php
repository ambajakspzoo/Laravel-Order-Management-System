<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->with('customer')->withCount('items')->latest();

        if ($status = $request->query('status')) {
            $enum = OrderStatus::tryFrom((string) $status);
            if ($enum) {
                $query->where('status', $enum->value);
            }
        }

        return view('orders.index', [
            'orders' => $query->limit(100)->get(),
            'statuses' => OrderStatus::cases(),
            'currentStatus' => $request->query('status'),
        ]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = $orderService->createOrder((int) $data['customer_id'], $data['notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('orders.show', $order)
            ->with('success', sprintf('Order %s created.', $order->order_number));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product']);

        return view('orders.show', [
            'order' => $order,
            'products' => Product::query()->where('active', true)->orderBy('name')->get(),
            'statuses' => $order->status->allowedTransitions(),
        ]);
    }

    public function addItem(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $orderService->addItem($order, (int) $data['product_id'], (int) $data['quantity']);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Item added to order.');
    }

    public function submit(Order $order, OrderService $orderService): RedirectResponse
    {
        try {
            $orderService->submitOrder($order);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Order submitted for processing.');
    }

    public function updateStatus(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $status = OrderStatus::tryFrom((string) $request->input('status'));
        if (!$status) {
            return back()->with('error', 'Invalid status.');
        }

        try {
            $orderService->updateStatus($order, $status);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', sprintf('Order status updated to %s.', $status->label()));
    }
}
