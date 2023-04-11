<?php

declare(strict_types=1);

namespace Controller\Dto\Order;

class RemoveProduct
{
    public function __construct(
        private readonly int $id,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
