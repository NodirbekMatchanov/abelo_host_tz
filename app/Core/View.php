<?php

declare(strict_types=1);

namespace App\Core;

use Smarty;

class View
{
    private Smarty $smarty;

    public function __construct(array $config)
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($config['smarty']['template_dir']);
        $this->smarty->setCompileDir($config['smarty']['compile_dir']);
        $this->smarty->setCacheDir($config['smarty']['cache_dir']);
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
