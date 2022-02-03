<?php
/***************************************************************************\
| Sypex Dumper Lite          version 1.0.8b                                 |
| (c)2003-2006 zapimir       zapimir@zapimir.net       http://sypex.net/    |
| (c)2005-2006 BINOVATOR     info@sypex.net                                 |
|---------------------------------------------------------------------------|
|     created: 2003.09.02 19:07              modified: 2006.10.27 03:30     |
|---------------------------------------------------------------------------|
\***************************************************************************/
if (!defined('MOZG')) die('Hacking attempt!');
header('Content-type: text/html; charset=utf-8');
$lang = array('dumper_1' => "Бэкап БД успешно создан в папке /backup/", 'dumper_2' => "Создается резервная копия БД", 'dumper_3' => "ОШИБКА! Не указана база данных!", 'dumper_4' => "Не удается выбрать базу данных.<br />", 'dumper_5' => "Создание файла с резервной копией БД:", 'dumper_6' => "Не удается изменить кодировку соединения.<br />", 'dumper_7' => "Установлена кодировка соединения", 'dumper_8' => "Кодировка соединения и таблицы не совпадает:", 'dumper_9' => "Таблица", 'dumper_10' => "соединение", 'dumper_11' => "Обработка таблицы", 'dumper_12' => "Резервная копия БД", 'dumper_13' => "создана.", 'dumper_14' => "Размер БД:", 'dumper_15' => "Размер файла:", 'dumper_16' => "Таблиц обработано:", 'dumper_17' => "Строк обработано:", 'dumper_18' => "Восстановление БД из резервной копии", 'dumper_19' => "ОШИБКА! Не указана база данных!", 'dumper_20' => "Подключение к БД", 'dumper_21' => "ОШИБКА! Файл не найден!", 'dumper_22' => "Чтение файла", 'dumper_23' => "Неправильный запрос.", 'dumper_24' => "БД восстановлена из резервной копии.", 'dumper_25' => "Дата создания копии:", 'dumper_26' => "Запросов к БД:", 'dumper_27' => "Таблиц создано:", 'dumper_28' => "Строк добавлено:", 'dumper_29' => "Статус таблицы:", 'dumper_30' => "Общий статус:", 'dumper_31' => "Возникла ошибка!");
if ($user_info['user_group'] != 1) {
    msgbox("error", 'error', 'error');
}
ob_end_flush();
define('PATH', ROOT_DIR . '/backup/');
define('URL', 'backup/');
define('TIME_LIMIT', 600);
define('LIMIT', 1);
define('DBNAMES', DBNAME);
define('DBNUSER', DBUSER);
define('DBPREFIX', PREFIX);
// Кодировка соединения с MySQL
// auto - автоматический выбор (устанавливается кодировка таблицы), cp1251 - windows-1251, и т.п.
define('CHARSET', 'auto');
// Кодировка соединения с MySQL при восстановлении
// На случай переноса со старых версий MySQL (до 4.1), у которых не указана кодировка таблиц в дампе
// При добавлении 'forced->', к примеру 'forced->cp1251', кодировка таблиц при восстановлении будет принудительно заменена на cp1251
// Можно также указывать сравнение нужное к примеру 'cp1251_ukrainian_ci' или 'forced->cp1251_ukrainian_ci'
define('RESTORE_CHARSET', 'cp1251');
define('SC', 0);
// Типы таблиц у которых сохраняется только структура, разделенные запятой
define('ONLY_CREATE', 'MRG_MyISAM,MERGE,HEAP,MEMORY');
$is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
if (!$is_safe_mode && function_exists('set_time_limit')) @set_time_limit(TIME_LIMIT);
$timer = array_sum(explode(' ', microtime()));
ob_implicit_flush();
$auth = 0;
$error = '';
if (@mysql_connect(DBHOST, DBNUSER, DBPASS)) {
    $auth = 1;
} else {
    $error = '#' . mysql_errno() . ': ' . mysql_error();
}
if (!file_exists(PATH)) {
    @mkdir(PATH, 0777) || die("Не удалось создать каталог для бекапа");
    @chmod(PATH, 0777);
}
$SK = new dumper();
define('C_DEFAULT', 1);
define('C_RESULT', 2);
define('C_ERROR', 3);
define('C_WARNING', 4);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($action) {
    case 'backup':
        $SK->backup();
    break;
    case 'restore':
        $SK->restore();
    break;
    default:
        $SK->main();
}
mysql_close();
if (!defined('AUTOMODE')) {
    echo "<SCRIPT>document.getElementById('timer').innerHTML = '" . round(array_sum(explode(' ', microtime())) - $timer, 4) . " sec.'</SCRIPT>";
}
class dumper {
    function mres($value) {
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
        return str_replace($search, $replace, $value);
    }
    function dumper() {
        $this->SET['last_action'] = 0;
        $this->SET['last_db_backup'] = '';
        $this->SET['tables'] = '';
        $this->SET['comp_method'] = 2;
        $this->SET['comp_level'] = 7;
        $this->SET['last_db_restore'] = '';
        $this->tabs = 0;
        $this->records = 0;
        $this->size = 0;
        $this->comp = 0;
        // Версия MySQL вида 40101
        preg_match("/^(\d+)\.(\d+)\.(\d+)/", mysql_get_server_info(), $m);
        $this->mysql_version = sprintf("%d%02d%02d", $m[1], $m[2], $m[3]);
        $this->only_create = explode(',', ONLY_CREATE);
        $this->forced_charset = false;
        $this->restore_charset = $this->restore_collate = '';
        if (preg_match("/^(forced->)?(([a-z0-9]+)(\_\w+)?)$/", RESTORE_CHARSET, $matches)) {
            $this->forced_charset = $matches[1] == 'forced->';
            $this->restore_charset = $matches[3];
            $this->restore_collate = !empty($matches[4]) ? ' COLLATE ' . $matches[2] : '';
        }
    }
    function backup() {
        global $lang;
        if (!isset($_POST['comp_method'])) $_POST['comp_method'] = $_GET['comp_method'];
        @set_error_handler("SXD_errorHandler", E_ALL ^ E_NOTICE);
        $buttons = "<span ID=save STYLE='display: none;'>{$lang['dumper_1']}</span>";
        echo tpl_page(tpl_process($lang['dumper_2']), $buttons);
        $this->SET['last_action'] = 0;
        $this->SET['last_db_backup'] = DBNAMES;
        $this->SET['tables_exclude'] = 0;
        $this->SET['tables'] = DBPREFIX . '*';
        $this->SET['comp_method'] = isset($_POST['comp_method']) ? intval($_POST['comp_method']) : 0;
        $this->SET['comp_level'] = 5;
        $this->SET['tables'] = explode(",", $this->SET['tables']);
        foreach ($this->SET['tables'] AS $table) {
            $table = preg_replace("/[^\w*?^]/", "", $table);
            $pattern = array("/\?/", "/\*/");
            $replace = array(".", ".*?");
            $tbls[] = preg_replace($pattern, $replace, $table);
        }
        if ($this->SET['comp_level'] == 0) {
            $this->SET['comp_method'] = 0;
        }
        $db = $this->SET['last_db_backup'];
        if (!$db) {
            echo tpl_l($lang['dumper_3'], C_ERROR);
            exit;
        }
        echo tpl_l("{$lang['dumper_20']} `{$db}`.");
        mysql_select_db($db) or trigger_error($lang['dumper_4'] . mysql_error(), E_USER_ERROR);
        $tables = array();
        $result = mysql_query("SHOW TABLES");
        $all = 0;
        while ($row = mysql_fetch_array($result)) {
            $status = 0;
            if (!empty($tbls)) {
                foreach ($tbls AS $table) {
                    $exclude = preg_match("/^\^/", $table) ? true : false;
                    if (!$exclude) {
                        if (preg_match("/^{$table}$/i", $row[0])) {
                            $status = 1;
                        }
                        $all = 1;
                    }
                    if ($exclude && preg_match("/{$table}$/i", $row[0])) {
                        $status = - 1;
                    }
                }
            } else {
                $status = 1;
            }
            if ($status >= $all) {
                $tables[] = $row[0];
            }
        }
        $tabs = count($tables);
        // Определение размеров таблиц
        $result = mysql_query("SHOW TABLE STATUS");
        $tabinfo = array();
        $tab_charset = array();
        $tab_type = array();
        $tabinfo[0] = 0;
        $info = '';
        while ($item = mysql_fetch_assoc($result)) {
            //print_r($item);
            if (in_array($item['Name'], $tables)) {
                $item['Rows'] = empty($item['Rows']) ? 0 : $item['Rows'];
                $tabinfo[0]+= $item['Rows'];
                $tabinfo[$item['Name']] = $item['Rows'];
                $this->size+= $item['Data_length'];
                $tabsize[$item['Name']] = 1 + round(LIMIT * 1048576 / ($item['Avg_row_length'] + 1));
                if ($item['Rows']) $info.= "|" . $item['Rows'];
                if (!empty($item['Collation']) && preg_match("/^([a-z0-9]+)_/i", $item['Collation'], $m)) {
                    $tab_charset[$item['Name']] = $m[1];
                }
                $tab_type[$item['Name']] = isset($item['Engine']) ? $item['Engine'] : $item['Type'];
            }
        }
        $show = 10 + $tabinfo[0] / 50;
        $info = $tabinfo[0] . $info;
        $salt = "abchefghjkmnpqrstuvwxyz0123456789";
        // srand((double)microtime()*1000000);
        $rand = "";
        for ($i = 0;$i < 9;$i++) {
            $rand.= $salt{rand(0, 33) };
        }
        if (!defined('AUTOMODE')) {
            $name = $db . '_' . date("Y-m-d_H-i") . '_' . substr(md5(date("Y-m-d_H-i") . DBHOST . DBNAME), 0, 5);
        } else {
            $name = date("Y-m-d_H-i") . '_' . $db . '_' . md5($rand);
        }
        $fp = $this->fn_open($name, "w");
        echo tpl_l($lang['dumper_5']);
        $this->fn_write($fp, "#SKD101|{$db}|{$tabs}|" . date("Y.m.d H:i:s") . "|{$info}\n\n");
        $t = 0;
        echo tpl_l(str_repeat("-", 60));
        $result = mysql_query("SET SQL_QUOTE_SHOW_CREATE = 1");
        // Кодировка соединения по умолчанию
        if ($this->mysql_version > 40101 && CHARSET != 'auto') {
            mysql_query("SET NAMES '" . CHARSET . "'") or trigger_error($lang['dumper_6'] . mysql_error(), E_USER_ERROR);
            $last_charset = CHARSET;
        } else {
            $last_charset = '';
        }
        foreach ($tables AS $table) {
            // Выставляем кодировку соединения соответствующую кодировке таблицы
            if ($this->mysql_version > 40101 && $tab_charset[$table] != $last_charset) {
                if (CHARSET == 'auto') {
                    mysql_query("SET NAMES '" . $tab_charset[$table] . "'") or trigger_error($lang['dumper_6'] . mysql_error(), E_USER_ERROR);
                    echo tpl_l("{$lang['dumper_7']} `" . $tab_charset[$table] . "`.", C_WARNING);
                    $last_charset = $tab_charset[$table];
                } else {
                    echo tpl_l($lang['dumper_8'], C_ERROR);
                    echo tpl_l($lang['dumper_9'] . ' `' . $table . '` -> ' . $tab_charset[$table] . ' (' . $lang['dumper_10'] . ' ' . CHARSET . ')', C_ERROR);
                }
            }
            echo tpl_l("{$lang['dumper_11']} `{$table}` [" . fn_int($tabinfo[$table]) . "].");
            // Создание таблицы
            $result = mysql_query("SHOW CREATE TABLE `{$table}`");
            $tab = mysql_fetch_array($result);
            $tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
            $this->fn_write($fp, "DROP TABLE IF EXISTS `{$table}`;\n{$tab[1]};\n\n");
            // Проверяем нужно ли дампить данные
            if (in_array($tab_type[$table], $this->only_create)) {
                continue;
            }
            // Опредеделяем типы столбцов
            $NumericColumn = array();
            $result = mysql_query("SHOW COLUMNS FROM `{$table}`");
            $field = 0;
            while ($col = mysql_fetch_row($result)) {
                $NumericColumn[$field++] = preg_match("/^(\w*int|year)/", $col[1]) ? 1 : 0;
            }
            $fields = $field;
            $from = 0;
            $limit = $tabsize[$table];
            $limit2 = round($limit / 3);
            if ($tabinfo[$table] > 0) {
                if ($tabinfo[$table] > $limit2) {
                    echo tpl_s(0, $t / $tabinfo[0]);
                }
                $i = 0;
                $this->fn_write($fp, "INSERT INTO `{$table}` VALUES");
                while (($result = mysql_query("SELECT * FROM `{$table}` LIMIT {$from}, {$limit}")) && ($total = mysql_num_rows($result))) {
                    while ($row = mysql_fetch_row($result)) {
                        $i++;
                        $t++;
                        for ($k = 0;$k < $fields;$k++) {
                            if ($NumericColumn[$k]) $row[$k] = isset($row[$k]) ? $row[$k] : "NULL";
                            else $row[$k] = isset($row[$k]) ? "'" . $this->mres($row[$k]) . "'" : "NULL";
                        }
                        $this->fn_write($fp, ($i == 1 ? "" : ",") . "\n(" . implode(", ", $row) . ")");
                        if ($i % $limit2 == 0) echo tpl_s($i / $tabinfo[$table], $t / $tabinfo[0]);
                    }
                    mysql_free_result($result);
                    if ($total < $limit) {
                        break;
                    }
                    $from+= $limit;
                }
                $this->fn_write($fp, ";\n\n");
                echo tpl_s(1, $t / $tabinfo[0]);
            }
        }
        $this->tabs = $tabs;
        $this->records = $tabinfo[0];
        $this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
        echo tpl_s(1, 1);
        echo tpl_l(str_repeat("-", 60));
        $this->fn_close($fp);
        echo tpl_l("{$lang['dumper_12']} `{$db}` {$lang['dumper_13']}", C_RESULT);
        echo tpl_l("{$lang['dumper_14']}       " . round($this->size / 1048576, 2) . " MB", C_RESULT);
        $filesize = round(filesize(PATH . $this->filename) / 1048576, 2) . " MB";
        echo tpl_l("{$lang['dumper_15']} {$filesize}", C_RESULT);
        echo tpl_l("{$lang['dumper_16']} {$tabs}", C_RESULT);
        echo tpl_l("{$lang['dumper_17']}   " . fn_int($tabinfo[0]), C_RESULT);
        if (!defined('AUTOMODE')) {
            echo "<SCRIPT>if (document.getElementById('save')) {document.getElementById('save').style.display = ''; }</SCRIPT>";
        }
    }
    function restore() {
        global $config, $lang;
        if (!isset($_POST['file'])) $_POST['file'] = $_GET['file'];
        @set_error_handler("SXD_errorHandler", E_ALL ^ E_NOTICE);
        $buttons = "";
        echo tpl_page(tpl_process($lang['dumper_18']), $buttons);
        $this->SET['last_action'] = 1;
        $this->SET['last_db_restore'] = DBNAMES;
        $file = isset($_POST['file']) ? $_POST['file'] : '';
        $file = str_replace("\\", "/", $file);
        $file = str_replace("..", "", $file);
        $file = str_replace("/", "", $file);
        if (stripos($file, "php") !== false) die("Hacking attempt!");
        $db = $this->SET['last_db_restore'];
        if (!$db) {
            echo tpl_l($lang['dumper_19'], C_ERROR);
            exit;
        }
        echo tpl_l("{$lang['dumper_20']} `{$db}`.");
        mysql_select_db($db) or trigger_error($lang['dumper_4'] . mysql_error(), E_USER_ERROR);
        // Определение формата файла
        if (preg_match("/^(.+?)\.sql(\.(bz2|gz))?$/", $file, $matches)) {
            if (isset($matches[3]) && $matches[3] == 'bz2') {
                $this->SET['comp_method'] = 2;
            } elseif (isset($matches[2]) && $matches[3] == 'gz') {
                $this->SET['comp_method'] = 1;
            } else {
                $this->SET['comp_method'] = 0;
            }
            $this->SET['comp_level'] = '';
            if (!file_exists(PATH . "/{$file}")) {
                echo tpl_l($lang['dumper_21'], C_ERROR);
                exit;
            }
            echo tpl_l("{$lang['dumper_22']} `{$file}`.");
            $file = $matches[1];
        } else {
            echo tpl_l($lang['dumper_21'], C_ERROR);
            exit;
        }
        echo tpl_l(str_repeat("-", 60));
        $fp = $this->fn_open($file, "r");
        $this->file_cache = $sql = $table = $insert = '';
        $is_skd = $query_len = $execute = $q = $t = $i = $aff_rows = 0;
        $limit = 300;
        $index = 4;
        $tabs = 0;
        $cache = '';
        $info = array();
        $convert = false;
        // Установка кодировки соединения
        if ($this->mysql_version > 40101 && (CHARSET != 'auto' || $this->forced_charset)) { // Кодировка по умолчанию, если в дампе не указана кодировка
            mysql_query("SET NAMES '" . $this->restore_charset . "'") or trigger_error($lang['dumper_6'] . mysql_error(), E_USER_ERROR);
            echo tpl_l("{$lang['dumper_7']} `" . $this->restore_charset . "`.", C_WARNING);
            $last_charset = $this->restore_charset;
        } else {
            $last_charset = '';
        }
        $last_showed = '';
        while (($str = $this->fn_read_str($fp)) !== false) {
            if (empty($str) || preg_match("/^(#|--)/", $str)) {
                if (!$is_skd && preg_match("/^#SKD101\|/", $str)) {
                    $info = explode("|", $str);
                    echo tpl_s(0, $t / $info[4]);
                    $is_skd = 1;
                }
                continue;
            }
            $query_len+= strlen($str);
            if (!$insert && preg_match("/^(INSERT INTO `?([^` ]+)`? .*?VALUES)(.*)$/i", $str, $m)) {
                if ($table != $m[2]) {
                    $table = $m[2];
                    $tabs++;
                    $cache.= tpl_l("Таблица `{$table}`.");
                    $last_showed = $table;
                    $i = 0;
                    if ($is_skd) echo tpl_s(100, $t / $info[4]);
                }
                $insert = $m[1] . ' ';
                $sql.= $m[3];
                $index++;
                $info[$index] = isset($info[$index]) ? $info[$index] : 0;
                $limit = round($info[$index] / 20);
                $limit = $limit < 300 ? 300 : $limit;
                if ($info[$index] > $limit) {
                    echo $cache;
                    $cache = '';
                    echo tpl_s(0 / $info[$index], $t / $info[4]);
                }
            } else {
                $sql.= $str;
                if ($insert) {
                    $i++;
                    $t++;
                    if ($is_skd && $info[$index] > $limit && $t % $limit == 0) {
                        echo tpl_s($i / $info[$index], $t / $info[4]);
                    }
                }
            }
            if (!$insert && preg_match("/^CREATE TABLE (IF NOT EXISTS )?`?([^` ]+)`?/i", $str, $m) && $table != $m[2]) {
                $table = $m[2];
                $insert = '';
                $tabs++;
                $is_create = true;
                $i = 0;
            }
            if ($sql) {
                if (preg_match("/;$/", $str)) {
                    $sql = rtrim($insert . $sql, ";");
                    if (empty($insert)) {
                        if ($this->mysql_version < 40101) {
                            $sql = preg_replace("/ENGINE\s?=/", "TYPE=", $sql);
                        } elseif (preg_match("/CREATE TABLE/i", $sql)) {
                            // Выставляем кодировку соединения
                            if (preg_match("/(CHARACTER SET|CHARSET)[=\s]+(\w+)/i", $sql, $charset)) {
                                if (!$this->forced_charset && $charset[2] != $last_charset) {
                                    if (CHARSET == 'auto') {
                                        if ($config['charset'] == "utf-8" AND $charset[2] == "cp1251") {
                                            $convert = true;
                                            $charset[2] = "utf8";
                                            $this->restore_charset = "utf8";
                                        }
                                        mysql_query("SET NAMES '" . $charset[2] . "'") or trigger_error("{$lang['dumper_6']}{$sql}<BR>" . mysql_error(), E_USER_ERROR);
                                        $cache.= tpl_l("{$lang['dumper_7']} `" . $charset[2] . "`.", C_WARNING);
                                        $last_charset = $charset[2];
                                    } else {
                                        $cache.= tpl_l($lang['dumper_8'], C_ERROR);
                                        $cache.= tpl_l($lang['dumper_9'] . ' `' . $table . '` -> ' . $charset[2] . ' (' . $lang['dumper_10'] . ' ' . $this->restore_charset . ')', C_ERROR);
                                    }
                                }
                                // Меняем кодировку если указано форсировать кодировку
                                if ($this->forced_charset OR $convert) {
                                    $sql = preg_replace("/(\/\*!\d+\s)?((COLLATE)[=\s]+)\w+(\s+\*\/)?/i", '', $sql);
                                    $sql = preg_replace("/((CHARACTER SET|CHARSET)[=\s]+)\w+/i", "\\1" . $this->restore_charset . $this->restore_collate, $sql);
                                }
                            } elseif (CHARSET == 'auto') { // Вставляем кодировку для таблиц, если она не указана и установлена auto кодировка
                                $sql.= ' DEFAULT CHARSET=' . $this->restore_charset . $this->restore_collate;
                                if ($this->restore_charset != $last_charset) {
                                    mysql_query("SET NAMES '" . $this->restore_charset . "'") or trigger_error("{$lang['dumper_6']}{$sql}<BR>" . mysql_error(), E_USER_ERROR);
                                    $cache.= tpl_l("{$lang['dumper_7']} `" . $this->restore_charset . "`.", C_WARNING);
                                    $last_charset = $this->restore_charset;
                                }
                            }
                        }
                        if ($last_showed != $table) {
                            $cache.= tpl_l("{$lang['dumper_9']} `{$table}`.");
                            $last_showed = $table;
                        }
                    } elseif ($this->mysql_version > 40101 && empty($last_charset)) { // Устанавливаем кодировку на случай если отсутствует CREATE TABLE
                        mysql_query("SET $this->restore_charset '" . $this->restore_charset . "'") or trigger_error("{$lang['dumper_6']}{$sql}<BR>" . mysql_error(), E_USER_ERROR);
                        echo tpl_l("{$lang['dumper_7']} `" . $this->restore_charset . "`.", C_WARNING);
                        $last_charset = $this->restore_charset;
                    }
                    $insert = '';
                    $execute = 1;
                }
                if ($query_len >= 65536 && preg_match("/,$/", $str)) {
                    $sql = rtrim($insert . $sql, ",");
                    $execute = 1;
                }
                if ($execute) {
                    $q++;
                    if ($convert) $sql = iconv('WINDOWS-1251', 'UTF-8//IGNORE', $sql);
                    mysql_query($sql) or trigger_error($lang['dumper_23'] . mysql_error(), E_USER_ERROR);
                    if (preg_match("/^insert/i", $sql)) {
                        $aff_rows+= mysql_affected_rows();
                    }
                    $sql = '';
                    $query_len = 0;
                    $execute = 0;
                }
            }
        }
        echo $cache;
        echo tpl_s(1, 1);
        echo tpl_l(str_repeat("-", 60));
        echo tpl_l($lang['dumper_24'], C_RESULT);
        if (isset($info[3])) echo tpl_l("{$lang['dumper_25']} {$info[3]}", C_RESULT);
        echo tpl_l("{$lang['dumper_26']} {$q}", C_RESULT);
        echo tpl_l("{$lang['dumper_27']} {$tabs}", C_RESULT);
        echo tpl_l("{$lang['dumper_28']} {$aff_rows}", C_RESULT);
        $this->tabs = $tabs;
        $this->records = $aff_rows;
        $this->size = filesize(PATH . $this->filename);
        $this->comp = $this->SET['comp_method'] * 10 + $this->SET['comp_level'];
        $this->fn_close($fp);
    }
    function main() {
        die("Hacking attempt!");
    }
    function db_select() {
        if (DBNAMES != '') {
            $items = explode(',', trim(DBNAMES));
            foreach ($items AS $item) {
                if (mysql_select_db($item)) {
                    $tables = mysql_query("SHOW TABLES");
                    if ($tables) {
                        $tabs = mysql_num_rows($tables);
                        $dbs[$item] = "{$item} ({$tabs})";
                    }
                }
            }
        } else {
            $result = mysql_query("SHOW DATABASES");
            $dbs = array();
            while ($item = mysql_fetch_array($result)) {
                if (mysql_select_db($item[0])) {
                    $tables = mysql_query("SHOW TABLES");
                    if ($tables) {
                        $tabs = mysql_num_rows($tables);
                        $dbs[$item[0]] = "{$item[0]} ({$tabs})";
                    }
                }
            }
        }
        return $dbs;
    }
    function file_select() {
        $files = array('' => ' ');
        if (is_dir(PATH) && $handle = opendir(PATH)) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match("/^.+?\.sql(\.(gz|bz2))?$/", $file)) {
                    $files[$file] = $file;
                }
            }
            closedir($handle);
        }
        ksort($files);
        return $files;
    }
    function fn_open($name, $mode) {
        if ($this->SET['comp_method'] == 2) {
            $this->filename = "{$name}.sql.bz2";
            return bzopen(PATH . $this->filename, "{$mode}");
        } elseif ($this->SET['comp_method'] == 1) {
            $this->filename = "{$name}.sql.gz";
            return gzopen(PATH . $this->filename, "{$mode}b{$this->SET['comp_level']}");
        } else {
            $this->filename = "{$name}.sql";
            return fopen(PATH . $this->filename, "{$mode}b");
        }
    }
    function fn_write($fp, $str) {
        if ($this->SET['comp_method'] == 2) {
            bzwrite($fp, $str);
        } elseif ($this->SET['comp_method'] == 1) {
            gzwrite($fp, $str);
        } else {
            fwrite($fp, $str);
        }
    }
    function fn_read($fp) {
        if ($this->SET['comp_method'] == 2) {
            return bzread($fp, 4096);
        } elseif ($this->SET['comp_method'] == 1) {
            return gzread($fp, 4096);
        } else {
            return fread($fp, 4096);
        }
    }
    function fn_read_str($fp) {
        $string = '';
        $this->file_cache = ltrim($this->file_cache);
        $pos = strpos($this->file_cache, "\n", 0);
        if ($pos < 1) {
            while (!$string && ($str = $this->fn_read($fp))) {
                $pos = strpos($str, "\n", 0);
                if ($pos === false) {
                    $this->file_cache.= $str;
                } else {
                    $string = $this->file_cache . substr($str, 0, $pos);
                    $this->file_cache = substr($str, $pos + 1);
                }
            }
            if (!$str) {
                if ($this->file_cache) {
                    $string = $this->file_cache;
                    $this->file_cache = '';
                    return trim($string);
                }
                return false;
            }
        } else {
            $string = substr($this->file_cache, 0, $pos);
            $this->file_cache = substr($this->file_cache, $pos + 1);
        }
        return trim($string);
    }
    function fn_close($fp) {
        if ($this->SET['comp_method'] == 2) {
            bzclose($fp);
        } elseif ($this->SET['comp_method'] == 1) {
            gzclose($fp);
        } else {
            fclose($fp);
        }
        @chmod(PATH . $this->filename, 0666);
        $this->fn_index();
    }
    function fn_select($items, $selected) {
        $select = '';
        foreach ($items AS $key => $value) {
            $select.= $key == $selected ? "<OPTION VALUE='{$key}' SELECTED>{$value}" : "<OPTION VALUE='{$key}'>{$value}";
        }
        return $select;
    }
    function fn_save() {
        return;
    }
    function fn_index() {
        if (!file_exists(PATH . 'index.html')) {
            $fh = fopen(PATH . 'index.html', 'wb');
            fwrite($fh, tpl_backup_index());
            fclose($fh);
        }
    }
}
function fn_int($num) {
    return number_format($num, 0, ',', ' ');
}
function fn_arr2str($array) {
    $str = "array(\n";
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $str.= "'$key' => " . fn_arr2str($value) . ",\n\n";
        } else {
            $str.= "'$key' => '" . str_replace("'", "\'", $value) . "',\n";
        }
    }
    return $str . ")";
}
// Шаблоны
function tpl_page($content = '', $buttons = '') {
    global $config;
    if (defined('AUTOMODE')) {
        return;
    }
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<META HTTP-EQUIV=Content-Type CONTENT="text/html; charset={$config['charset']}">
<meta http-equiv="X-UA-Compatible" content="IE=7" />

<STYLE TYPE="TEXT/CSS">
<!--
body{
	overflow: auto;
}
form {
margin:0px;
padding: 0px;
}

table{
border:0px;
border-collapse:collapse;
}

table td{
padding:0px;
font-size: 11px;
font-family: tahoma;
}

input, select, div {
	font: 11px tahoma, verdana, arial;
}
input.text, select {
	width: 100%;
}
fieldset {
	margin-bottom: 10px;
}
.unterline {
	background: url(engine/skins/images/line_bg.gif);
	width: 100%;
	height: 9px;
	font-size: 3px;
	font-family: tahoma;
	margin-bottom: 4px;
}
.hr_line {
	background: url(engine/skins/images/line.gif);
	width: 100%;
	height: 7px;
	font-size: 3px;
	font-family: tahoma;
	margin-top: 4px;
	margin-bottom: 4px;
}
-->
</STYLE>
</head>
<body>

<table width="100%">
    <tr>
        <td>
<TD VALIGN=TOP STYLE="padding: 8px 8px;">
{$content}
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD STYLE='color: #CECECE' ID=timer></TD>
<TD ALIGN=RIGHT>{$buttons}</TD>
</TR>
</TABLE></TD>
</td>
    </tr>
</table>



</body>
</HTML>
HTML;
    
}
function tpl_process($title) {
    global $lang;
    if (defined('AUTOMODE')) {
        return;
    }
    return <<<HTML
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR><TD COLSPAN=2 style="padding:2px;"><DIV ID=logarea STYLE="width: 99%; height: 140px; border: 1px solid #7F9DB9; padding: 3px; overflow: auto;"></DIV></TD></TR>
<TR><TD WIDTH=31% style="padding:2px; width:100px;">{$lang['dumper_29']}</TD><TD><TABLE WIDTH=100% style="border: 1px solid #7F9DB9;" CELLPADDING=0 CELLSPACING=0>
<TR><TD BGCOLOR=#FFFFFF><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#5555CC ID=st_tab
STYLE="background: #5c9ccc url(system/inc/images/ui-bg_gloss-wave_55_5c9ccc_500x100.png) 50% 50% repeat-x;";
border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD></TR></TABLE></TD></TR>
<TR><TD style="padding:2px; width:100px;">{$lang['dumper_30']}</TD><TD><TABLE WIDTH=100% style="border: 1px solid #7F9DB9;" CELLSPACING=0 CELLPADDING=0>
<TR><TD BGCOLOR=#FFFFFF><TABLE WIDTH=1 BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#00AA00 ID=so_tab
STYLE="background: #5c9ccc url(system/inc/images/ui-bg_gloss-wave_55_5c9ccc_500x100.png) 50% 50% repeat-x;";
border-right: 1px solid #AAAAAA"><TR><TD HEIGHT=12></TD></TR></TABLE></TD>
</TR></TABLE></TD></TR></TABLE>
<SCRIPT>
var WidthLocked = false;
function s(st, so){
	document.getElementById('st_tab').width = st ? st + '%' : '1';
	document.getElementById('so_tab').width = so ? so + '%' : '1';
}
function l(str, color){
	switch(color){
		case 2: color = 'navy'; break;
		case 3: color = 'red'; break;
		case 4: color = 'maroon'; break;
		default: color = 'black';
	}
	with(document.getElementById('logarea')){
		if (!WidthLocked){
			style.width = clientWidth;
			WidthLocked = true;
		}
		str = '<FONT COLOR=' + color + '>' + str + '</FONT>';
		innerHTML += innerHTML ? "<BR>\\n" + str : str;
		scrollTop += 14;
	}
}
</SCRIPT>
HTML;
    
}
function tpl_l($str, $color = C_DEFAULT) {
    if (defined('AUTOMODE')) {
        return;
    }
    $str = preg_replace("/\s{2}/", " &nbsp;", $str);
    return <<<HTML
<SCRIPT>l('{$str}', $color);</SCRIPT>

HTML;
    
}
function tpl_s($st, $so) {
    if (defined('AUTOMODE')) {
        return;
    }
    $st = round($st * 100);
    $st = $st > 100 ? 100 : $st;
    $so = round($so * 100);
    $so = $so > 100 ? 100 : $so;
    return <<<HTML
<SCRIPT>s({$st},{$so});</SCRIPT>

HTML;
    
}
function tpl_backup_index() {
    if (defined('AUTOMODE')) {
        return;
    }
    return <<<HTML
<CENTER>
<H1>access denied</H1>
</CENTER>

HTML;
    
}
function tpl_error($error) {
    if (defined('AUTOMODE')) {
        return;
    }
    return <<<HTML
<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR>
<TD ALIGN=center>{$error}</TD>
</TR>
</TABLE>
HTML;
    
}
function SXD_errorHandler($errno, $errmsg, $filename, $linenum, $vars) {
    global $lang;
    if ($errno == 2048) return true;
    if (strpos($errmsg, "chmod():") !== false) return true;
    $dt = date("Y.m.d H:i:s");
    $errmsg = addslashes($errmsg);
    echo tpl_l("{$dt}<BR><B>{$lang['dumper_31']}</B>", C_ERROR);
    echo tpl_l("{$errmsg} ({$errno})", C_ERROR);
    die();
}
?>