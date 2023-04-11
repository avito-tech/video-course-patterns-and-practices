<?php

declare(strict_types=1);

namespace Service\Discount;

class NullObject implements IDiscount
{
    /**
     * @inheritdoc
     */
    public function hasDiscount(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDiscount(): float
    {
        // Скидка отсутствует
        return 0;
    }
}
