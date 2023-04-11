<?php

declare(strict_types=1);

namespace Service\Discount;

interface IDiscount
{
    /**
     * Проверяем возможность получения скидки
     */
    public function hasDiscount(): bool;

    /**
     * Получаем скидку в процентах
     */
    public function getDiscount(): float;
}
