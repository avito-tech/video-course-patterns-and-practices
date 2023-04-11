<?php

declare(strict_types=1);

namespace Test\Service\Product;

use App\Db\Exception\UniqueDbException;
use Controller\Dto\Admin\AddProduct;
use Controller\Dto\Admin\EditProduct;
use Controller\Dto\Admin\ChangeVisibilityProduct;
use Exception;
use Generator;
use Model\Entity\Product as ProductEntity;
use Model\Repository\Product as ProductRepository;
use PHPUnit\Framework\TestCase;
use Service\Product\Product as ProductService;

class ProductTest extends TestCase
{
    /**
     * @dataProvider dataProviderGetAll
     *
     * @param ProductEntity[] $productEntities
     */
    public function testGetAll(array $productEntities): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('fetchAll')
            ->willReturn($productEntities);

        $productService = new ProductService($productRepository);

        $productList = $productService->getAll();

        $this->assertEquals($productEntities, $productList);
    }

    public function dataProviderGetAll(): Generator
    {
        yield 'empty product list' => [
            [
            ]
        ];

        yield 'product list' => [
            [
                new ProductEntity(3, 'Another', 5011, false),
                new ProductEntity(10, 'Test', 19999, true),
            ]
        ];

        yield 'product list with extreme values' => [
            [
                new ProductEntity(0, '', 0, false),
                new ProductEntity(PHP_INT_MAX, 'Test', PHP_INT_MAX, true),
            ]
        ];
    }

    /**
     * @dataProvider dataProviderGetOne
     *
     * @param ProductEntity[] $productEntities
     */
    public function testGetOne(array $productEntities): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('search')
            ->willReturn($productEntities);

        $productService = new ProductService($productRepository);

        $product = $productService->getInfo(10);

        $this->assertEquals(current($productEntities), $product);
    }

    public function dataProviderGetOne(): Generator
    {
        yield 'empty product list' => [
            [
            ]
        ];

        yield 'product list' => [
            [
                new ProductEntity(3, 'Another', 5011, false),
                new ProductEntity(4, 'Another 2', 19911, false),
            ]
        ];
    }

    /**
     * @dataProvider dataProviderAdd
     *
     * @param array<string, mixed> $expectedResponse
     */
    public function testAdd(AddProduct $dto, int $addResponse, int $editResponse, array $expectedResponse): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('add')
            ->willReturn($addResponse);
        $productRepository->method('edit')
            ->willReturn($editResponse);

        $productService = new ProductService($productRepository);

        $response = $productService->add($dto);

        $this->assertEquals($expectedResponse, $response);
    }

    public function dataProviderAdd(): Generator
    {
        $affectedItemId = 100;

        yield 'add product' => [
            'dto' => new AddProduct('Test', 123, false),
            'addResponse' => $affectedItemId,
            'editResponse' => 0,
            'response' => [
                'isSuccess' => true,
                'productId' => $affectedItemId,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAddException
     *
     * @param array<string, mixed> $expectedResponse
     */
    public function testAddException(AddProduct $dto, Exception $exception, array $expectedResponse): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('add')
            ->willThrowException($exception);

        $productService = new ProductService($productRepository);

        $response = $productService->add($dto);

        $this->assertEquals($expectedResponse, $response);
    }

    public function dataProviderAddException(): Generator
    {
        yield 'UniqueDbException thrown in add method' => [
            'dto' => new AddProduct('Test', 123, false),
            'exception' => new UniqueDbException(),
            'response' => [
                'isSuccess' => false,
                'message' => 'Выбранный продукт уже добавлен в корзину',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderEdit
     *
     * @param array<string, mixed> $expectedResponse
     */
    public function testEdit(EditProduct $dto, int $addResponse, int $editResponse, array $expectedResponse): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('add')
            ->willReturn($addResponse);
        $productRepository->method('edit')
            ->willReturn($editResponse);

        $productService = new ProductService($productRepository);

        $response = $productService->edit($dto);

        $this->assertEquals($expectedResponse, $response);
    }

    public function dataProviderEdit(): Generator
    {
        $affectedItemId = 100;

        yield 'edit product' => [
            'dto' => new EditProduct($affectedItemId, 'Test', 123, false),
            'addResponse' => 0,
            'editResponse' => 1,
            'response' => [
                'isSuccess' => true,
                'productId' => $affectedItemId,
            ],
        ];

        yield 'edit product. 0 rows affected' => [
            'dto' => new EditProduct($affectedItemId, 'Test', 123, false),
            'addResponse' => 0,
            'editResponse' => 0,
            'response' => [
                'isSuccess' => false,
                'message' => 'Не удалось обновить данные',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderChangeVisibility
     *
     * @param ProductEntity[] $searchResponse
     * @param array<string, mixed> $expectedResponse
     */
    public function testChangeVisibility(ChangeVisibilityProduct $dto, array $searchResponse, int $editResponse, array $expectedResponse): void
    {
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('search')
            ->willReturn($searchResponse);
        $productRepository->method('edit')
            ->willReturn($editResponse);

        $productService = new ProductService($productRepository);

        $response = $productService->changeVisibility($dto);

        $this->assertEquals($expectedResponse, $response);
    }

    public function dataProviderChangeVisibility(): Generator
    {
        yield 'not found product' => [
            'dto' => new ChangeVisibilityProduct(123, false),
            'searchResponse' => [],
            'editResponse' => 0,
            'response' => [
                'isSuccess' => false,
                'message' => 'Продукт не существует',
            ],
        ];

        yield '0 rows affected' => [
            'dto' => new ChangeVisibilityProduct(123, false),
            'searchResponse' => [
                new ProductEntity(3, 'Another', 5011, false),
            ],
            'editResponse' => 0,
            'response' => [
                'isSuccess' => false,
                'message' => 'Не удалось обновить данные',
            ],
        ];

        yield 'updated visability' => [
            'dto' => new ChangeVisibilityProduct(123, false),
            'searchResponse' => [
                new ProductEntity(3, 'Another', 5011, false),
            ],
            'editResponse' => 1,
            'response' => [
                'isSuccess' => true,
                'affectedRows' => 1,
            ],
        ];
    }
}
