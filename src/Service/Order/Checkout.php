<?php

namespace Service\Order;

use Controller\Dto\Order\CheckoutProduct;
use Model;
use Service\Billing\Creator as BillingCreator;
use Service\Billing\Exception\BillingException;
use Service\Communication\Creator as CommunicationCreator;
use Service\Communication\Exception\CommunicationException;
use Service\Discount\Creator as DiscountCreator;
use Service\Discount\Exception\UnavailableDiscountException;

class Checkout
{
    public function __construct(
        private readonly Model\Repository\Basket $basket,
        private readonly DiscountCreator $discountCreator,
        private readonly CommunicationCreator $communicationCreator,
        private readonly BillingCreator $billingCreator,
    ) {
    }

    /**
     * Оплата корзины и информировании о размещённом заказе
     */
    public function checkoutProcess(int $userId, CheckoutProduct $checkoutProduct): bool
    {
        $totalPrice = 0;
        foreach ($this->basket->getUserBasket($userId) as $product) {
            $totalPrice += $product->getPrice();
        }

        try {
            $discount = $this->discountCreator->getDiscount($userId, $checkoutProduct->getPromoCode());
        } catch (UnavailableDiscountException) {
            // логириуем ошибку
            // считаем что пользователю недоступна скидка
            $discount = 0;
        }

        $totalPrice = $totalPrice - $totalPrice / 100 * $discount;

        try {
            $this->billingCreator->pay($checkoutProduct->getBillingType(), $totalPrice);
        } catch (BillingException) {
            // логируем ошибку
            // без оплаты нельзя отправить заказ, поэтому отменяем его
            return false;
        }

        try {
            $this->communicationCreator
                ->sendMessage(CommunicationCreator::TYPE_SMS)
                ->prepare($userId, 'checkout_template');
        } catch (CommunicationException) {
            // логируем ошибку
            // создаём задание на повторную отправку уведомления
        }

        return true;
    }
}
