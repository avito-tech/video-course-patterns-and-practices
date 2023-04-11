<?php

declare(strict_types=1);

namespace View;

use Symfony\Component\HttpFoundation\Response as ResponseCore;

class Response extends ResponseCore
{
    public function __construct(array $content = [], bool $isSuccess = true, int $status = self::HTTP_OK, array $headers = [])
    {
        $output = [
            'success' => $isSuccess,
            'content' => $content,
        ];

        parent::__construct(
            json_encode($output, JSON_THROW_ON_ERROR),
            $status,
            array_merge($headers, ['Content-type' => 'application/json;charset=utf-8']),
        );
    }
}
