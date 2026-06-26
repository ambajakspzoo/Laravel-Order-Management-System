<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return HasMany<OrderItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [OrderStatus::Draft, OrderStatus::Pending], true);
    }

    public function recalculateTotals(): void
    {
        $subtotal = '0.00';

        foreach ($this->items as $item) {
            $item->recalculateSubtotal();
            $item->save();
            $subtotal = bcadd($subtotal, (string) $item->subtotal, 2);
        }

        $this->subtotal = $subtotal;
        $this->total_amount = bcadd(
            bcadd($this->subtotal, (string) $this->tax_amount, 2),
            (string) $this->shipping_amount,
            2,
        );
        $this->save();
    }
}
