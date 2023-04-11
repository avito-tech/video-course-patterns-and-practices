<?php

declare(strict_types=1);

namespace Controller;

use Controller\Dto\Admin\AddProduct;
use Controller\Dto\Admin\ChangeVisibilityProduct;
use Controller\Dto\Admin\EditProduct;
use Service\Product\Product;
use Symfony\Component\HttpFoundation\Request;
use View\Response;

class AdminController
{
    public function __construct(
        private readonly Product $product,
    ) {
    }

    /**
     * Добавляет новый продукт
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

        $operation = $this->product->add(
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

        return new Response(
            [
                'product' => [
                    'id' => $operation['productId'],
                ],
            ],
        );
    }

    /**
     * Редактирует существующий продукт
     */
    public function editProductAction(Request $request): Response
    {
        $requestData = $request->request->all();

        if (!$this->validateEditProductData($requestData)) {
            return new Response(
                [
                    'message' => 'Отправлен невалидный набор данных',
                ],
                false,
            );
        }

        $operation = $this->product->edit(
            $this->transformToEditProductDto($requestData)
        );

        if ($operation['isSuccess'] === false) {
            return new Response(
                [
                    'message' => $operation['message'],
                ],
                false,
            );
        }

        return new Response(
            [
                'product' => [
                    'id' => $operation['productId'],
                ],
            ],
        );
    }

    /**
     * Изменяет видимость продукта
     */
    public function changeVisibilityProductAction(Request $request): Response
    {
        $requestData = $request->request->all();

        if (!$this->validateChangeVisibilityProductData($requestData)) {
            return new Response(
                [
                    'message' => 'Отправлен невалидный набор данных',
                ],
                false,
            );
        }

        $operation = $this->product->changeVisibility(
            $this->transformToChangeVisibilityProductDto($requestData)
        );

        if ($operation['isSuccess'] === false) {
            return new Response(
                [
                    'message' => $operation['message'],
                ],
                false,
            );
        }

        return new Response(
            [
                'affectedRows' => $operation['affectedRows'],
            ],
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
        return array_key_exists('name', $data)
            && is_string($data['name'])
            && array_key_exists('price', $data)
            && is_numeric($data['price']);
    }

    private function transformToAddProductDto(array $data): AddProduct
    {
        return new AddProduct(
            $data['name'],
            (int) $data['price'],
            isset($data['isHidden']) && $data['isHidden'],
        );
    }

    /**
     * Здесь упрощённая валидация, чтобы не усложнять проект
     * Рекомендуется использовать symfony/validator
     *
     * @param array<string, mixed> $data
     */
    private function validateEditProductData(array $data): bool
    {
        return array_key_exists('name', $data)
            && is_string($data['name'])
            && array_key_exists('price', $data)
            && is_numeric($data['price']);
    }

    private function transformToEditProductDto(array $data): EditProduct
    {
        return new EditProduct(
            (int) $data['id'],
            $data['name'],
            (int) $data['price'],
            isset($data['isHidden']) && $data['isHidden'],
        );
    }

    /**
     * Здесь упрощённая валидация, чтобы не усложнять проект
     * Рекомендуется использовать symfony/validator
     *
     * @param array<string, mixed> $data
     */
    private function validateChangeVisibilityProductData(array $data): bool
    {
        return array_key_exists('id', $data)
            && is_string($data['id'])
            && array_key_exists('isHidden', $data)
            && ($data['isHidden'] === 'true' || $data['isHidden'] === 'false');
    }

    private function transformToChangeVisibilityProductDto(array $data): ChangeVisibilityProduct
    {
        return new ChangeVisibilityProduct(
            (int) $data['id'],
            $data['isHidden'] === 'true',
        );
    }
}
