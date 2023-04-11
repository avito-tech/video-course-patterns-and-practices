<?php

declare(strict_types=1);

namespace Controller\Dto\Admin;

class EditProduct
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $price,
        private readonly bool $isHidden,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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
