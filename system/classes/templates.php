<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
class mozg_template {
    public string|false$dir = '.';
    public string|null $template = null;
    public string|null $copy_template = null;
    public array $data = array();
    public array $block_data = array();
    public array $result = array('info' => '', 'vote' => '', 'speedbar' => '', 'content' => '');
    public bool $allow_php_include = true;

    public function set($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set($key, $key_var);
            }
        } else $this->data[$name] = $var;
    }
    public function set_block($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set_block($key, $key_var);
            }
        } else $this->block_data[$name] = $var;
    }

    /**
     * @throws ErrorException
     */
    public function load_template($tpl_name): bool
    {
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            throw new ErrorException("Невозможно загрузить шаблон: " . $tpl_name, 0, 'null', 'null', 'null');
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
        $this->copy_template = $this->template;
        return true;
    }

    public function load_file($name, $include_file = "tpl"): bool|array|string|null
    {
        $name = str_replace('..', '', $name);
        $url = parse_url($name);
        $type = explode(".", $url['path']);
        $type = strtolower(end($type));
        if ($type == "tpl") {
            return $this->sub_load_template($name);
        }
        if ($include_file == "php") {
            if (!$this->allow_php_include){
                echo 'error';
                return false;
            }
            if ($type != "php") return "To connect permitted only files with the extension: .tpl or .php";

            $file_path = ROOT_DIR."/".cleanPath(dirname($url['path']));
            $url['path'] = clearFilePath( trim($url['path']) , array ("php") );

            $file_name = pathinfo($url['path']);
            $file_name = $file_name['basename'];
            if (stristr(php_uname("s"), "windows") === false)
                $chmod_value = @decoct(@fileperms($file_path)) % 1000;
            if (stristr(dirname($url['path']), "uploads") !== false) return "Include files from directory /uploads/ is denied";
            if (stristr(dirname($url['path']), "templates") !== false) return "Include files from directory /templates/ is denied";
            if ($chmod_value == 777)
                return "File {$url['path']} is in the folder, which is available to write (CHMOD 777). For security purposes the connection files from these folders is impossible. Change the permissions on the folder that it had no rights to the write.";
            if (!file_exists($file_path . "/" . $file_name)) return "File {$url['path']} not found.";
            if (isset($url['query']) AND $url['query']) {
                parse_str($url['query']);
            }
            ob_start();
            $tpl = new mozg_template();
            $tpl->dir = TEMPLATE_DIR;
            include $file_path . "/" . $file_name;
            return ob_get_clean();
        }
        return '{include file="' . $name . '"}';
    }
    public function sub_load_template($tpl_name): array|bool|string|null
    {
        $tpl_name = to_translit($tpl_name);
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            return "Отсутствует файл шаблона: " . $tpl_name;
        }
        $template = file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl_name);
        if (str_contains($template, "[aviable=")) {
            $template = preg_replace_callback("#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2]);
            }, $template);
        }
        if (str_contains($template, "[not-aviable=")) {
            $template = preg_replace_callback("#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2], false);
            }, $template);
        }
        if (str_contains($template, "[not-group=")) {
            $template = preg_replace_callback("#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2], false);
            }, $template);
        }
        if (str_contains($template, "[group=")) {
            $template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2]);
            }, $template);
        }
        return $template;
    }

    public function check_module($aviable, $block, $action = true): array|string
    {
        global $mozg_module;
        $aviable = explode('|', $aviable);
        $block = str_replace('\"', '"', $block);
        if ($action) {
            if (!(in_array($mozg_module, $aviable)) and ($aviable[0] != "global")) return "";
            else return $block;
        } else {
            if ((in_array($mozg_module, $aviable))) return "";
            else return $block;
        }
    }
    public function check_group($groups, $block, $action = true): array|string
    {
        global $user_info;
        $groups = explode(',', $groups);
        if ($action) {
            if (!in_array($user_info['user_group'], $groups)) return "";
        } else {
            if (in_array($user_info['user_group'], $groups)) return "";
        }
        return str_replace('\"', '"', $block);
    }
    public function _clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = $this->template;
    }
    public function clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = null;
        $this->template = null;
    }
    public function global_clear() {
        $this->data = array();
        $this->block_data = array();
        $this->result = array();
        $this->copy_template = null;
        $this->template = null;
    }
    private function load_lang($var) {
        global $lang;
        return $lang[$var];
    }
    public function compile($tpl) {
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

        $this->copy_template = str_replace(array("_&#123;_", "_&#91;_"), array("{", "["), $this->copy_template);

        if( isset( $this->result[$tpl] ) )
            $this->result[$tpl] .= $this->copy_template;
        else
            $this->result[$tpl] = $this->copy_template;


        $this->_clear();
//        $this->template_parse_time+= $this->get_real_time() - $time_before;
    }

}