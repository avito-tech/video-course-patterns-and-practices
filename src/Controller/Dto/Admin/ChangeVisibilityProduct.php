<?php

declare(strict_types=1);

namespace Controller\Dto\Admin;

class ChangeVisibilityProduct
{
    public function __construct(
        private readonly int $id,
        private readonly bool $isHidden,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }
}
