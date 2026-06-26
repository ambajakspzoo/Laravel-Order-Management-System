<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

final class EntityNormalizer
{
    /** @return array<string, mixed> */
    public function customer(Customer $customer, bool $includeOrders = false): array
    {
        $data = [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => [
                'line1' => $customer->address_line1,
                'line2' => $customer->address_line2,
                'city' => $customer->city,
                'postalCode' => $customer->postal_code,
                'country' => $customer->country,
            ],
            'createdAt' => $customer->created_at?->toIso8601String(),
        ];

        if ($includeOrders) {
            $data['orderCount'] = $customer->orders()->count();
        }

        return $data;
    }

    /** @return array<string, mixed> */
    public function product(Product $product): array
    {
        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (string) $product->price,
            'stockQuantity' => $product->stock_quantity,
            'active' => $product->active,
            'createdAt' => $product->created_at?->toIso8601String(),
            'updatedAt' => $product->updated_at?->toIso8601String(),
        ];
    }

    /** @return array<string, mixed> */
    public function orderItem(OrderItem $item): array
    {
        $product = $item->product;

        return [
            'id' => $item->id,
            'productId' => $product?->id,
            'sku' => $product?->sku,
            'productName' => $product?->name,
            'quantity' => $item->quantity,
            'unitPrice' => (string) $item->unit_price,
            'subtotal' => (string) $item->subtotal,
        ];
    }

    /** @return array<string, mixed> */
    public function order(Order $order, bool $detailed = true): array
    {
        $order->loadMissing(['customer', 'items.product']);

        $data = [
            'id' => $order->id,
            'orderNumber' => $order->order_number,
            'status' => $order->status->value,
            'statusLabel' => $order->status->label(),
            'customerId' => $order->customer_id,
            'customerName' => $order->customer?->name,
            'subtotal' => (string) $order->subtotal,
            'taxAmount' => (string) $order->tax_amount,
            'shippingAmount' => (string) $order->shipping_amount,
            'totalAmount' => (string) $order->total_amount,
            'notes' => $order->notes,
            'createdAt' => $order->created_at?->toIso8601String(),
            'updatedAt' => $order->updated_at?->toIso8601String(),
            'editable' => $order->isEditable(),
        ];

        if ($detailed) {
            $data['items'] = $order->items->map(fn (OrderItem $item) => $this->orderItem($item))->values()->all();
            $data['allowedTransitions'] = array_map(
                fn (OrderStatus $status) => $status->value,
                $order->status->allowedTransitions(),
            );
        }

        return $data;
    }
}
