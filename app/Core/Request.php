<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $path;
    private string $method;
    private array  $query;

    public function __construct()
    {
        $uri          = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path   = parse_url($uri, PHP_URL_PATH) ?: '/';
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        parse_str(parse_url($uri, PHP_URL_QUERY) ?? '', $parsed);
        $this->query  = $parsed;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        return isset($this->query[$key]) ? (int) $this->query[$key] : $default;
    }
}
