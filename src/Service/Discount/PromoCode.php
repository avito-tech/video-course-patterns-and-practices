<?php

declare(strict_types=1);

namespace Service\Discount;

class PromoCode implements IDiscount
{
    public const CODE_NEW_BUYER = 'new_10';
    public const CODE_BLACK_FRIDAY = 'black_15';

    public function __construct(
        private readonly string $promoCode,
    ) {
    }

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
        // Вместо хардкода может идти запрос в сервис промо-кодов с выполнением дополнительной бизнес-логики
        return match ($this->promoCode) {
            self::CODE_NEW_BUYER => 10.0,
            self::CODE_BLACK_FRIDAY => 15.0,
            default => 0.0,
        };
    }
}
