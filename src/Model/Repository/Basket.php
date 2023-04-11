<?php

declare(strict_types=1);

namespace Model\Repository;

use App\Db\DbProvider;
use Model\Entity;

class Basket
{
    public function __construct(
        private readonly DbProvider $db,
    ) {
    }

    /**
     * Возвращаем коллекцию товаров в корзине
     *
     * @return Entity\Basket[]
     */
    public function getUserBasket(int $userId): array
    {
        $query = <<<EOT
            select b.id, p.name, p.price
            from basket b
            inner join product p on b.product_id = p.id
            where b.user_id = :user_id and p.is_hidden = 0
        EOT;

        $userBasket = [];
        foreach ($this->db->fetchAll($query, [':user_id' => $userId]) as $item) {
            $userBasket[] = new Entity\Basket($item['id'], $item['name'], $item['price']);
        }

        return $userBasket;
    }

    /**
     * Добавляет продукт в корзину
     */
    public function addProduct(int $userId, int $productId, int $quantity): int
    {
        return $this->db->insert(
            'insert into basket (user_id, product_id, quantity) values (:user_id, :product_id, :quantity)',
            ['user_id' => $userId, 'product_id' => $productId, 'quantity' => $quantity],
        );
    }

    /**
     * Удаляет продукт из корзины
     */
    public function removeProduct(int $id): int
    {
        return $this->db->execute(
            'delete from basket where id = :id',
            ['id' => $id],
        );
    }
}
