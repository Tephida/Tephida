<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    echo "Please change php version";
    exit();
}

function main_print(): void
{
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
<input type="submit" class="inp fl_r" value="Начать установку" onClick="location.href='/install.php?act=files'" />
<br />
<br />
HTML;
}

echo <<<HTML
<!DOCTYPE>
<html lang="ru">
<head>
<title>Vii Engine - Установка</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<style media="all">
body{font-size:0.8em;
font-family:Tahoma;
background: linear-gradient(180deg, #0d789c, #c8eeb1, white, white) repeat-x;}
a{color:#4274a5;text-decoration:underline}
a:hover{color:#4274a5;text-decoration:none}
.box {margin: auto;width: 800px;
background: #fff;
box-shadow: 0 1px 4px 1px #cfcfcf;
padding: 10px;border-radius: 5px;
}
.head{background: linear-gradient(0deg, #1993b0, #1993b0, #3db9c2) repeat-x;height:49px;border-top-left-radius:5px;
margin:-10px -10px 5px -10px;
}
.h1{font-size:1.2em;font-weight:bold;color:#4274a5;
margin: 5px;padding-bottom:2px;
border-bottom:1px solid #e5edf5;padding-left:2px}
.clr{clear:both}
.fl_l{float:left}
.fl_r{float:right}
.inp{padding: 5px 10px 5px 10px; 
 background: linear-gradient(45deg, #b7c42d, #8d991b); color: #fff; font-size: 11px; font-family: Tahoma, Verdana, Arial, sans-serif, Lucida Sans; 
 text-shadow: 0 1px 0 #767f18; border: 0; border-top: 1px solid #cdd483; cursor: pointer; margin: 10px 0 0 0; 
 font-weight: bold; border-radius: 2px;
   box-shadow: inset 0 1px 3px 0 #d2d2d2;}
.inp:hover{background:linear-gradient(180deg, #c6d059, #a3ae36);
}
.inp:active{background:#848f18;position:relative;border-top:1px solid #727c0e;outline:none}
.inpu{width:200px;box-shadow:inset 0 1px 3px 0 #d2d2d2;border:1px solid #ccc;padding:4px;border-radius:3px;font-size:11px;
font-family:tahoma;margin-bottom:3px;}
textarea{width:300px;height:100px;}
.fllogall{color:#555}
</style>
<div class="box clr">
 <a href="/install.php"><div class="head"><div style="color: white;font-size: 1.5em;padding: 10px;margin-left: 5px">Vii Engine - Установка</div></div></a>
HTML;

try {
    require_once './vendor/autoload.php';
} catch (Error $e) {
    echo <<<HTML
Please install composer <a href="https://getcomposer.org/" target="_blank" style="text-decoration: underline;color: darkblue">Composer</a>
<div style="width: 100%;height: 50px">
<input type="submit" class="inp fl_r" value="Обновить" onClick="location.href='/install.php'" />
</div>

</div>
</body>
</html>
HTML;
    die('');
}

header('Content-type: text/html; charset=utf-8');

const ROOT_DIR = __DIR__;
const ENGINE_DIR = ROOT_DIR . '/system';

function check_install(): bool
{
    return !(!file_exists(ENGINE_DIR . '/data/config.php') || !file_exists(ENGINE_DIR . '/data/db.php'));
}

$act = (new \FluffyDollop\Http\Request)->filter('act');

switch ($act) {
    case "files":
        if (!check_install()) {
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
                './templates/',
            );

            try {
                \FluffyDollop\Support\Filesystem::createDir('./uploads/room/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/records/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/attach/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/audio_tmp/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/blog/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/groups/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/users/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/videos/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/audio/');
                \FluffyDollop\Support\Filesystem::createDir('./uploads/doc/');

                \FluffyDollop\Support\Filesystem::createDir('./system/cache/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/groups/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/groups_forum/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/groups_mark/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/photos_mark/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/votes/');
                \FluffyDollop\Support\Filesystem::createDir('./system/cache/wall/');

                \FluffyDollop\Support\Filesystem::createDir('./system/data/');

                \FluffyDollop\Support\Filesystem::createDir('./backup/');

            } catch (Exception $e) {
                echo '<div class="h2">Не удалось создать директории</div>';
            }

            $chmod_errors = 0;
            $not_found_errors = 0;
            echo "<div style=\"font-weight: bold\"><div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\">Папка/Файл</div>
		<div style=\"float:left;width:90px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">CHMOD</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Статус</div>
		<div class=clear></div></div>";

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
            if ($chmod_errors == 0 && $not_found_errors == 0) {
                $status_report = 'Проверка успешно завершена! Можете продолжить установку!';
            } else {
                $status_report = '';
                if ($chmod_errors > 0) {
                    $status_report = "<div style=\"color: red;\">Внимание!!!</div><br /><br />Во время проверки обнаружены ошибки: <div style=\"font-weight: bold\">$chmod_errors</div>. Запрещена запись в файл.<br />Вы должны выставить для папок CHMOD 777, для файлов CHMOD 666, используя ФТП-клиент.<br /><br /><div style=\"color: red;\"><div style=\"font-weight: bold\">Настоятельно не рекомендуется</div></div> продолжать установку, пока не будут произведены изменения.<br />";
                }
                if ($not_found_errors > 0) {
                    $status_report .= "<div style=\"color: red;\">Внимание!!!</div><br />Во время проверки обнаружены ошибки: <div style=\"font-weight: bold\">$not_found_errors</div>. Файлы не найдены!<br /><br /><div style=\"color: red;\"><div style=\"font-weight: bold\">Не рекомендуется</div></div> продолжать установку, пока не будут произведены изменения.<br />";
                }
                if (!isset($status_report)) {
                    $status_report = '';
                }
            }

            echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;margin-top:10px"><div style="margin-bottom:7px;"><div style=\"font-weight: bold\">Состояние проверки</div></div>' . $status_report . '</div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=system\'" />
		
		<br />
        <br />
        ';
        } else {
            main_print();
        }
        break;
    case "system":
        if (!check_install()) {
            echo "<div class=\"h1\">Системные требования</div>
		<div style=\"font-weight: bold\"><div style=\"float:left;width:448px;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Требования движка</div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Ваша версия</div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Статус</div>
		<div class=clear></div></div>";
            $status = version_compare(PHP_VERSION, '8.0.0') >= 0 ? '<div style="color: green;"><div style="font-weight: bold">Совместимо</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Не совместимо</div></div>';
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">PHP 8.0</div></div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">" . PHP_VERSION . "</div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = function_exists('mysqli_connect') ? '<div style="color: green;"><div style="font-weight: bold">Совместимо</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Не совместимо</div></div>';
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Поддержка MySQLi</div></div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\"> </div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = extension_loaded('zlib') ? '<div style="color: green;"><div style="font-weight: bold">Совместимо</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Не совместимо</div></div>';
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Поддержка сжатия ZLib</div></div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\"> </div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = extension_loaded('gd') ? '<div style="color: green;"><div style="font-weight: bold">Совместимо</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Не совместимо</div></div>';
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Поддержка GD</div></div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\"> </div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = function_exists('iconv') ? '<div style="color: green;"><div style="font-weight: bold">Совместимо</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Не совместимо</div></div>';
            echo "<div style=\"float:left;width:450px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Поддержка ICONV</div></div>
		<div style=\"float:left;width:110px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\"> </div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc;"><div style="margin-bottom:7px;text-align: center;font-size: 12px;"><div style="font-weight: bold">Если любой из этих пунктов выделен красным, то выполните действия для исправления положения. <br />В случае несоблюдения минимальных требований скрипта возможна его некорректная работа в системе.</div></div></div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=server\'" />
		<br />
        <br />
		';
        } else {
            main_print();
        }
        break;
    case "server":
        if (!check_install()) {
            echo "<div class=\"h1\">Настройки сервера</div>
		<div style=\"font-weight: bold\"><div style=\"float:left;width:360px;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Рекомендуемые настройки</div>
		<div style=\"float:left;width:175px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Рекомендуемое значение</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border: 1px solid #ddd;background: #f7f7f7;\">Текущее значение</div>
		<div class=clear></div></div>";
            $status = ini_get('file_uploads') ? '<div style="color: green;"><div style="font-weight: bold">Включено</div></div>' : '<div style="color: red;"><div style="font-weight: bold">Выключено</div></div>';
            echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Загрузка файлов</div></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Включено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = ini_get('output_buffering') ? '<div style="font-weight: bold"><div style="font-weight: bold">Включено</div></div>' : '<div style="color: green;"><div style="font-weight: bold">Выключено</div></div>';
            echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Буферизация вывода</div></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            $status = ini_get('session.auto_start') ? '<div style="color: red;"><div style="font-weight: bold">Включено</div></div>' : '<div style="color: green;"><div style="font-weight: bold">Выключено</div></div>';
            echo "<div style=\"float:left;width:390px;padding:10px;border-bottom:1px dashed #ddd\"><div style=\"font-weight: bold\">Session auto start</div></div>
		<div style=\"float:left;width:150px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">Выключено</div>
		<div style=\"float:left;width:195px;text-align:center;padding:10px;border-bottom:1px dashed #ddd\">{$status}</div>
		<div class=clear></div>";
            echo '
		<div class="clr"></div>
		<div style="background:lightyellow;padding:10px;margin-bottom:10px;margin-top:10px;border:1px dashed #ccc"><div style="margin-bottom:7px;text-align: center;font-size: 12px;"><div style="font-weight: bold">Данные настройки являются рекомендуемыми для полной совместимости, однако скрипт способен работать даже если рекомендуемые настройки не совпадают с текущими.</div></div></div>
		<input type="submit" class="inp fl_r" value="Продолжить &raquo;" onClick="location.href=\'/install.php?act=settings\'" />
		<br />
        <br />
		';
        } else {
            main_print();
        }
        break;
    case "settings":
        if (!check_install()) {
            $url = $_SERVER['HTTP_HOST'];
            echo <<<HTML
<form method="POST" action="/install.php?act=install">

<div class="h1">Настройка конфигурации системы</div>
<div class="fllogall">Адрес сайта:</div><input type="text" name="url" class="inpu" value="https://{$url}/" />&nbsp;&nbsp;<span style="display:flex;color:#777">Укажите путь без имени файла, знак слеша <div style="color:red;"> / </div> на конце обязателен</span><div class="mgcler"></div>

<div class="h1" style="margin-top:15px">Данные для доступа к MySQL серверу</div>
<div class="fllogall">Сервер MySQL:</div><input type="text" name="mysql_server" class="inpu" value="localhost" /><div class="mgcler"></div>
<div class="fllogall">Имя базы данных:</div><input type="text" name="mysql_dbname" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Имя пользователя:</div><input type="text" name="mysql_dbuser" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Пароль:</div><input type="text" name="mysql_pass" class="inpu" /><div class="mgcler"></div>

<div class="h1" style="margin-top:15px">Данные для доступа к панели управления</div>
<div class="fllogall">файл админпанели:</div><input type="text" name="adminfile" class="inpu" value="adminpanel.php" /><div class="mgcler"></div>
<div class="fllogall">Имя администратора:</div><input type="text" name="name" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Фамилия администратора:</div><input type="text" name="lastname" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">E-mail:</div><input type="text" name="email" class="inpu" /><div class="mgcler"></div>
<div class="fllogall">Пароль:</div><input type="password" name="pass" class="inpu" /><div class="mgcler"></div>

<input type="submit" class="inp fl_r" value="Завершить установку &raquo;" onClick="location.href=\'/install.php?act=settings\'" />
<br />
<br />
</form>
HTML;
        } else {
            main_print();
        }
        break;
    case "install":
        if (!check_install()) {
            if (!empty($_POST['mysql_server']) && !empty($_POST['mysql_dbname']) && !empty($_POST['mysql_dbuser']) &&
                !empty($_POST['adminfile']) && !empty($_POST['name']) && !empty($_POST['lastname']) &&
                !empty($_POST['email']) && !empty($_POST['pass'])) {
                $_POST['mysql_server'] = str_replace(array("$", '"'), array("\\$", '\"'), $_POST['mysql_server']);
                $_POST['mysql_dbname'] = str_replace(array("$", '"'), array("\\$", '\"'), $_POST['mysql_dbname']);
                $_POST['mysql_dbuser'] = str_replace(array("$", '"'), array("\\$", '\"'), $_POST['mysql_dbuser']);
                $_POST['mysql_pass'] = str_replace(array("$", '"'), array("\\$", '\"'), $_POST['mysql_pass']);
                //Создаём файл БД
                $db_config = <<<HTML
<?php

const DBHOST = "{$_POST['mysql_server']}"; 

const DBNAME = "{$_POST['mysql_dbname']}";

const DBUSER = "{$_POST['mysql_dbuser']}";

const DBPASS = "{$_POST['mysql_pass']}";

const COLLATE = "utf8";

return new \FluffyDollop\Support\Mysql;

HTML;
                file_put_contents(ENGINE_DIR . "/data/db.php", $db_config);

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
session_start();
ob_start();
ob_implicit_flush(0);

if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    throw new \RuntimeException("Please change php version");
}

try {
    require_once './vendor/autoload.php';
} catch (Exception) {
    throw new \RuntimeException("Please install composer");
}

const ROOT_DIR = __DIR__;
const ENGINE_DIR = ROOT_DIR . '/system';
const ADMIN_DIR = ROOT_DIR . '/system/inc';
include ADMIN_DIR.'/login.php';
HTML;
                file_put_contents(ROOT_DIR . "/" . $_POST['adminfile'], $admin);


                //Создаём файл конфигурации системы
                $config = <<<HTML
<?php

//System Configurations 

return array ( 

'home' => "Социальная сеть", 

'charset' => "utf-8", 

'home_url' => "{$_POST['url']}", 

'admin_index' => "{$_POST['adminfile']}",

'temp' => "Mixchat", 

'online_time' => "150", 

'lang' => "Russian", 

'gzip' => "no", 

'gzip_js' => "no", 

'offline' => "no", 

'offline_msg' => "Сайт находится на текущей реконструкции, после завершения всех работ сайт будет открыт.\r\n\r\nПриносим вам свои извинения за доставленные неудобства.",

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
                file_put_contents(ENGINE_DIR . "/data/config.php", $config);

                $db = require ENGINE_DIR . '/data/db.php';

                $_POST['name'] = strip_tags($_POST['name']);
                $_POST['lastname'] = strip_tags($_POST['lastname']);
                $table_Chema = array();

                include_once ENGINE_DIR . '/data/mysql_tables.php';

                //Вставляем админа в базу
                $_POST['pass'] = md5(md5($_POST['pass']));
                $hid = $_POST['pass'] . md5(md5($_SERVER['REMOTE_ADDR']));

                $server_time = time();

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

                foreach ($table_Chema as $query) {
                    try {
                        $db->query($query);
                    } catch (Error $e) {
                        echo $query;
                        exit();
                    }
                }

                $admin_index = $admin_index ?? 'adminpanel.php';
                echo <<<HTML
<div class="h1">Установка успешно завершена</div>
Поздравляем Вас, Vii Engine был успешно установлен на Ваш сервер. Вы можете просмотреть теперь главную <a href="/">страницу вашего сайта</a> и посмотреть возможности скрипта. Либо Вы можете <a href="/{$admin_index}">зайти</a> в панель управления Vii Engine и изменить другие настройки системы. 
<br /><br />
<div style="color: red">Внимание: при установке скрипта создается структура базы данных, создается аккаунт администратора, 
а также прописываются основные настройки системы.</div>
<br /><br />
Приятной Вам работы!
<br />
<br />
HTML;

            } else {
                echo <<<HTML
<div class="h1">Ошибка</div>
Заполните необходимые поля!
<input type="submit" class="inp fl_r" value="Назад" onClick="javascript:history.back()" />
<br />
<br />
HTML;
            }

        } else {
            main_print();
        }
        break;
    case "remove_installer":
        if (check_install() && !file_exists('./system/data/look')) {
            \FluffyDollop\Support\Filesystem::delete('./install.php');
            \FluffyDollop\Support\Filesystem::delete('./system/mysql_tables.php');
            header('Location: /');

        } else {
            main_print();
        }
        break;
    case "clean":
        if (check_install() && !file_exists('./system/data/look')) {
            \FluffyDollop\Support\Filesystem::delete('./uploads/room/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/records/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/attach/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/audio_tmp/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/blog/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/groups/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/users/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/videos/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/audio/');
            \FluffyDollop\Support\Filesystem::delete('./uploads/doc/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/groups/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/groups_forum/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/groups_mark/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/photos_mark/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/votes/');
            \FluffyDollop\Support\Filesystem::delete('./system/cache/wall/');

            \FluffyDollop\Support\Filesystem::delete(ROOT_DIR . '/adminpanel.php');

            $db = require ENGINE_DIR . '/data/db.php';

            $table_Chema = array();
            $table_Chema[] = "DROP TABLE IF EXISTS `room`";
            $table_Chema[] = "DROP TABLE IF EXISTS `room_users`";
            $table_Chema[] = "DROP TABLE IF EXISTS `albums`";
            $table_Chema[] = "DROP TABLE IF EXISTS `attach`";
            $table_Chema[] = "DROP TABLE IF EXISTS `antispam`";
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
            $table_Chema[] = "DROP TABLE IF EXISTS `gifts`";
            $table_Chema[] = "DROP TABLE IF EXISTS `gifts_list`";
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
            foreach ($table_Chema as $query) {
                try {
                    $db->query($query);
                } catch (Error $e) {
                    echo $query;
                    exit();
                }
            }

            \FluffyDollop\Support\Filesystem::delete(ENGINE_DIR . '/data/config.php');
            \FluffyDollop\Support\Filesystem::delete(ENGINE_DIR . '/data/db.php');

            echo <<<HTML
Добро пожаловать в мастер установки Vii Engine. 
<br /><br />
Данный мастер поможет вам установить скрипт всего за пару минут. 

<input type="submit" class="inp fl_r" value="Начать установку" onClick="location.href='/install.php?act=files'" />
<br />
<br />
HTML;
        } else {
            main_print();
        }
        break;
    default:
        if (check_install()) {
            echo <<<HTML
<div class="h1">Установка скрипта автоматически заблокирована</div>
<div style="font-weight: bold">
    Внимание, на сервере обнаружена уже установленная копия Vii Engine. 
</div>


<div style=" display: flex">
<input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px" value="Очистить VII Engine" onClick="location.href='/install.php?act=clean'" />
<input type="submit" class="inp fl_r" style="background: #f44336; margin: 10px;"  value="Удалить инсталятор" onClick="location.href='/install.php?act=remove_installer'" />
</div>

<div style="width: 100%;height: 50px">
<input type="submit" class="inp fl_r" value="Обновить" onClick="location.href='/install.php'" />
</div>
<br />
<br />
HTML;
        } else {
            main_print();
        }
}

echo <<<HTML
</div>
</body>
</html>
HTML;