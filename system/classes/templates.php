<?php
/*
	Appointment: Парсинг шаблонов
	File: templates.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
class mozg_template {
    var $dir = '.';
    var $template = null;
    var $copy_template = null;
    var $data = array();
    var $block_data = array();
    var $result = array('info' => '', 'vote' => '', 'speedbar' => '', 'content' => '');
    var $allow_php_include = true;
    var $template_parse_time = 0;
    function set($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set($key, $key_var);
            }
        } else $this->data[$name] = $var;
    }
    function set_block($name, $var) {
        if (is_array($var) && count($var)) {
            foreach ($var as $key => $key_var) {
                $this->set_block($key, $key_var);
            }
        } else $this->block_data[$name] = $var;
    }
    function load_template($tpl_name) {
        $time_before = $this->get_real_time();
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            die("Невозможно загрузить шаблон: " . $tpl_name);
            return false;
        }
        $this->template = file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl_name);
        if (strpos($this->template, "[aviable=") !== false) {
            $this->template = preg_replace_callback("#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2]);
            }, $this->template);
        }
        if (strpos($this->template, "[not-aviable=") !== false) {
            $this->template = preg_replace_callback("#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (strpos($this->template, "[not-group=") !== false) {
            $this->template = preg_replace_callback("#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2], false);
            }, $this->template);
        }
        if (strpos($this->template, "[group=") !== false) {
            $this->template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2]);
            }, $this->template);
        }
        $this->copy_template = $this->template;
        $this->template_parse_time+= $this->get_real_time() - $time_before;
        return true;
    }
    function load_file($name, $include_file = "tpl") {
        global $db, $is_logged, $member_id, $cat_info, $config, $user_group, $category_id, $_TIME, $lang, $smartphone_detected, $mozg_module;
        $name = str_replace('..', '', $name);
        $url = @parse_url($name);
        $type = explode(".", $url['path']);
        $type = strtolower(end($type));
        if ($type == "tpl") {
            return $this->sub_load_template($name);
        }
        if ($include_file == "php") {
            if (!$this->allow_php_include) return;
            if ($type != "php") return "To connect permitted only files with the extension: .tpl or .php";
            if ($url['path'] {
                0
            } == "/") $file_path = dirname(ROOT_DIR . $url['path']);
            else $file_path = dirname(ROOT_DIR . "/" . $url['path']);
            $file_name = pathinfo($url['path']);
            $file_name = $file_name['basename'];
            if (stristr(php_uname("s"), "windows") === false) $chmod_value = @decoct(@fileperms($file_path)) % 1000;
            if (stristr(dirname($url['path']), "uploads") !== false) return "Include files from directory /uploads/ is denied";
            if (stristr(dirname($url['path']), "templates") !== false) return "Include files from directory /templates/ is denied";
            if ($chmod_value == 777) return "File {$url['path']} is in the folder, which is available to write (CHMOD 777). For security purposes the connection files from these folders is impossible. Change the permissions on the folder that it had no rights to the write.";
            if (!file_exists($file_path . "/" . $file_name)) return "File {$url['path']} not found.";
            if ($url['query']) {
                parse_str($url['query']);
            }
            ob_start();
            $tpl = new hp_template();
            $tpl->dir = TEMPLATE_DIR;
            include $file_path . "/" . $file_name;
            return ob_get_clean();
        }
        return '{include file="' . $name . '"}';
    }
    function sub_load_template($tpl_name) {
        $tpl_name = totranslit($tpl_name);
        if ($tpl_name == '' || !file_exists($this->dir . DIRECTORY_SEPARATOR . $tpl_name)) {
            return "Отсутствует файл шаблона: " . $tpl_name;
            return false;
        }
        $template = file_get_contents($this->dir . DIRECTORY_SEPARATOR . $tpl_name);
        if (strpos($template, "[aviable=") !== false) {
            $template = preg_replace_callback("#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2]);
            }, $template);
        }
        if (strpos($template, "[not-aviable=") !== false) {
            $template = preg_replace_callback("#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#is", function ($matches) {
                return $this->check_module($matches[1], $matches[2], false);
            }, $template);
        }
        if (strpos($template, "[not-group=") !== false) {
            $template = preg_replace_callback("#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2], false);
            }, $template);
        }
        if (strpos($template, "[group=") !== false) {
            $template = preg_replace_callback("#\\[group=(.+?)\\](.*?)\\[/group\\]#is", function ($matches) {
                return $this->check_group($matches[1], $matches[2]);
            }, $template);
        }
        return $template;
    }
    function check_module($aviable, $block, $action = true) {
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
    function check_group($groups, $block, $action = true) {
        global $user_info;
        $groups = explode(',', $groups);
        if ($action) {
            if (!in_array($user_info['user_group'], $groups)) return "";
        } else {
            if (in_array($user_info['user_group'], $groups)) return "";
        }
        $block = str_replace('\"', '"', $block);
        return $block;
    }
    function _clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = $this->template;
    }
    function clear() {
        $this->data = array();
        $this->block_data = array();
        $this->copy_template = null;
        $this->template = null;
    }
    function global_clear() {
        $this->data = array();
        $this->block_data = array();
        $this->result = array();
        $this->copy_template = null;
        $this->template = null;
    }
    function load_lang($var) {
        global $lang;
        return $lang[$var];
    }
    function compile($tpl) {
        $time_before = $this->get_real_time();
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
        if (isset($this->result[$tpl])) $this->result[$tpl].= $this->copy_template;
        else $this->result[$tpl] = $this->copy_template;
        $this->_clear();
        $this->template_parse_time+= $this->get_real_time() - $time_before;
    }
    function get_real_time() {
        list($seconds, $microSeconds) = explode(' ', microtime());
        return (( float )$seconds + ( float )$microSeconds);
    }
}
?>