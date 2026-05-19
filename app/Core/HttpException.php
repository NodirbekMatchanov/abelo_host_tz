<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class HttpException extends RuntimeException
{
    private const MESSAGES = [
        404 => 'Страница не найдена',
        403 => 'Доступ запрещён',
        500 => 'Внутренняя ошибка сервера',
    ];

    public function __construct(public readonly int $statusCode, string $message = '')
    {
        parent::__construct($message ?: (self::MESSAGES[$statusCode] ?? 'Ошибка'));
    }
}
