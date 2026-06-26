<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderService
{
    public function __construct(
        private readonly OrderNumberGenerator $orderNumberGenerator,
    ) {
    }

    public function createOrder(int $customerId, ?string $notes = null): Order
    {
        $customer = Customer::query()->find($customerId);
        if (!$customer) {
            throw new \InvalidArgumentException(sprintf('Customer #%d not found.', $customerId));
        }

        return Order::query()->create([
            'order_number' => $this->orderNumberGenerator->generate(),
            'customer_id' => $customer->id,
            'status' => OrderStatus::Draft,
            'notes' => $notes,
        ]);
    }

    public function addItem(Order $order, int $productId, int $quantity): OrderItem
    {
        $this->assertEditable($order);

        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }

        $product = Product::query()->find($productId);
        if (!$product) {
            throw new \InvalidArgumentException(sprintf('Product #%d not found.', $productId));
        }

        if (!$product->active) {
            throw new \DomainException(sprintf('Product %s is not active.', $product->sku));
        }

        $existing = $order->items()->where('product_id', $product->id)->first();
        if ($existing) {
            $existing->quantity += $quantity;
            $existing->recalculateSubtotal();
            $existing->save();
            $order->recalculateTotals();

            return $existing->load('product');
        }

        $item = OrderItem::fromProduct($product, $quantity);
        $order->items()->save($item);
        $order->recalculateTotals();

        return $item->load('product');
    }

    public function removeItem(Order $order, int $itemId): void
    {
        $this->assertEditable($order);

        $item = $order->items()->where('id', $itemId)->first();
        if (!$item) {
            throw new \InvalidArgumentException(sprintf(
                'Order item #%d not found on order %s.',
                $itemId,
                $order->order_number,
            ));
        }

        $item->delete();
        $order->recalculateTotals();
    }

    public function submitOrder(Order $order): Order
    {
        $this->assertEditable($order);
        $order->loadCount('items');

        if ($order->items_count === 0) {
            throw new \DomainException('Cannot submit an order without items.');
        }

        $order->status = OrderStatus::Pending;
        $order->save();

        return $order;
    }

    public function updateStatus(Order $order, OrderStatus $newStatus): Order
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new \DomainException(sprintf(
                'Cannot transition order from %s to %s.',
                $order->status->label(),
                $newStatus->label(),
            ));
        }

        if ($newStatus === OrderStatus::Confirmed) {
            $this->reserveStock($order);
        }

        if ($newStatus === OrderStatus::Cancelled && $order->status !== OrderStatus::Draft) {
            $this->releaseStock($order);
        }

        $order->status = $newStatus;
        $order->save();

        return $order;
    }

    public function setShippingAndTax(Order $order, string $shippingAmount, string $taxAmount): Order
    {
        $this->assertEditable($order);

        $order->shipping_amount = $shippingAmount;
        $order->tax_amount = $taxAmount;
        $order->recalculateTotals();

        return $order;
    }

    private function reserveStock(Order $order): void
    {
        $order->load('items.product');

        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            if (!$product->hasStock($item->quantity)) {
                throw new \DomainException(sprintf(
                    'Insufficient stock for %s. Available: %d, requested: %d.',
                    $product->sku,
                    $product->stock_quantity,
                    $item->quantity,
                ));
            }

            $product->decreaseStock($item->quantity);
        }
    }

    private function releaseStock(Order $order): void
    {
        if (!in_array($order->status, [OrderStatus::Confirmed, OrderStatus::Processing, OrderStatus::Shipped], true)) {
            return;
        }

        $order->load('items.product');

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increaseStock($item->quantity);
            }
        }
    }

    private function assertEditable(Order $order): void
    {
        if (!$order->isEditable()) {
            throw new \DomainException(sprintf(
                'Order %s cannot be modified in status %s.',
                $order->order_number,
                $order->status->label(),
            ));
        }
    }
}
