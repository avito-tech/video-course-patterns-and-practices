<?php

declare(strict_types=1);

namespace Controller\Dto\Admin;

class AddProduct
{
    public function __construct(
        private readonly string $name,
        private readonly int $price,
        private readonly bool $isHidden,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }
}
