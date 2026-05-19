<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private string $path;
    private string $method;
    private array  $query;

    public function __construct()
    {
        $uri         = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path  = parse_url($uri, PHP_URL_PATH) ?: '/';
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        parse_str(parse_url($uri, PHP_URL_QUERY) ?? '', $this->query);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }
}
