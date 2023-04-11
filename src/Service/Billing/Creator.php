<?php

declare(strict_types=1);

namespace Service\Billing;

use Service\Billing\Exception\BillingException;

class Creator
{
    public const BANK_TRANSFER = 'bank_transfer';
    public const CARD = 'card';

    public function pay(string $billingType, float $totalPrice): void
    {
        match ($billingType) {
            self::BANK_TRANSFER => $this->payByBankTransfer($totalPrice),
            self::CARD => $this->payByCard($totalPrice),
            default => throw new BillingException('unknown payment type'),
        };
    }

    protected function payByBankTransfer(float $totalPrice): void
    {
        (new BankTransfer())->pay($totalPrice);
    }

    protected function payByCard(float $totalPrice): void
    {
        (new Card())->pay($totalPrice);
    }
}
