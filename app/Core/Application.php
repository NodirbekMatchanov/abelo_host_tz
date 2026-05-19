<?php

declare(strict_types=1);

namespace App\Core;

final class Application
{
    public readonly Router  $router;
    public readonly Request $request;

    public function __construct()
    {
        $this->router  = new Router();
        $this->request = new Request();
    }

    public function run(): void
    {
        $this->router->dispatch($this->request);
    }
}
