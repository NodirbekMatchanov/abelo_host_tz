<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array}> */
    private array $routes = [];

    public function __construct(private readonly array $config) {}

    public function get(string $pattern, array $handler): self
    {
        return $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): self
    {
        return $this->addRoute('POST', $pattern, $handler);
    }

    public function dispatch(Request $request): void
    {
        $path   = $request->getPath();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->match($route['pattern'], $path);

            if ($params !== null) {
                [$class, $action] = $route['handler'];
                (new $class($this->config))->$action($request, $params);
                return;
            }
        }

        throw new HttpException(404);
    }

    private function addRoute(string $method, string $pattern, array $handler): self
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];

        return $this;
    }

    private function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace_callback(
            '/\{(\w+)(?::([^}]+))?\}/',
            static fn(array $m) => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $pattern,
        );

        if (preg_match('#^' . $regex . '$#', $path, $matches) !== 1) {
            return null;
        }

        return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
    }
}
