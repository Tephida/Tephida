<?php
/*
	Appointment: Подключение модулей
	File: mod.php 
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if (!defined('MOZG')) die('Hacking attempt!');
if (isset($_GET['go'])) $go = htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['go'])))));
else $go = "main";
$mozg_module = $go;
check_xss();
//FOR MOBILE VERSION 1.0
if ($config['temp'] == 'mobile') $lang['online'] = '<img src="{theme}/images/monline.gif" />';
switch ($go) {
        //Регистрация
        
    case "register":
        include ENGINE_DIR . '/modules/register.php';
    break;
        //Профиль пользователя
        
    case "profile":
        $spBar = true;
        include ENGINE_DIR . '/modules/profile.php';
    break;
        //Редактирование моей страницы
        
    case "editprofile":
        $spBar = true;
        include ENGINE_DIR . '/modules/editprofile.php';
    break;
        //Загрузка городов
        
    case "loadcity":
        include ENGINE_DIR . '/modules/loadcity.php';
    break;
        //Альбомы
        
    case "albums":
        $spBar = true;
        if ($config['album_mod'] == 'yes') include ENGINE_DIR . '/modules/albums.php';
        else {
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;
        //Просмотр фотографии
        
    case "photo":
        include ENGINE_DIR . '/modules/photo.php';
        break;
        //Друзья
        
    case "friends":
        $spBar = true;
        include ENGINE_DIR . '/modules/friends.php';
        break;
        //Закладки
        
    case "fave":
        $spBar = true;
        include ENGINE_DIR . '/modules/fave.php';
        break;
        //Сообщения
        
    case "messages":
        $spBar = true;
        include ENGINE_DIR . '/modules/messages.php';
        break;
        //Диалоги
        
    case "im":
        include ENGINE_DIR . '/modules/im.php';
        break;
        //Заметки
        
    case "notes":
        $spBar = true;
        include ENGINE_DIR . '/modules/notes.php';
        break;
        //Подписки
        
    case "subscriptions":
        include ENGINE_DIR . '/modules/subscriptions.php';
        break;
        //Видео
        
    case "videos":
        $spBar = true;
        if ($config['video_mod'] == 'yes') include ENGINE_DIR . '/modules/videos.php';
        else {
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;
        //Поиск
        
    case "search":
        include ENGINE_DIR . '/modules/search.php';
        break;
        //Стена
        
    case "wall":
        $spBar = true;
        include ENGINE_DIR . '/modules/wall.php';
        break;
        //Статус
        
    case "status":
        include ENGINE_DIR . '/modules/status.php';
        break;
        //Новости
        
    case "news":
        $spBar = true;
        include ENGINE_DIR . '/modules/news.php';
        break;
        //Настройки
        
    case "settings":
        include ENGINE_DIR . '/modules/settings.php';
        break;
        //Помощь
        
    case "support":
        include ENGINE_DIR . '/modules/support.php';
        break;
        //Воостановление доступа
        
    case "restore":
        include ENGINE_DIR . '/modules/restore.php';
        break;
        //Загрузка картинок при прикриплении файлов со стены, заметок, или сообщений
        
    case "attach":
        include ENGINE_DIR . '/modules/attach.php';
        break;
        //Блог сайта
        
    case "blog":
        $spBar = true;
        include ENGINE_DIR . '/modules/blog.php';
        break;
        //Баланс
        
    case "balance":
        include ENGINE_DIR . '/modules/balance.php';
        break;
        //Подарки
        
    case "gifts":
        include ENGINE_DIR . '/modules/gifts.php';
        break;
        //Сообщества
        
    case "groups":
        include ENGINE_DIR . '/modules/groups.php';
        break;
        //Сообщества -> Публичные страницы
        
    case "public":
        $spBar = true;
        include ENGINE_DIR . '/modules/public.php';
        break;
        //Сообщества -> Загрузка фото
        
    case "attach_groups":
        include ENGINE_DIR . '/modules/attach_groups.php';
        break;
        //Музыка
        
    case "audio":
        if ($config['audio_mod'] == 'yes') include ENGINE_DIR . '/modules/audio.php';
        else {
            $spBar = true;
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;
        //Статические страницы
        
    case "static":
        include ENGINE_DIR . '/modules/static.php';
        break;
        //Выделить человека на фото
        
    case "distinguish":
        include ENGINE_DIR . '/modules/distinguish.php';
        break;
        //Скрываем блок Дни рожденья друзей
        
    case "happy_friends_block_hide":
        $_SESSION['happy_friends_block_hide'] = 1;
        die();
        break;
        //Скрываем блок Дни рожденья друзей
        
    case "fast_search":
        include ENGINE_DIR . '/modules/fast_search.php';
        break;
        //Жалобы
        
    case "report":
        include ENGINE_DIR . '/modules/report.php';
        break;
        //Отправка записи в сообщество или другу
        
    case "repost":
        include ENGINE_DIR . '/modules/repost.php';
        break;
        //Моментальные оповещания
        
    case "updates":
        include ENGINE_DIR . '/modules/updates.php';
        break;
        //Документы
        
    case "doc":
        include ENGINE_DIR . '/modules/doc.php';
        break;
        //Опросы
        
    case "votes":
        include ENGINE_DIR . '/modules/votes.php';
        break;
        //Сообщества -> Публичные страницы -> Аудиозаписи
        
    case "public_audio":
        include ENGINE_DIR . '/modules/public_audio.php';
        break;
        //Сообщества -> Публичные страницы -> Обсуждения
        
    case "groups_forum":
        include ENGINE_DIR . '/modules/groups_forum.php';
        break;
        //Комментарии к прикприпленным фото
        
    case "attach_comm":
        include ENGINE_DIR . '/modules/attach_comm.php';
        break;
        //Сообщества -> Публичные страницы -> Видеозаписи
        
    case "public_videos":
        include ENGINE_DIR . '/modules/public_videos.php';
        break;
        //Удаление страницы
        
    case "del_my_page":
        NoAjaxQuery();
        if ($logged) {
            $user_id = $user_info['user_id'];
            $uploaddir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
            $row = $db->super_query("SELECT user_photo, user_wall_id FROM `" . PREFIX . "_users` WHERE user_id = '" . $user_id . "'");
            if ($row['user_photo']) {
                $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `" . PREFIX . "_wall` WHERE id = '" . $row['user_wall_id'] . "'");
                if ($check_wall_rec['cnt']) {
                    $update_wall = ", user_wall_num = user_wall_num-1";
                    $db->query("DELETE FROM `" . PREFIX . "_wall` WHERE id = '" . $row['user_wall_id'] . "'");
                    $db->query("DELETE FROM `" . PREFIX . "_news` WHERE obj_id = '" . $row['user_wall_id'] . "'");
                }
                $db->query("UPDATE `" . PREFIX . "_users` SET user_delet = 1, user_photo = '', user_wall_id = '' " . $update_wall . " WHERE user_id = '" . $user_id . "'");
                @unlink($uploaddir . $row['user_photo']);
                @unlink($uploaddir . '50_' . $row['user_photo']);
                @unlink($uploaddir . '100_' . $row['user_photo']);
                @unlink($uploaddir . 'o_' . $row['user_photo']);
                @unlink($uploaddir . '130_' . $row['user_photo']);
            } else $db->query("UPDATE `" . PREFIX . "_users` SET user_delet = 1, user_photo = '' WHERE user_id = '" . $user_id . "'");
            mozg_clear_cache_file('user_' . $user_id . '/profile_' . $user_id);
        }
        die();
        break;
        //Гости
        
    case "guests":
        $spBar = true;
        include ENGINE_DIR . '/modules/guests.php';
        break;
        //Фоторедактор
        
    case "photo_editor":
        include ENGINE_DIR . '/modules/photo_editor.php';
        break;
        //Игры
        
    case "apps":
        include ENGINE_DIR . '/modules/apps.php';
        break;
        //Отзывы
        
    case "reviews":
        include ENGINE_DIR . '/modules/reviews.php';
        break;
        //Плеер
        
    case "audio_player":
        include ENGINE_DIR . '/modules/audio_player.php';
        break;
        //Рейтинг
        
    case "rating":
        include ENGINE_DIR . '/modules/rating.php';
        break;
        //Статистика сообществ
        
    case "stats_groups":
        include ENGINE_DIR . '/modules/stats_groups.php';
        break;
        //Выбор языка
        
    case "lang":
        include ENGINE_DIR . '/modules/lang.php';
        break;
        //Статистика страницы пользователя
        
    case "my_stats":
        include ENGINE_DIR . '/modules/my_stats.php';
        break;
    default:
        $spBar = true;
        if ($go != 'main') msgbox('', $lang['no_str_bar'], 'info');
    }
    if (!$metatags['title']) $metatags['title'] = $config['home'];
    if ($user_speedbar) $speedbar = $user_speedbar;
    else $speedbar = $lang['welcome'];
    $headers = '<title>' . $metatags['title'] . '</title>
<meta name="generator" content="CMS TOOLS" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
?>