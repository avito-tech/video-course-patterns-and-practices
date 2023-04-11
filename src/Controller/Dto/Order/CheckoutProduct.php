<?php

declare(strict_types=1);

namespace Controller\Dto\Order;

class CheckoutProduct
{
    public function __construct(
        private readonly string $billingType,
        private readonly ?string $promoCode,
    ) {
    }

    public function getBillingType(): string
    {
        return $this->billingType;
    }

    public function getPromoCode(): ?string
    {
        return $this->promoCode;
    }
}
