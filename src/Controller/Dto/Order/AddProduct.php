<?php

declare(strict_types=1);

namespace Controller\Dto\Order;

class AddProduct
{
    public function __construct(
        private readonly int $userId,
        private readonly int $productId,
        private readonly int $quantity,
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
