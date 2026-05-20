<?php

declare(strict_types=1);

namespace App\Core;

use Smarty;

class View
{
    private Smarty $smarty;

    /**
     * Инициализирует Smarty с директориями из конфига.
     * Кэширование отключено — данные актуальны при каждом запросе.
     *
     * @param array{smarty: array{template_dir: string, compile_dir: string, cache_dir: string}} $config
     */
    public function __construct(array $config)
    {
        $this->smarty = new Smarty();
        $this->smarty->setTemplateDir($config['smarty']['template_dir']);
        $this->smarty->setCompileDir($config['smarty']['compile_dir']);
        $this->smarty->setCacheDir($config['smarty']['cache_dir']);
        $this->smarty->caching = false;
    }

    /**
     * Передаёт переменные в шаблон и выводит результат.
     *
     * @param string               $template имя шаблона без расширения (например 'home/index')
     * @param array<string, mixed> $data     переменные, доступные в .tpl
     */
    public function render(string $template, array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }

        $this->smarty->display($template . '.tpl');
    }
}
