<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'stock_quantity',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function hasStock(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }

    public function decreaseStock(int $quantity): void
    {
        if (!$this->hasStock($quantity)) {
            throw new \DomainException(sprintf('Insufficient stock for SKU %s.', $this->sku));
        }

        $this->stock_quantity -= $quantity;
        $this->save();
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }
}
