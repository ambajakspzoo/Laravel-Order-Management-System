<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
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

        return view('dashboard.index', [
            'statusCounts' => $statusCounts,
            'recentOrders' => Order::query()->with('customer')->latest()->limit(10)->get(),
            'lowStock' => Product::query()
                ->where('active', true)
                ->where('stock_quantity', '<=', 10)
                ->orderBy('stock_quantity')
                ->limit(10)
                ->get(),
            'totalOrders' => array_sum($statusCounts),
            'totalCustomers' => Customer::query()->count(),
            'totalProducts' => Product::query()->where('active', true)->count(),
            'statuses' => OrderStatus::cases(),
        ]);
    }
}
