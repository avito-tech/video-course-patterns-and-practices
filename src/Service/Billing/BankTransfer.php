<?php

declare(strict_types=1);

namespace Service\Billing;

class BankTransfer implements IBilling
{
    /**
     * @inheritdoc
     */
    public function pay(float $totalPrice): void
    {
        // Проведение банковского транзакции (перевод с одного счёта на другой счёт)
    }
}
