<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private readonly PDO $pdo;

    /**
     * Создаёт PDO-соединение с MySQL.
     *
     * ATTR_EMULATE_PREPARES = false — реальные prepared statements на стороне БД;
     * без этого флага PDO лишь экранирует строки, что слабее по безопасности.
     *
     * @param array{host: string, port: int|string, database: string, charset: string, username: string, password: string} $config
     * @throws \RuntimeException если соединение не установлено
     */
    public function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset'],
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Возвращает PDO для передачи в репозитории.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
