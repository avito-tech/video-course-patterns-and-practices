<?php

declare(strict_types=1);

namespace Service\Discount;

use Service\Discount\Exception\UnavailableDiscountException;

class VipDiscount implements IDiscount
{
    public const VIP_USERS = [3, 10, 38];

    public function __construct(
        private readonly int $userId
    ) {
    }

    /**
     * @inheritdoc
     */
    public function hasDiscount(): bool
    {
        // Вместо хардкода может идти запрос в сервис промо-кодов с выполнением дополнительной бизнес-логики
        return in_array($this->userId, self::VIP_USERS, true);
    }

    /**
     * @inheritdoc
     */
    public function getDiscount(): float
    {
        if (!$this->hasDiscount()) {
            throw new UnavailableDiscountException();
        }

        return 20.0;
    }
}
