<?php
/*
	Appointment: Класс для работы с базой данных
	File: mysql.php
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if (!defined('MOZG')) die('Hacking attempt!');
class db {
    var $db_id = false;
    var $query_num = 0;
    var $query_list = array();
    var $mysql_error = '';
    var $mysql_version = '';
    var $mysql_error_num = 0;
    var $mysql_extend = "MySQLi";
    var $MySQL_time_taken = 0;
    var $query_id = false;
    function connect($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error = 1) {
        $db_location = explode(":", $db_location);
        if (isset($db_location[1])) {
            $this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name, $db_location[1]);
        } else {
            $this->db_id = @mysqli_connect($db_location[0], $db_user, $db_pass, $db_name);
        }
        if (!$this->db_id) {
            if ($show_error == 1) {
                $this->display_error(mysqli_connect_error(), '1');
            } else {
                return false;
            }
        }
        $this->mysql_version = mysqli_get_server_info($this->db_id);
        if (!defined('COLLATE')) {
            define("COLLATE", "utf8");
        }
        mysqli_query($this->db_id, "SET NAMES '" . COLLATE . "'");
        return true;
    }
    function query($query, $show_error = true) {
        $time_before = $this->get_real_time();
        if (!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);
        if (!($this->query_id = mysqli_query($this->db_id, $query))) {
            $this->mysql_error = mysqli_error($this->db_id);
            $this->mysql_error_num = mysqli_errno($this->db_id);
            if ($show_error) {
                $this->display_error($this->mysql_error, $this->mysql_error_num, $query);
            }
        }
        $this->MySQL_time_taken+= $this->get_real_time() - $time_before;
        $this->query_num++;
        return $this->query_id;
    }
    function get_row($query_id = '') {
        if ($query_id == '') $query_id = $this->query_id;
        return is_object($query_id) ? mysqli_fetch_assoc($query_id) : [];
    }
    function get_array($query_id = '') {
        if ($query_id == '') $query_id = $this->query_id;
        return mysqli_fetch_array($query_id);
    }
    function super_query($query, $multi = false, $cache_prefix = false, $system_cache = false) {
        //Если включен кеш, то проверяем на его существование
        if ($cache_prefix) {
            if ($system_cache) $data = system_cache($cache_prefix);
            else $data = mozg_cache($cache_prefix);
        }
        //Если есть ответ с кеша
        if ($data) {
            $unSerData = unserialize($data);
            if ($unSerData) return $unSerData;
            else return array();
        } else {
            if (!$multi) {
                $this->query($query);
                $data = $this->get_row();
                $this->free();
                //Если включен кеш, то создаём его
                if ($cache_prefix) {
                    $cache_rows = serialize($data);
                    mozg_create_cache($cache_prefix, $cache_rows);
                }
                return $data ? $data : [];
            } else {
                $this->query($query);
                $rows = array();
                while ($row = $this->get_row()) {
                    $rows[] = $row;
                }
                $this->free();
                //Если включен кеш, то создаём его
                if ($cache_prefix) {
                    $cache_rows = serialize($rows);
                    if ($system_cache) creat_system_cache($cache_prefix, $cache_rows);
                    else mozg_create_cache($cache_prefix, $cache_rows);
                }
                return $rows ? $rows : [];
            }
        }
    }
    function num_rows($query_id = '') {
        if ($query_id == '') $query_id = $this->query_id;
        return mysqli_num_rows($query_id);
    }
    function insert_id() {
        return mysqli_insert_id($this->db_id);
    }
    function get_result_fields($query_id = '') {
        if ($query_id == '') $query_id = $this->query_id;
        while ($field = mysqli_fetch_field($query_id)) {
            $fields[] = $field;
        }
        return $fields;
    }
    function safesql($source) {
        if (!$this->db_id) $this->connect(DBUSER, DBPASS, DBNAME, DBHOST);
        return mysqli_real_escape_string($this->db_id, $source);
    }
    function free($query_id = '') {
        if ($query_id == '') $query_id = $this->query_id;
        @mysqli_free_result($query_id);
    }
    function close() {
        @mysqli_close($this->db_id);
    }
    function get_real_time() {
        list($seconds, $microSeconds) = explode(' ', microtime());
        return ((float)$seconds + (float)$microSeconds);
    }
    function display_error($error, $error_num, $query = '') {
        echo '<?xml version="1.0" encoding="iso-8859-1"?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<title>Ошибка</title>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
		</head>
		<body>
			<font size="4">Ошибка сервера, попробуйте обновить страницу позже. ' . $query . ' ' . $error_num . '</font> 
		</body>
		</html>';
        exit();
    }
}
?>