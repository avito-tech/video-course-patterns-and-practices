<?php

declare(strict_types=1);

namespace Service\Discount;

use Generator;
use Service\Discount\Exception\UnavailableDiscountException;

class Creator
{
    /**
     * Вместо такого хардкода может идти запрос в сервис промо-кодов с выполнением дополнительной бизнес-логики
     */
    public function getDiscount(int $userId, ?string $promoCode): float
    {
        foreach ($this->generateDiscounter($userId, $promoCode) as $discounter) {
            if (!$discounter instanceof IDiscount) {
                throw new UnavailableDiscountException('discounter not implement needed interface');
            }

            if ($discounter->hasDiscount()) {
                return $discounter->getDiscount();
            }
        }

        throw new UnavailableDiscountException('discounter not found');
    }

    private function generateDiscounter(int $userId, ?string $promoCode): Generator
    {
        yield new VipDiscount($userId);

        if (!is_null($promoCode)) {
            yield new PromoCode($promoCode);
        }

        yield new NullObject();
    }
}
