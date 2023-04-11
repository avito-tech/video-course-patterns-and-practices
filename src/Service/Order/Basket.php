<?php

declare(strict_types=1);

namespace Service\Order;

use App\Db\Exception\UniqueDbException;
use Controller\Dto\Order\AddProduct;
use Controller\Dto\Order\RemoveProduct;
use Model;

class Basket
{
    public function __construct(
        private readonly Model\Repository\Basket $basket,
        private readonly Model\Repository\Product $product,
    ) {
    }

    /**
     * Корзина пользователя
     *
     * @return Model\Entity\Basket[]
     */
    public function getUserBasket(int $userId): array
    {
        return $this->basket->getUserBasket($userId);
    }

    /**
     * Проверяем наличие товара в корзине пользователя
     */
    public function isProductInBasket(int $userId, int $productId): bool
    {
        foreach ($this->basket->getUserBasket($userId) as $item) {
            if ($productId === $item->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Добавляем товар в заказ
     *
     * @return array<string, mixed>
     */
    public function addProduct(AddProduct $dto): array
    {
        $isProductExists = $this->product->exists(
            $dto->getProductId(),
        );

        if (!$isProductExists) {
            return [
                'isSuccess' => false,
                'message' => 'Продукт не существует',
            ];
        }

        try {
            $orderId = $this->basket->addProduct(
                $dto->getUserId(),
                $dto->getProductId(),
                $dto->getQuantity(),
            );
        } catch (UniqueDbException) {
            return [
                'isSuccess' => false,
                'message' => 'Выбранный продукт уже добавлен в корзину',
            ];
        }

        return [
            'isSuccess' => true,
            'orderId' => $orderId,
        ];
    }

    /**
     * Удаляет товар из заказа
     */
    public function removeProduct(RemoveProduct $dto): int
    {
        return $this->basket->removeProduct(
            $dto->getId(),
        );
    }
}
