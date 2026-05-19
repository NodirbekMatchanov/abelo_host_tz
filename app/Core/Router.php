<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<array{method: string, pattern: string, handler: callable}> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method'  => 'GET',
            'pattern' => $this->toRegex($pattern),
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            if (preg_match($route['pattern'], $request->getPath(), $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                ($route['handler'])($request, $params);
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function toRegex(string $pattern): string
    {
        // Convert /post/{id} → /post/(?P<id>[^/]+)
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }
}
