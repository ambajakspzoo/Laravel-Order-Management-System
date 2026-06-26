<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Support\EntityNormalizer;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly EntityNormalizer $normalizer)
    {
    }

    public function index(): JsonResponse
    {
        $statusCounts = array_fill_keys(
            array_map(fn (OrderStatus $s) => $s->value, OrderStatus::cases()),
            0,
        );

        Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->each(function ($row) use (&$statusCounts) {
                $status = $row->status instanceof OrderStatus ? $row->status->value : (string) $row->status;
                $statusCounts[$status] = (int) $row->total;
            });

        $recentOrders = Order::query()->with('customer')->latest()->limit(10)->get();

        $lowStock = Product::query()
            ->where('active', true)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        return $this->jsonOk([
            'totals' => [
                'orders' => array_sum($statusCounts),
                'customers' => Customer::query()->count(),
                'products' => Product::query()->where('active', true)->count(),
            ],
            'ordersByStatus' => $statusCounts,
            'recentOrders' => $recentOrders->map(fn (Order $order) => [
                'id' => $order->id,
                'orderNumber' => $order->order_number,
                'customerName' => $order->customer?->name,
                'status' => $order->status->value,
                'totalAmount' => (string) $order->total_amount,
                'createdAt' => $order->created_at?->toIso8601String(),
            ])->values(),
            'lowStockProducts' => $lowStock->map(fn (Product $p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'stockQuantity' => $p->stock_quantity,
            ])->values(),
            'statuses' => array_map(fn (OrderStatus $s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ], OrderStatus::cases()),
        ]);
    }
}
