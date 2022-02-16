<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
header('Content-type: text/html; charset=utf-8');
const MOZG = true;
define('ROOT_DIR', dirname(__FILE__));
const ENGINE_DIR = ROOT_DIR . '/system';

include './system/classes/Filesystem.php';
include './system/functions.php';

echo <<<HTML
<!DOCTYPE>
<html lang="ru">
<head>
<title>Vii Engine - Установка</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<style type="text/css" media="all">
html,body{font-size:11px;font-family:Tahoma;line-height:17px;background: url("templates/Default/images/bg.png") repeat-x;}
a{color:#4274a5;text-decoration:underline}
a:hover{color:#4274a5;text-decoration:none}
.box {position: absolute;right: 0;left: 0;top: 5%;width: 800px;margin: auto;background: #fff;box-shadow: 0px 1px 4px 1px #cfcfcf;-moz-box-shadow: 0px 1px 4px 1px #cfcfcf;-webkit-box-shadow: 0px 1px 4px 1px #cfcfcf;-khtml-box-shadow: 0px 1px 4px 1px #cfcfcf;padding: 10px;border-radius: 5px;-moz-border-radius: 5px;-webkit-border-radius: 5px;-khtml-border-radius: 5px;margin-bottom: 50px;}
.head{background:url("/system/inc/images/head.png") repeat-x;height:49px;border-top-left-radius:5px;-moz-top-left-border-radius:5px;-webkit-top-left-border-radius:5px;-khtml-top-left-border-radius:5px;margin:-10px;border-top-right-radius:5px;-moz-top-right-border-radius:5px;-webkit-top-right-border-radius:5px;-khtml-top-right-border-radius:5px;margin:-10px;margin-bottom:5px}
.logo{background:url("/system/inc/images/logo.png") no-repeat;width:133px;height:48px;margin-left:5px}
.h1{font-size:13px;font-weight:bold;color:#4274a5;margin-top:5px;margin-bottom:5px;padding-bottom:2px;border-bottom:1px solid #e5edf5;padding-left:2px}
.clr{clear:both}
.fl_l{float:left}
.fl_r{float:right}
.inp{padding: 5.5px 10px 5.5px 10px; background: -webkit-linear-gradient(top, #b7c42d, #8d991b); background: -moz-linear-gradient(top, #b7c42d, #8d991b); background: -ms-linear-gradient(top, #b7c42d, #8d991b); background: -o-linear-gradient(top, #b7c42d, #8d991b); background: linear-gradient(top, #b7c42d, #8d991b); color: #fff; font-size: 11px; font-family: Tahoma, Verdana, Arial, sans-serif, Lucida Sans; text-shadow: 0px 1px 0px #767f18; border: 0px; border-top: 1px solid #cdd483; cursor: pointer; margin: 0px; font-weight: bold; border-radius: 2px; -moz-border-radius: 2px; -webkit-border-radius: 2px; box-shadow: inset 0px 1px 3px 0px #d2d2d2; -moz-box-shadow: inset 0px 1px 3px 0px #d2d2d2; -webkit-box-shadow: inset 0px 1px 3px 0px #d2d2d2;margin-top: 10px;}
.inp:hover{background:-webkit-linear-gradient(top, #c6d059, #a3ae36);}
.inp:active{background:#848f18;position:relative;border-top:1px solid #727c0e;outline:none}
.inpu{width:200px;box-shadow:inset 0px 1px 3px 0px #d2d2d2;border:1px solid #ccc;padding:4px;border-radius:3px;font-size:11px;font-family:tahoma;margin-bottom:3px;-moz-box-shadow:inset 0px 1px 3px 0px #d2d2d2;-webkit-box-shadow:inset 0px 1px 3px 0px #d2d2d2;}
textarea{width:300px;height:100px;}
.fllogall{color:#555}
</style>
<div class="box clr">
 <a href="/install.php"><div class="head"><div class="logo"></div></div></a>
HTML;

$act = $_GET['act'] ?? null;
if (!file_exists(ENGINE_DIR . '/data/config.php') or !file_exists(ENGINE_DIR . '/data/db.php')) {
    //Проверка на запись у важных файлов системы
    if ($act == 'files') {
        echo '<div class="h1">Проверка на запись у важных файлов системы</div>';

        $important_files = array(
            './system/data/xfields.txt',
            './system/data/',
            './system/cache/',
            './system/cache/system/',
            './system/cache/photos_mark/',
            './system/cache/votes/',
            './system/cache/groups/',
            './system/cache/wall/',
            './system/cache/groups_forum/',
            './backup/',
            './uploads/',
            './uploads/room/',
            './uploads/records/',
            './uploads/attach/',
            './uploads/audio_tmp/',
            './uploads/blog/',
            './uploads/gifts/',
            './uploads/groups/',
            './uploads/users/',
            './uploads/videos/',
            './uploads/audio/',
            './uploads/doc/',
            './uploads/apps/',
            './templates/',
            './templates/Default/',
        );

        try {
            Filesystem::createDir('./uploads/room/');
            Filesystem::createDir('./uploads/records/');
            Filesystem::createDir('./uploads/attach/');
            Filesystem::createDir('./uploads/audio_tmp/');
            Filesystem::createDir('./uploads/blog/');
            Filesystem::createDir('./uploads/groups/');
            Filesystem::createDir('./uploads/users/');
            Filesystem::createDir('./uploads/videos/');
            Filesystem::createDir('./uploads/audio/');
            Filesystem::createDir('./uploads/doc/');
            Filesystem::createDir('./uploads/apps/');

            Filesystem::createDir('./system/cache/');
            Filesystem::createDir('./system/cache/groups/');
            Filesystem::createDir('./system/cache/groups_forum/');
            Filesystem::createDir('./system/cache/groups_mark/');
            Filesystem::createDir('./system/cache/photos_mark/');
            Filesystem::createDir('./system/cache/votes/');
            Filesystem::createDir('./system/cache/wall/');

            Filesystem::createDir('./system/data/');

            Filesystem::createDir('./backup/');

        } catch (Exception $e) {
            echo '<div class="h2">Не удалось создать директории</div>';
        }

        $chmod_errors = 0;
        $not_found_errors = 0;
        echo "<b><div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\">Папка/Файл</div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">CHMOD</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Статус</div>
		<div class=clear></div></b>";

        foreach ($important_files as $file) {
            if (!file_exists($file)) {
                $file_status = "<div style=\"color: red;\">не найден!</div>";
                $not_found_errors++;
            } elseif (is_writable($file)) {
                $file_status = "<div style=\"color: green;\">разрешено</div>";
            } else {
                @chmod($file, 0777);
                if (is_writable($file)) {
                    $file_status = "<div style=\"color: green;\">разрешено</div>";
                } else {
                    @chmod("$file", 0755);
                    if (is_writable($file)) {
                        $file_status = "<div style=\"color: green;\">разрешено</div>";
                    } else {
                        $file_status = "<div style=\"color: red;\">запрещено</div>";
                        $chmod_errors++;
                    }
                }
            }
            $chmod_value = @decoct(@fileperms($file)) % 1000;
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\">{$file}</div>
			<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$chmod_value}</div>
			<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$file_status}</div>
			<div class=clear></div>";
        }
        if ($chmod_errors == 0 and $not_found_errors == 0) {
            $status_report = 'Проверка успешно завершена! Можете продолжить установку!';
        } else {
            $status_report = '';
            if ($chmod_errors > 0) {
                $status_report = "<div style=\"color: red;\">Внимание!!!</div><br /><br />Во время проверки обнаружены ошибки: <b>$chmod_errors</b>. Запрещена запись в файл.<br />Вы должны выставить для папок CHMOD 777, для файлов CHMOD 666, используя ФТП-клиент.<br /><br /><div style=\"color: red;\"><b>Настоятельно не рекомендуется</b></div> продолжать установку, пока не будут произведены изменения.<br />";
            }
            if ($not_found_errors > 0) {
                $status_report .= "<div style=\"color: red;\">Внимание!!!</div><br />Во время проверки обнаружены ошибки: <b>$not_found_errors</b>. Файлы не найдены!<br /><br /><div style=\"color: red;\"><b>Не рекомендуется</b></div> продолжать установку, пока не будут произведены изменения.<br />";
            }
            if (!isset($status_report))
                $status_report = '';
        }

        echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;margin-top:10px"><div style="margin-bottom:7px;"><b>Состояние проверки</b></div>' . $status_report . '</div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=system\'" />
		';
        die();
    } elseif ($act == 'system') {
        echo "<div class=\"h1\">Системные требования</div>
		<b><div style=\"float:left;width:448px;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Требования движка</div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Ваша версия</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Статус</div>
		<div class=clear></div></b>";
        $status = version_compare(PHP_VERSION, '8.0.0') >= 0 ? '<div style="color: green;"><b>Совместимо</b></div>' : '<div style="color: red;"><b>Не совместимо</b></div>';
        echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><b>PHP 8.0</b></div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">" . phpversion() . "</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = function_exists('mysqli_connect') ? '<div style="color: green;"><b>Совместимо</b></div>' : '<div style="color: red;"><b>Не совместимо</b></div>';
        echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><b>Поддержка MySQLi</b></div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">не определяется</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = extension_loaded('zlib') ? '<div style="color: green;"><b>Совместимо</b></div>' : '<div style="color: red;"><b>Не совместимо</b></div>';
        echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><b>Поддержка сжатия ZLib</b></div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">не определяется</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = extension_loaded('xml') ? '<div style="color: green;"><b>Совместимо</b></div>' : '<div style="color: red;"><b>Не совместимо</b></div>';
        echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><b>Поддержка XML</b></div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">не определяется</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = function_exists('iconv') ? '<div style="color: green;"><b>Совместимо</b></div>' : '<div style="color: red;"><b>Не совместимо</b></div>';
        echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><b>Поддержка iconv</b></div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">не определяется</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;"><div style="margin-bottom:7px;text-align: center;font-size: 12px;"><b>Если любой из этих пунктов выделен красным, то выполните действия для исправления положения. <br />В случае несоблюдения минимальных требований скрипта возможна его некорректная работа в системе.</b></div></div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=server\'" />
		';
        die();
    } elseif ($act == 'server') {
        echo "<div class=\"h1\">Настройки сервера</div>
		<b><div style=\"float:left;width:388px;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Рекомендуемые настройки</div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Рекомендуемое значение</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Текущее значение</div>
		<div class=clear></div></b>";
        $status = ini_get('safe_mode') ? '<div style="color: red;"><b>Включено</b></div>' : '<div style="color: green;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Safe Mode</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = ini_get('file_uploads') ? '<div style="color: green;"><b>Включено</b></div>' : '<div style="color: red;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Загрузка файлов</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Включено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = ini_get('output_buffering') ? '<div style="div-weight: bold"><b>Включено</b></div>' : '<div style="color: green;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Буферизация вывода</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = ini_get('magic_quotes_runtime') ? '<div style="color: red;"><b>Включено</b></div>' : '<div style="color: green;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Magic Quotes Runtime</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = ini_get('register_globals') ? '<div style="color: red;"><b>Включено</b></div>' : '<div style="color: green;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Register Globals</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        $status = ini_get('session.auto_start') ? '<div style="color: red;"><b>Включено</b></div>' : '<div style="color: green;"><b>Выключено</b></div>';
        echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><b>Session auto start</b></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
        echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;margin-top:10px"><div style="margin-bottom:7px;text-align: center;font-size: 12px;"><b>Данные настройки являются рекомендуемыми для полной совместимости, однако скрипт способен работать даже если рекомендуемые настройки несовпадают с текущими.</b></div></div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=settings\'" />
		';
        die();
    }
    //Настройка конфигурации системы
    elseif ($act == 'settings') {
        echo <<<HTML
<form method="POST" action="/install.php?act=install">
HTML;
        $url = $_SERVER['HTTP_HOST'];
        echo <<<HTML
<div class="h1">Настройка конфигурации системы</div>
<div class="fllogall">Адрес сайта:</div><input type="text" name="url" class="inpu" value="https://{$url}/" />&nbsp;&nbsp;<span style="color:#777">Укажите путь без имени файла, знак слеша <div color="red">/</div> на конце обязателен</span><div class="mgcler"></div>
HTML;
        echo <<<HTML
<div class="h1" style="margin-top:15px">Данные для доступа к MySQL серверу</div>
<div class="fllogall">Сервер MySQL:</div><input type="text" name="mysql_server" class="inpu" value="localhost" /><div class="mgcler"></div>
<div class="fllogall">Имя базы данных:</div><input type="text" name="mysql_dbname" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Имя пользователя:</div><input type="text" name="mysql_dbuser" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Пароль:</div><input type="text" name="mysql_pass" class="inpu" /><div class="mgcler"></div>
HTML;
        echo <<<HTML
<div class="h1" style="margin-top:15px">Данные для доступа к панели управления</div>
<div class="fllogall">файл админпанели:</div><input type="text" name="adminfile" class="inpu" value="adminpanel.php" /><div class="mgcler"></div>
<div class="fllogall">Имя администратора:</div><input type="text" name="name" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Фамилия администратора:</div><input type="text" name="lastname" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">E-mail:</div><input type="text" name="email" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Пароль:</div><input type="password" name="pass" class="inpu" /><div class="mgcler"></div>
HTML;
        echo <<<HTML
<input type="submit" class="inp fl_r" value="Завершить установку &raquo;" onClick="location.href=\'/install.php?act=settings\'" />
</form>
HTML;
        die();
    }
    //Завершение установки
    elseif ($act == 'install') {
        if (!empty($_POST['mysql_server']) && !empty($_POST['mysql_dbname']) && !empty($_POST['mysql_dbuser']) && !empty($_POST['adminfile']) && !empty($_POST['name']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['pass'])) {
            $_POST['mysql_server'] = str_replace('"', '\"', str_replace("$", "\\$", $_POST['mysql_server']));
            $_POST['mysql_dbname'] = str_replace('"', '\"', str_replace("$", "\\$", $_POST['mysql_dbname']));
            $_POST['mysql_dbuser'] = str_replace('"', '\"', str_replace("$", "\\$", $_POST['mysql_dbuser']));
            $_POST['mysql_pass'] = str_replace('"', '\"', str_replace("$", "\\$", $_POST['mysql_pass']));
            $_POST['mysql_prefix'] = str_replace('"', '\"', str_replace("$", "\\$", $_POST['mysql_prefix']));
            //Создаём файл БД
            $dbconfig = <<<HTML
<?php

const DBHOST = "{$_POST['mysql_server']}"; 

const DBNAME = "{$_POST['mysql_dbname']}";

const DBUSER = "{$_POST['mysql_dbuser']}";

const DBPASS = "{$_POST['mysql_pass']}";

const COLLATE = "utf8";

\$db = new db;

HTML;
            $con_file = fopen("system/data/db.php", "w+") or die("Извините, но невозможно создать файл <b>.system/data/db.php</b>.<br />Проверьте правильность проставленного CHMOD!");
            fwrite($con_file, $dbconfig);
            fclose($con_file);
            @chmod("system/data/db.php", 0666);
            //Создаём файл админ панели
            $admin = <<<HTML
<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
@session_start();
@ob_start();
@ob_implicit_flush(0);

const MOZG = true;
define('ROOT_DIR', dirname (__FILE__));
const ENGINE_DIR = ROOT_DIR . '/system';
const ADMIN_DIR = ROOT_DIR . '/system/inc';
include ENGINE_DIR . '/classes/Registry.php';
include ENGINE_DIR . '/classes/Filesystem.php';
include ADMIN_DIR.'/functions.php';
\$config = settings_get();

\$admin_index = \$config['admin_index'];

\$admin_link = \$config['home_url'].\$admin_index;

include ENGINE_DIR.'/classes/mysql.php';
include ENGINE_DIR.'/data/db.php';
include ADMIN_DIR.'/login.php';

\$db->close();

HTML;
            $con_file = fopen($_POST['adminfile'], "w+") or die("Извините, но невозможно создать файл <b>{$_POST['adminfile']}</b>.<br />Проверьте правильность проставленного CHMOD!");
            fwrite($con_file, $admin);
            fclose($con_file);
            $this_year = date('Y');
            //Создаём файл конфигурации системы
            $config = <<<HTML
<?php

//System Configurations 

return array ( 

'home' => "Социальная сеть", 

'charset' => "utf-8", 

'home_url' => "{$_POST['url']}", 

'admin_index' => "{$_POST['adminfile']}",

'temp' => "Default", 

'online_time' => "150", 

'lang' => "Russian", 

'gzip' => "no", 

'gzip_js' => "no", 

'offline' => "no", 

'offline_msg' => "Сайт находится на текущей реконструкции, после завершения всех работ сайт будет открыт.\r\n\r\nПриносим вам свои извинения за доставленные неудобства.",

'lang_list' => "Русский | Russian", 

'bonus_rate' => "", 

'cost_balance' => "10", 

'video_mod' => "yes", 

'video_mod_comm' => "yes", 

'video_mod_add' => "yes", 

'video_mod_add_my' => "yes", 

'video_mod_search' => "yes", 

'audio_mod' => "yes", 

'audio_mod_add' => "yes", 

'audio_mod_search' => "yes", 

'album_mod' => "yes", 

'max_albums' => "20", 

'max_album_photos' => "500", 

'max_photo_size' => "5000", 

'photo_format' => "jpg, jpeg, jpe, png, gif", 

'albums_drag' => "yes", 

'photos_drag' => "yes", 

'rate_price' => "1", 

'admin_mail' => "{$_POST['email']}", 

'mail_metod' => "php", 

'smtp_host' => "localhost", 

'smtp_port' => "25", 

'smtp_user' => "", 

'smtp_pass' => "", 

'news_mail_1' => "no", 

'news_mail_2' => "no", 

'news_mail_3' => "no", 

'news_mail_4' => "no", 

'news_mail_5' => "no", 

'news_mail_6' => "no", 

'news_mail_7' => "no", 

'news_mail_8' => "no", 

'code_word' => "code_word", 

'sms_number' => "123456", 

);

HTML;
            $con_file = fopen(ROOT_DIR . "/system/data/config.php", "w+") or die("Извините, но невозможно создать файл <b>.system/data/config.php</b>.<br />Проверьте правильность проставленного CHMOD!");
            fwrite($con_file, $config);
            fclose($con_file);
            @chmod("system/data/config.php", 0666);
            include ENGINE_DIR . '/classes/mysql.php';
            include ENGINE_DIR . '/data/db.php';

            $_POST['name'] = strip_tags($_POST['name']);
            $_POST['lastname'] = strip_tags($_POST['lastname']);
            $table_Chema = array();

            include_once ENGINE_DIR . '/data/mysql_tables.php';

            //Вставляем админа в базу
            $_POST['pass'] = md5(md5($_POST['pass']));
            $hid = $_POST['pass'] . md5(md5($_SERVER['REMOTE_ADDR']));

            $server_time = $server_time ?? time();

            $table_Chema[] = "INSERT INTO `users` 
SET user_name = '{$_POST['name']}', 
    user_lastname = '{$_POST['lastname']}', 
    user_email = '{$_POST['email']}', 
    user_password = '{$_POST['pass']}', 
    user_group = 1, 
    user_search_pref = '{$_POST['name']} {$_POST['lastname']}', 
    user_privacy = 'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||', 
    user_hid = '{$hid}',     
    user_birthday = '0-0-0', 
    user_day = '0', 
    user_month = '0', 
    user_year = '0', 
    user_country = '0', 
    user_city = '0', 
    user_lastdate = '{$server_time}', 
    user_lastupdate = '{$server_time}',   
    user_reg_date = '{$server_time}'";
            $table_Chema[] = "INSERT INTO `log` SET uid = '1', browser = '', ip = ''";
            foreach ($table_Chema as $query)
                $db->query($query);
            echo <<<HTML
<div class="h1">Установка успешно завершена</div>
Поздравляем Вас, Vii Engine был успешно установлен на Ваш сервер. Вы можете просмотреть теперь главную <a href="/">страницу вашего сайта</a> и посмотреть возможности скрипта. Либо Вы можете <a href="/{$admin_index}">зайти</a> в панель управления Vii Engine и изменить другие настройки системы. 
<br /><br />
<div style="color: red">Внимание: при установке скрипта создается структура базы данных, создается аккаунт администратора, 
а также прописываются основные настройки системы.</div>
<br /><br />
Приятной Вам работы!
HTML;

        } else
            echo <<<HTML
<div class="h1">Ошибка</div>
Заполните необходимые поля!
<input type="submit" class="inp fl_r" value="Назад" onClick="javascript:history.back()" />
HTML;
        die();
    } elseif ($act == 'remove_installer') {
        Filesystem::delete('./install.php');
        Filesystem::delete('./system/mysql_tables.php');
        header('Location: /');
    } else {
        echo <<<HTML
<div class="h1">Мастер установки скрипта</div>
Добро пожаловать в мастер установки Vii Engine. 
<br /><br />
Данный мастер поможет вам установить скрипт всего за пару минут. 

<div style="color: red">Внимание: при установке скрипта создается структура базы данных, 
создается аккаунт администратора, 
а также прописываются основные настройки системы.
</div>

<br /><br />
Приятной Вам работы!
HTML;
        echo <<<HTML
<input type="submit" class="inp fl_r" value="Начать установку" onClick="location.href='/install.php?act=files'" />
HTML;
    }

} else {

    if ($act == 'clean') {

        Filesystem::delete('./uploads/room/');
        Filesystem::delete('./uploads/records/');
        Filesystem::delete('./uploads/attach/');
        Filesystem::delete('./uploads/audio_tmp/');
        Filesystem::delete('./uploads/blog/');
        Filesystem::delete('./uploads/groups/');
        Filesystem::delete('./uploads/users/');
        Filesystem::delete('./uploads/videos/');
        Filesystem::delete('./uploads/audio/');
        Filesystem::delete('./uploads/doc/');
        Filesystem::delete('./uploads/apps/');
        Filesystem::delete('./system/cache/groups/');
        Filesystem::delete('./system/cache/groups_forum/');
        Filesystem::delete('./system/cache/groups_mark/');
        Filesystem::delete('./system/cache/photos_mark/');
        Filesystem::delete('./system/cache/votes/');
        Filesystem::delete('./system/cache/wall/');

        Filesystem::delete(ROOT_DIR . '/adminpanel.php');

        include ENGINE_DIR . '/classes/mysql.php';
        include ENGINE_DIR . '/data/db.php';

        $table_Chema = array();
        $table_Chema[] = "DROP TABLE IF EXISTS `room`";
        $table_Chema[] = "DROP TABLE IF EXISTS `room_users`";
        $table_Chema[] = "DROP TABLE IF EXISTS `albums`";
        $table_Chema[] = "DROP TABLE IF EXISTS `attach`";
        $table_Chema[] = "DROP TABLE IF EXISTS `antispam`";
        $table_Chema[] = "DROP TABLE IF EXISTS `apps`";
        $table_Chema[] = "DROP TABLE IF EXISTS `apps_transactions`";
        $table_Chema[] = "DROP TABLE IF EXISTS `apps_users`";
        $table_Chema[] = "DROP TABLE IF EXISTS `attach`";
        $table_Chema[] = "DROP TABLE IF EXISTS `attach_comm`";
        $table_Chema[] = "DROP TABLE IF EXISTS `audio`";
        $table_Chema[] = "DROP TABLE IF EXISTS `banned`";
        $table_Chema[] = "DROP TABLE IF EXISTS `blog`";
        $table_Chema[] = "DROP TABLE IF EXISTS `city`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_audio`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_feedback`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_forum`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_forum_msg`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_join`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_stats`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_stats_log`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_wall`";
        $table_Chema[] = "DROP TABLE IF EXISTS `communities_wall_like`";
        $table_Chema[] = "DROP TABLE IF EXISTS `country`";
        $table_Chema[] = "DROP TABLE IF EXISTS `doc`";
        $table_Chema[] = "DROP TABLE IF EXISTS `fave`";
        $table_Chema[] = "DROP TABLE IF EXISTS `friends`";
        $table_Chema[] = "DROP TABLE IF EXISTS `friends_demands`";
        $table_Chema[] = "DROP TABLE IF EXISTS `games`";
        $table_Chema[] = "DROP TABLE IF EXISTS `games_activity`";
        $table_Chema[] = "DROP TABLE IF EXISTS `games_files`";
        $table_Chema[] = "DROP TABLE IF EXISTS `games_users`";
        $table_Chema[] = "DROP TABLE IF EXISTS `gifts`";
        $table_Chema[] = "DROP TABLE IF EXISTS `gifts_list`";
        $table_Chema[] = "DROP TABLE IF EXISTS `guests`";
        $table_Chema[] = "DROP TABLE IF EXISTS `im`";
        $table_Chema[] = "DROP TABLE IF EXISTS `invites`";
        $table_Chema[] = "DROP TABLE IF EXISTS `log`";
        $table_Chema[] = "DROP TABLE IF EXISTS `mail_tpl`";
        $table_Chema[] = "DROP TABLE IF EXISTS `messages`";
        $table_Chema[] = "DROP TABLE IF EXISTS `news`";
        $table_Chema[] = "DROP TABLE IF EXISTS `notes`";
        $table_Chema[] = "DROP TABLE IF EXISTS `notes_comments`";
        $table_Chema[] = "DROP TABLE IF EXISTS `photos`";
        $table_Chema[] = "DROP TABLE IF EXISTS `photos_comments`";
        $table_Chema[] = "DROP TABLE IF EXISTS `photos_mark`";
        $table_Chema[] = "DROP TABLE IF EXISTS `photos_rating`";
        $table_Chema[] = "DROP TABLE IF EXISTS `report`";
        $table_Chema[] = "DROP TABLE IF EXISTS `restore`";
        $table_Chema[] = "DROP TABLE IF EXISTS `reviews`";
        $table_Chema[] = "DROP TABLE IF EXISTS `sms_log`";
        $table_Chema[] = "DROP TABLE IF EXISTS `static`";
        $table_Chema[] = "DROP TABLE IF EXISTS `support`";
        $table_Chema[] = "DROP TABLE IF EXISTS `support_answers`";
        $table_Chema[] = "DROP TABLE IF EXISTS `updates`";
        $table_Chema[] = "DROP TABLE IF EXISTS `users`";
        $table_Chema[] = "DROP TABLE IF EXISTS `users_rating`";
        $table_Chema[] = "DROP TABLE IF EXISTS `users_stats`";
        $table_Chema[] = "DROP TABLE IF EXISTS `users_stats_log`";
        $table_Chema[] = "DROP TABLE IF EXISTS `videos`";
        $table_Chema[] = "DROP TABLE IF EXISTS `videos_comments`";
        $table_Chema[] = "DROP TABLE IF EXISTS `votes`";
        $table_Chema[] = "DROP TABLE IF EXISTS `votes_result`";
        $table_Chema[] = "DROP TABLE IF EXISTS `wall`";
        $table_Chema[] = "DROP TABLE IF EXISTS `wall_like`";
        foreach ($table_Chema as $query)
            $db->query($query);

        Filesystem::delete(ENGINE_DIR . '/data/config.php');
        Filesystem::delete(ENGINE_DIR . '/data/db.php');

        echo <<<HTML
Добро пожаловать в мастер установки Vii Engine. 
<br /><br />
Данный мастер поможет вам установить скрипт всего за пару минут. 
HTML;
        echo <<<HTML
<input type="submit" class="inp fl_r" value="Начать установку" onClick="location.href='/install.php?act=files'" />
HTML;

    } else {
        echo <<<HTML
<div class="h1">Установка скрипта автоматически заблокирована</div>
Внимание, на сервере обнаружена уже установленная копия Vii Engine. 
HTML;

        echo <<<HTML
<div style=" display: flex">
<input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px" value="Очистить VII Engine" onClick="location.href='/install.php?act=clean'" />
<input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px" value="Удалить инсталятор" onClick="location.href='/install.php?act=remove_installer'" />
</div>

HTML;

        echo <<<HTML
<div style="width: 100%;height: 50px">
<input type="submit" class="inp fl_r" value="Обновить" onClick="location.href='/install.php'" />
</div>

HTML;


    }
}

echo <<<HTML
</div>
</body>
</html>
HTML;

