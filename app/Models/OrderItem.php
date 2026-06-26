<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recalculateSubtotal(): void
    {
        $this->subtotal = bcmul((string) $this->unit_price, (string) $this->quantity, 2);
    }

    public static function fromProduct(Product $product, int $quantity): self
    {
        $item = new self([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
        $item->setRelation('product', $product);
        $item->recalculateSubtotal();

        return $item;
    }
}
