<?php

declare(strict_types=1);

namespace Service\Communication;

use Service\Communication\Exception\CommunicationException;

class Creator
{
    public const TYPE_EMAIL = 'email';
    public const QUEUE_EMAIL = 'sender_email';
    public const TYPE_SMS = 'sms';
    public const QUEUE_SMS = 'sender_sms';

    public function sendMessage(string $type): ICommunication
    {
        return match ($type) {
            self::TYPE_EMAIL => $this->prepareEmail(),
            self::TYPE_SMS => $this->prepareSms(),
            default => throw new CommunicationException('unknown communication type'),
        };
    }

    protected function prepareEmail(): ICommunication
    {
        return new Email(static::QUEUE_EMAIL);
    }

    protected function prepareSms(): ICommunication
    {
        return new Sms(static::QUEUE_SMS);
    }
}
