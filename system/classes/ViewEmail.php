<?php

declare(strict_types=1);

namespace Mozg\classes;

use Mozg\modules\Lang;
use Tephida\View\myView;

/**
 *
 */
class ViewEmail
{
    public string $message = '';

    /**
     * @throws \Exception
     */
    public function __construct(string $template, $variables)
    {
        $config = settings_get() ?? settings_get();
        $views = ROOT_DIR . '/templates/' . $config['temp'] . '';
        $cache = ENGINE_DIR . '/cache/views';
        $blade = new myView($views, $cache, \Tephida\View\View::MODE_AUTO); // MODE_DEBUG allows pinpointing troubles.
        $blade::$dictionary = I18n::dictionary();
        $this->message = $blade->run($template, $variables);
    }

    /**
     * @return string
     */
    final public function run(): string
    {
        return $this->message;
    }
}