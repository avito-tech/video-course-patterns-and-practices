<?php

declare(strict_types=1);

namespace Service\Billing;

use Service\Billing\Exception\BillingException;

interface IBilling
{
    /**
     * Рассчёт стоимости доставки заказа
     *
     * @throws BillingException
     */
    public function pay(float $totalPrice): void;
}
