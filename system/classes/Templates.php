<?php
declare(strict_types=1);
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
namespace Mozg\classes;

use ErrorException;

class Templates
{
    public string|false $dir = '.';
    public string|null $template = null;
    public string|null $copy_template = null;
    public array $data = array();
    public array $block_data = array();
    public array $result = array('info' => '', 'vote' => '', 'speedbar' => '', 'content' => '');

    public function set(string $name, array|string|int $var): void
    {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set($key, $key_var);
            }
        } else $this->data[$name] = $var;
    }

    public function set_block(string $name, array|string|int $var): void
    {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set_block($key, $key_var);
            }
        } else $this->block_data[$name] = $var;
    }

    /**
     * @throws ErrorException
     */
    public function load_template(string $tpl_name): false|string
    {
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            throw new ErrorException("Невозможно загрузить шаблон: " . $tpl_name, 0, 0, 'null', 0);
        }
        $this->template = file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl_name);

        if (str_contains($this->template, "[aviable=")) {
            $this->template = preg_replace_callback("#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2]);
            }, $this->template);
        }
        if (str_contains($this->template, "[not-aviable=")) {
            $this->template = preg_replace_callback("#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (str_contains($this->template, "[not-group=")) {
            $this->template = preg_replace_callback("#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (str_contains($this->template, "[group=")) {
            $this->template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2]);
            }, $this->template);
        }

        if (str_contains($this->template, "[group=")) {
            $this->template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function () {
                return $this->set_block("'\\[groups\\](.*?)\\[/groups\\]'si", "");
            }, $this->template);
        }

        $this->copy_template = $this->template;
        return $this->template;
    }

    public function check_module(string $aviable, array|string $block, bool $action = true): array|string
    {
        $mozg_module = Registry::get('go');
        $aviable = explode('|', $aviable);
        $block = str_replace('\"', '"', $block);
        if ($action) {
            if (!(in_array($mozg_module, $aviable)) and ($aviable[0] != "global"))
                return "";
            else
                return $block;
        } else {
            if ((in_array($mozg_module, $aviable)))
                return "";
            else return $block;
        }
    }

    public function check_group(string $groups, array|string $block, bool $action = true): array|string
    {
        $user_info = Registry::get('user_info');
        $groups = explode(',', $groups);
        if ($action) {
            if (!in_array($user_info['user_group'], $groups))
                return "";
        } else {
            if (in_array($user_info['user_group'], $groups))
                return "";
        }
        return str_replace('\"', '"', $block);
    }

    public function _clear(): void
    {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = $this->template;
    }

    public function clear(): void
    {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = null;
        $this->template = null;
    }

    public function global_clear(): void
    {
        $this->data = array();
        $this->block_data = array();
        $this->result = array();
        $this->copy_template = null;
        $this->template = null;
    }

    private function load_lang(string $var): string
    {
        global $lang;
        return $lang[$var];
    }

    public function compile(string $tpl): void
    {
        $find = $find_preg = $replace = $replace_preg = array();

        if (count($this->block_data)) {
            foreach ($this->block_data as $key_find => $key_replace) {
                $find_preg[] = $key_find;
                $replace_preg[] = $key_replace;
            }
            $this->copy_template = preg_replace($find_preg, $replace_preg, $this->copy_template);
        }
        foreach ($this->data as $key_find => $key_replace) {
            $find[] = $key_find;
            $replace[] = $key_replace;
        }
        $this->copy_template = str_replace($find, $replace, $this->copy_template);
        $this->copy_template = word_filter($this->copy_template);

        $this->copy_template = preg_replace_callback("#\\{translate=(.+?)\\}#is", function ($matches) {
            return $this->load_lang($match);
        }, $this->copy_template);

        if (str_contains($this->copy_template, "{*")) {
            $this->copy_template = preg_replace("'\\{\\*(.*?)\\*\\}'si", '', $this->copy_template);
        }

        $this->copy_template = str_replace(array("_&#123;_", "_&#91;_"), array("{", "["), $this->copy_template);

        if (isset($this->result[$tpl]))
            $this->result[$tpl] .= $this->copy_template;
        else
            $this->result[$tpl] = $this->copy_template;

        $this->_clear();
    }
}