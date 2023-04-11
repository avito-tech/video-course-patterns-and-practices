<?php

declare(strict_types=1);

namespace Service\Communication;

use Service\Communication\Exception\CommunicationException;

interface ICommunication
{
    /**
     * Точка входа по формированию и отправке сообщения пользователю
     *
     * @param array<string, mixed> $params
     * @throws CommunicationException
     */
    public function prepare(int $userId, string $templateName, array $params = []): void;
}
