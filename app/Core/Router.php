<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<array{methods: string[], pattern: string, handler: callable}> */
    private array $routes = [];

    private string $groupPrefix = '';

    // ── Route registration ────────────────────────────────────────────────────

    public function get(string $path, callable $handler): void
    {
        $this->add(['GET'], $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add(['POST'], $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add(['PUT'], $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->add(['PATCH'], $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add(['DELETE'], $path, $handler);
    }

    /** Register a route for several HTTP methods at once. */
    public function match(array $methods, string $path, callable $handler): void
    {
        $this->add(array_map('strtoupper', $methods), $path, $handler);
    }

    /** Prefix all routes registered inside the callback. */
    public function group(string $prefix, callable $fn): void
    {
        $previous          = $this->groupPrefix;
        $this->groupPrefix = $previous . '/' . trim($prefix, '/');
        $fn($this);
        $this->groupPrefix = $previous;
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────

    public function dispatch(Request $request): void
    {
        $method   = $request->getMethod();
        $path     = $request->getPath();
        $matched  = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            $matched = true;

            if (!in_array($method, $route['methods'], true)) {
                http_response_code(405);
                echo '405 Method Not Allowed';
                return;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            ($route['handler'])($request, $params);
            return;
        }

        if (!$matched) {
            http_response_code(404);
            echo '404 Not Found';
        }
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function add(array $methods, string $path, callable $handler): void
    {
        $fullPath = $this->groupPrefix . '/' . ltrim($path, '/');
        $fullPath = $fullPath === '//' ? '/' : (rtrim($fullPath, '/') ?: '/');

        $this->routes[] = [
            'methods' => $methods,
            'pattern' => $this->compile($fullPath),
            'handler' => $handler,
        ];
    }

    /**
     * Compile a route path into a regex.
     *
     * Supported syntax:
     *   {name}        required, matches [^/]+
     *   {name:\d+}    required, custom constraint
     *   {name?}       optional (trailing segment), matches [^/]+
     *
     * Examples:
     *   /post/{id:\d+}          →  /post/42
     *   /post/{id:\d+}/{slug}   →  /post/42/my-title
     *   /archive/{year?}        →  /archive  or  /archive/2024
     */
    private function compile(string $path): string
    {
        // {name?} — optional trailing segment (must be last)
        $regex = preg_replace_callback(
            '#/\{(\w+)\?\}#',
            fn($m) => '(?:/(?P<' . $m[1] . '>[^/]+))?',
            $path,
        );

        // {name:pattern} — required with custom constraint
        $regex = preg_replace_callback(
            '#\{(\w+):([^}]+)\}#',
            fn($m) => '(?P<' . $m[1] . '>' . $m[2] . ')',
            $regex,
        );

        // {name} — required, default constraint
        $regex = preg_replace(
            '#\{(\w+)\}#',
            '(?P<$1>[^/]+)',
            $regex,
        );

        return '#^' . $regex . '$#u';
    }
}
