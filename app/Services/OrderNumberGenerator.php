<?php

namespace App\Services;

use App\Models\Order;

final class OrderNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = sprintf('ORD-%s-%04d', date('Ymd'), random_int(1, 9999));
            $exists = Order::query()->where('order_number', $number)->exists();
        } while ($exists);

        return $number;
    }
}
