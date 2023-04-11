<?php

declare(strict_types=1);

namespace Service\Billing;

class Card implements IBilling
{
    /**
     * @inheritdoc
     */
    public function pay(float $totalPrice): void
    {
        // Оплата кредитной или дебетовой картой
    }
}
