<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        if ($this === self::Cancelled || $this === self::Delivered) {
            return false;
        }

        return match ($this) {
            self::Draft => $target === self::Pending || $target === self::Cancelled,
            self::Pending => in_array($target, [self::Confirmed, self::Cancelled], true),
            self::Confirmed => in_array($target, [self::Processing, self::Cancelled], true),
            self::Processing => in_array($target, [self::Shipped, self::Cancelled], true),
            self::Shipped => in_array($target, [self::Delivered, self::Cancelled], true),
            default => false,
        };
    }

    /** @return list<OrderStatus> */
    public function allowedTransitions(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $status) => $this->canTransitionTo($status) && $status !== $this,
        ));
    }
}
