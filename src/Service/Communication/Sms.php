<?php

declare(strict_types=1);

namespace Service\Communication;

use Model;

class Sms implements ICommunication
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
        // Форминуем текст смс и отправляет его в отложенную очередь, чтобы затем шлюз доставил sms
    }
}
