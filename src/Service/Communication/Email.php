<?php

declare(strict_types=1);

namespace Service\Communication;

use Model;

class Email implements ICommunication
{
    public function __construct(
        private string $queueName,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function prepare(int $userId, string $templateName, array $params = []): void
    {
        // Формируем письмо и отправляет его в отложенную очередь, чтобы затем почтовый сервер доставил email
    }
}
