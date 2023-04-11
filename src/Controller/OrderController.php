<?php

declare(strict_types=1);

namespace Controller;

use Controller\Dto\Order\AddProduct;
use Controller\Dto\Order\CheckoutProduct;
use Controller\Dto\Order\RemoveProduct;
use Service\Order\Basket;
use Service\Order\Checkout;
use Symfony\Component\HttpFoundation\Request;
use View\Response;

class OrderController
{
    public function __construct(
        private readonly Basket $basket,
        private readonly Checkout $checkout,
    ) {
    }

    /**
     * Корзина пользователя
     */
    public function infoAction(int $userId): Response
    {
        $response = [];
        foreach ($this->basket->getUserBasket($userId) as $item) {
            $response[] = $item->toArray();
        }

        return new Response($response);
    }

    /**
     * Проверяет наличие товара в корзине пользователя
     */
    public function checkProductInBasketAction(int $userId, int $productId): Response
    {
        return new Response([
            'isInBasket' => $this->basket->isProductInBasket($userId, $productId),
        ]);
    }

    /**
     * Добавляет новый товар в корзину пользователя
     */
    public function addProductAction(Request $request): Response
    {
        $requestData = $request->request->all();

        if (!$this->validateAddProductData($requestData)) {
            return new Response(
                [
                    'message' => 'Отправлен невалидный набор данных',
                ],
                false,
            );
        }

        $operation = $this->basket->addProduct(
            $this->transformToAddProductDto($requestData)
        );

        if ($operation['isSuccess'] === false) {
            return new Response(
                [
                    'message' => $operation['message'],
                ],
                false,
            );
        }

        return new Response([
            'order' => [
                'id' => $operation['orderId'],
            ],
        ]);
    }

    /**
     * Удаляет товар из корзины пользователя
     */
    public function removeProductAction(Request $request): Response
    {
        $requestData = $request->request->all();

        if (!$this->validateRemoveProductData($requestData)) {
            return new Response(
                [
                    'message' => 'Отправлен невалидный набор данных',
                ],
                false,
            );
        }

        $affectedRows = $this->basket->removeProduct(
            $this->transformToRemoveProductDto($requestData)
        );

        return new Response(['affectedRows' => $affectedRows]);
    }

    /**
     * Оформление заказа
     */
    public function checkoutAction(int $userId, Request $request): Response
    {
        $requestData = $request->request->all();

        if (!$this->validateCheckoutData($requestData)) {
            return new Response(
                [
                    'message' => 'Отправлен невалидный набор данных',
                ],
                false,
            );
        }

        return new Response(
            [
                'finished' => $this->checkout->checkoutProcess(
                    $userId,
                    $this->transformToCheckoutProductDto($requestData),
                ),
            ]
        );
    }

    /**
     * Здесь упрощённая валидация, чтобы не усложнять проект
     * Рекомендуется использовать symfony/validator
     *
     * @param array<string, mixed> $data
     */
    private function validateAddProductData(array $data): bool
    {
        return array_key_exists('userId', $data)
            && is_numeric($data['userId'])
            && array_key_exists('productId', $data)
            && is_numeric($data['productId'])
            && array_key_exists('quantity', $data)
            && is_numeric($data['quantity']);
    }

    private function transformToAddProductDto(array $data): AddProduct
    {
        return new AddProduct(
            (int) $data['userId'],
            (int) $data['productId'],
            (int) $data['quantity'],
        );
    }

    /**
     * Здесь упрощённая валидация, чтобы не усложнять проект
     * Рекомендуется использовать symfony/validator
     *
     * @param array<string, mixed> $data
     */
    private function validateRemoveProductData(array $data): bool
    {
        return array_key_exists('id', $data)
            && is_numeric($data['id']);
    }

    private function transformToRemoveProductDto(array $data): RemoveProduct
    {
        return new RemoveProduct(
            (int) $data['id'],
        );
    }

    /**
     * Здесь упрощённая валидация, чтобы не усложнять проект
     * Рекомендуется использовать symfony/validator
     *
     * @param array<string, mixed> $data
     */
    private function validateCheckoutData(array $data): bool
    {
        return array_key_exists('billingType', $data)
            && is_string($data['billingType']);
    }

    private function transformToCheckoutProductDto(array $data): CheckoutProduct
    {
        return new CheckoutProduct(
            $data['billingType'],
            $data['promoCode'] ?? null,
        );
    }
}
