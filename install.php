<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;

if (version_compare(PHP_VERSION, '8.0.0') < 0) {
    echo "Please change php version";
    exit();
}

function main_print(): bool
{
    $params = [];
    return view('install.installed', $params);
}

try {
    require_once './vendor/autoload.php';
} catch (Error $e) {
    echo <<<HTML
Please install composer <a href="https://getcomposer.org/" target="_blank" style="text-decoration: underline;color: darkblue">Composer</a>
<div style="width: 100%;height: 50px">
<input type="submit" class="inp fl_r" style="background-color: #ffeb3b" value="Обновить" onClick="location.href='/install.php'" />
</div>
HTML;
    die('');
}

$config = [
    'home' => 'Installer',
    'temp' => 'Mixchat',
];
Registry::set('config', $config);

header('Content-type: text/html; charset=utf-8');

const ROOT_DIR = __DIR__;
const ENGINE_DIR = ROOT_DIR . '/system';

function check_install(): bool
{
    return !(!file_exists(ENGINE_DIR . '/data/config.php') || !file_exists(ENGINE_DIR . '/data/db_config.php'));
}

$act = (new \FluffyDollop\Http\Request)->filter('act');

switch ($act) {

    case "settings":
        if (!check_install()) {
            $url = $_SERVER['HTTP_HOST'];
            try {
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/room/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/records/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/attach/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/audio_tmp/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/blog/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/groups/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/users/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/videos/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/audio/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./uploads/doc/');

                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/groups/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/groups_forum/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/groups_mark/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/photos_mark/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/votes/');
                \FluffyDollop\Filesystem\Filesystem::createDir('./system/cache/wall/');

                \FluffyDollop\Filesystem\Filesystem::createDir('./system/data/');

                \FluffyDollop\Filesystem\Filesystem::createDir('./backup/');

            } catch (Exception $e) {
                echo '<div class="h2">Не удалось создать директории</div>';//fixme
            }

            $params = [
                'url' => $url,
            ];
            return view('install.settings', $params);
        } else {
            $params = [];
            return view('install.installed', $params);
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
                $db_config = "<?php
                return [
                    'host' => \"{$_POST['mysql_server']}\",
                    'name' => \"{$_POST['mysql_dbname']}\",
                    'user' => \"{$_POST['mysql_dbuser']}\",
                    'pass' => \"{$_POST['mysql_pass']}\",
                ];
                ";
                $db_config = str_replace('                ', '', $db_config);
                file_put_contents(ENGINE_DIR . "/data/db_config.php", $db_config);

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
                    
                    return [
                    'home' => "Social", 
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
                    ];
                    
                    HTML;
                file_put_contents(ENGINE_DIR . "/data/config.php", $config);

                $db_config = require ENGINE_DIR . '/data/db_config.php';

                $_POST['name'] = strip_tags($_POST['name']);
                $_POST['lastname'] = strip_tags($_POST['lastname']);
                $table_Chema = array();
                $table_data = array();

                include_once ENGINE_DIR . '/data/mysql_tables.php';
                $db = new PDO(
                    "mysql:dbname=" . $_POST['mysql_dbname'] . ";host=" . $_POST['mysql_server'],
                    $_POST['mysql_dbuser'],
                    $_POST['mysql_pass']);
                try {
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Error Handling
                    foreach ($table_Chema as $sql) {
                        try {
                            $db->exec($sql);
                        } catch (Error $e) {
                            echo 'error query: ' . $sql;
                            exit();
                        }
                    }

                    include_once ENGINE_DIR . '/data/mysql_data_country.php';

                    //Вставляем админа в базу
                    $_POST['pass'] = md5(md5($_POST['pass']));
                    $hid = $_POST['pass'] . md5(md5($_SERVER['REMOTE_ADDR']));

                    $server_time = time();

                    $sql = "INSERT INTO users (
                   user_name, 
                   user_lastname, 
                   user_email,
                   user_password,
                   user_group,
                   user_search_pref,
                   user_privacy,
                   user_hid,
                   user_birthday,
                   user_day,
                   user_month,
                   user_year,
                   user_country,
                   user_city,
                   user_lastdate,
                   user_lastupdate,
                   user_reg_date
                   ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $db->prepare($sql)->execute([
                        $_POST['name'],
                        $_POST['lastname'],
                        $_POST['email'],
                        $_POST['pass'],
                        1,
                        $_POST['name'] . ' ' . $_POST['lastname'],
                        'val_msg|1||val_wall1|1||val_wall2|1||val_wall3|1||val_info|1||',
                        $hid,
                        '0-0-0',
                        0,
                        0,
                        0,
                        0,
                        0,
                        $server_time,
                        $server_time,
                        $server_time,
                    ]);

                    $sql = "INSERT INTO log (uid, browser, ip) VALUES (?,?,?)";
                    $db->prepare($sql)->execute([1, '', '']);

                } catch (PDOException $e) {
                    echo $e->getMessage();//Remove or change message in production code
                }

                $admin_index = $admin_index ?? 'adminpanel.php';
                echo <<<HTML
<div class="h1">Установка успешно завершена</div>
Поздравляем Вас, Tephida был успешно установлен на Ваш сервер. Вы можете просмотреть теперь главную <a href="/">страницу вашего сайта</a> и посмотреть возможности скрипта. Либо Вы можете <a href="/{$admin_index}">зайти</a> в панель управления Tephida и изменить другие настройки системы. 
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
            $params = [];
            return view('install.installed', $params);
        }
        break;
    case "remove_installer":
        if (check_install() && !file_exists('./system/data/look')) {
            \FluffyDollop\Filesystem\Filesystem::delete('./install.php');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/mysql_tables.php');
            header('Location: /');
        } else {
            $params = [];
            return view('install.installed', $params);
        }
        break;
    case "clean":
        if (check_install() && !file_exists('./system/data/look')) {
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/room/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/records/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/attach/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/audio_tmp/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/blog/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/groups/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/users/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/videos/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/audio/');
            \FluffyDollop\Filesystem\Filesystem::delete('./uploads/doc/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/groups/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/groups_forum/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/groups_mark/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/photos_mark/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/votes/');
            \FluffyDollop\Filesystem\Filesystem::delete('./system/cache/wall/');

            $db_config = require ENGINE_DIR . '/data/db_config.php';

            $db = new PDO(
                "mysql:dbname=" . $db_config['name'] . ";host=" . $db_config['host'],
                $db_config['user'],
                $db_config['pass']);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//Error Handling

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

            \FluffyDollop\Filesystem\Filesystem::delete(ENGINE_DIR . '/data/config.php');
            \FluffyDollop\Filesystem\Filesystem::delete(ENGINE_DIR . '/data/db_config.php');
            \FluffyDollop\Filesystem\Filesystem::delete(ROOT_DIR . '/adminpanel.php');

            $params = [];
            return view('install.installed', $params);
        } else {
            $params = [];
            return view('install.installed', $params);
        }
        break;
    default:
        if (check_install()) {
            $params = [];
            return view('install.installed', $params);
        } else {
            $params = [];
            return view('install.main', $params);
        }
}
