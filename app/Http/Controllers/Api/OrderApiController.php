<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\EntityNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    use ApiResponses;

    public function __construct(
        private readonly OrderService $orderService,
        private readonly EntityNormalizer $normalizer,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Order::query()->with('customer')->latest();

        if ($status = $request->query('status')) {
            $enum = OrderStatus::tryFrom((string) $status);
            if (!$enum) {
                return $this->jsonError('Invalid status filter.');
            }
            $query->where('status', $enum->value);
        }

        $orders = $query->limit(100)->get();

        return $this->jsonOk($orders->map(fn (Order $o) => $this->normalizer->order($o, false))->values());
    }

    public function show(Order $order): JsonResponse
    {
        return $this->jsonOk($this->normalizer->order($order));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customerId' => ['required', 'integer', 'exists:customers,id'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = $this->orderService->createOrder($data['customerId'], $data['notes'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 404);
        }

        return $this->jsonOk($this->normalizer->order($order), 201);
    }

    public function addItem(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'productId' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->orderService->addItem($order, $data['productId'], $data['quantity']);
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 409);
        }

        return $this->jsonOk($this->normalizer->order($order->fresh()));
    }

    public function removeItem(Order $order, int $itemId): JsonResponse
    {
        try {
            $this->orderService->removeItem($order, $itemId);
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError($e->getMessage(), 404);
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 409);
        }

        return $this->jsonOk($this->normalizer->order($order->fresh()));
    }

    public function submit(Order $order): JsonResponse
    {
        try {
            $this->orderService->submitOrder($order);
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 409);
        }

        return $this->jsonOk($this->normalizer->order($order->fresh()));
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $status = OrderStatus::tryFrom($data['status']);
        if (!$status) {
            return $this->jsonError('Invalid status value.');
        }

        try {
            $this->orderService->updateStatus($order, $status);
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 409);
        }

        return $this->jsonOk($this->normalizer->order($order->fresh()));
    }

    public function updatePricing(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'shippingAmount' => ['nullable', 'numeric', 'min:0'],
            'taxAmount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $this->orderService->setShippingAndTax(
                $order,
                (string) ($data['shippingAmount'] ?? $order->shipping_amount),
                (string) ($data['taxAmount'] ?? $order->tax_amount),
            );
        } catch (\DomainException $e) {
            return $this->jsonError($e->getMessage(), 409);
        }

        return $this->jsonOk($this->normalizer->order($order->fresh()));
    }
}
