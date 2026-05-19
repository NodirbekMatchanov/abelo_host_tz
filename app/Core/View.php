<?php

declare(strict_types=1);

namespace App\Core;

use Smarty;

final class View
{
    private Smarty $smarty;

    public function __construct()
    {
        $cfg = require dirname(__DIR__, 2) . '/config/app.php';

        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($cfg['views_dir']);
        $this->smarty->setCompileDir($cfg['compile_dir']);
        $this->smarty->setCacheDir($cfg['cache_dir']);
        $this->smarty->caching = false;
    }

    /** @param array<string, mixed> $data */
    public function render(string $template, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->display($template . '.tpl');
    }
}
