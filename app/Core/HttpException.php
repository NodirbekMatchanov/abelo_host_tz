<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * HTTP-исключение, которое Application перехватывает и рендерит как страницу ошибки.
 */
class HttpException extends RuntimeException
{
    private const MESSAGES = [
        404 => 'Страница не найдена',
        403 => 'Доступ запрещён',
        500 => 'Внутренняя ошибка сервера',
    ];

    /**
     * @param int    $statusCode HTTP-код ответа (404, 403, 500 …)
     * @param string $message    Если пуст — берётся стандартное сообщение из MESSAGES
     */
    public function __construct(public readonly int $statusCode, string $message = '')
    {
        parent::__construct($message ?: (self::MESSAGES[$statusCode] ?? 'Ошибка'));
    }
}
