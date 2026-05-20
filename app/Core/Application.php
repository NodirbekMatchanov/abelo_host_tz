<?php

declare(strict_types=1);

namespace App\Core;

class Application
{
    private readonly Router  $router;
    private readonly Request $request;

    public function __construct(private readonly array $config)
    {
        $this->request = new Request();
        $this->router  = new Router($config);

        $this->loadRoutes();
    }

    public function run(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $this->router->dispatch($this->request);
        } catch (HttpException $e) {
            http_response_code($e->statusCode);
            (new View($this->config))->render('errors/' . $e->statusCode, [
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function loadRoutes(): void
    {
        $router = $this->router;
        require dirname(__DIR__, 2) . '/routes/web.php';
    }
}
