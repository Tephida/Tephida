<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG')) die('Hacking attempt!');

$go = isset($_GET['go']) ? htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['go']))))) : "main";

$mozg_module = $go;
check_xss();
//FOR MOBILE VERSION 1.0
$lang['online'] = $config['temp'] == 'mobile' ? '<img src="{theme}/images/monline.gif" />' : '';

switch ($go) {
    /** Регистрация */
    case "register":
        include ENGINE_DIR . '/modules/register.php';
        break;

    /** Профиль пользователя */
    case "profile":
        $spBar = true;
        include ENGINE_DIR . '/modules/profile.php';
        break;

    /** Редактирование моей страницы */
    case "editprofile":
        $spBar = true;
        include ENGINE_DIR . '/modules/editprofile.php';
        break;

    /**Загрузка городов  */
    case "loadcity":
        include ENGINE_DIR . '/modules/loadcity.php';
        break;

    /** Альбомы */
    case "albums":
        $spBar = true;
        if ($config['album_mod'] == 'yes')
            include ENGINE_DIR . '/modules/albums.php';
        else {
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;

    /** Просмотр фотографии */
    case "photo":
        include ENGINE_DIR . '/modules/photo.php';
        break;

    /** Друзья */
    case "friends":
        $spBar = true;
        include ENGINE_DIR . '/modules/friends.php';
        break;

    /** Закладки */
    case "fave":
        $spBar = true;
        include ENGINE_DIR . '/modules/fave.php';
        break;

    /** Диалоги */
    case "im":
        include ENGINE_DIR . '/modules/im.php';
        break;

    /** Заметки */
    case "notes":
        $spBar = true;
        include ENGINE_DIR . '/modules/notes.php';
        break;

    /** Подписки */
    case "subscriptions":
        include ENGINE_DIR . '/modules/subscriptions.php';
        break;

    /** Видео */
    case "videos":
        $spBar = true;
        if ($config['video_mod'] == 'yes')
            include ENGINE_DIR . '/modules/videos.php';
        else {
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;

    /** Поиск */
    case "search":
        include ENGINE_DIR . '/modules/search.php';
        break;

    /** Стена */
    case "wall":
        $spBar = true;
        include ENGINE_DIR . '/modules/wall.php';
        break;

    /** Статус */
    case "status":
        include ENGINE_DIR . '/modules/status.php';
        break;

    /** Новости */
    case "news":
        $spBar = true;
        include ENGINE_DIR . '/modules/news.php';
        break;

    /** Настройки */
    case "settings":
        include ENGINE_DIR . '/modules/settings.php';
        break;

    /** Помощь */
    case "support":
        include ENGINE_DIR . '/modules/support.php';
        break;

    /** Восстановление доступа */
    case "restore":
        include ENGINE_DIR . '/modules/restore.php';
        break;

    /** Загрузка картинок при прикреплении файлов со стены, заметок, или сообщений */
    case "attach":
        include ENGINE_DIR . '/modules/attach.php';
        break;

    /** Блог сайта */
    case "blog":
        $spBar = true;
        include ENGINE_DIR . '/modules/blog.php';
        break;

    /** Баланс */
    case "balance":
        include ENGINE_DIR . '/modules/balance.php';
        break;

    /** Подарки */
    case "gifts":
        include ENGINE_DIR . '/modules/gifts.php';
        break;

    /** Сообщества */
    case "groups":
        include ENGINE_DIR . '/modules/groups.php';
        break;

    /** Сообщества -> Публичные страницы */
    case "public":
        $spBar = true;
        include ENGINE_DIR . '/modules/public.php';
        break;

    /** Сообщества -> Загрузка фото */
    case "attach_groups":
        include ENGINE_DIR . '/modules/attach_groups.php';
        break;

    /** Музыка */
    case "audio":
        if ($config['audio_mod'] == 'yes')
            include ENGINE_DIR . '/modules/audio.php';
        else {
            $spBar = true;
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }
        break;

    /** Статические страницы */
    case "static":
        include ENGINE_DIR . '/modules/static.php';
        break;

    /** Выделить человека на фото */
    case "distinguish":
        include ENGINE_DIR . '/modules/distinguish.php';
        break;

    /** Скрываем блок Дни рожденья друзей */
    case "happy_friends_block_hide":
        $_SESSION['happy_friends_block_hide'] = 1;
        die();
        break;

    /** Скрываем блок Дни рожденья друзей */
    case "fast_search":
        include ENGINE_DIR . '/modules/fast_search.php';
        break;

    /** Жалобы */
    case "report":
        include ENGINE_DIR . '/modules/report.php';
        break;

    /** Отправка записи в сообщество или другу */
    case "repost":
        include ENGINE_DIR . '/modules/repost.php';
        break;

    /** Моментальные оповещения */
    case "updates":
        include ENGINE_DIR . '/modules/updates.php';
        break;

    /** Документы */
    case "doc":
        include ENGINE_DIR . '/modules/doc.php';
        break;

    /** Опросы */
    case "votes":
        include ENGINE_DIR . '/modules/votes.php';
        break;

    /** Сообщества -> Публичные страницы -> Аудиозаписи */
    case "public_audio":
        include ENGINE_DIR . '/modules/public_audio.php';
        break;

    /** Сообщества -> Публичные страницы -> Обсуждения */
    case "groups_forum":
        include ENGINE_DIR . '/modules/groups_forum.php';
        break;

    /** Комментарии к прикрепленным фото */
    case "attach_comm":
        include ENGINE_DIR . '/modules/attach_comm.php';
        break;

    /** Сообщества -> Публичные страницы -> Видеозаписи */
    case "public_videos":
        include ENGINE_DIR . '/modules/public_videos.php';
        break;

    /** Удаление страницы */
    case "del_my_page":
        include ENGINE_DIR . '/modules/del_my_page.php';
        die();
        break;

    /** Гости */
    case "guests":
        $spBar = true;
        include ENGINE_DIR . '/modules/guests.php';
        break;

    /** Фоторедактор */
    case "photo_editor":
        include ENGINE_DIR . '/modules/photo_editor.php';
        break;

    /** Игры */
    case "apps":
        $app_mod = false;
        if ($app_mod == true) {
            include ENGINE_DIR . '/modules/apps.php';
        } else {
            $user_speedbar = 'Информация';
            msgbox('', 'Сервис отключен.', 'info');
        }

        break;

    /** Отзывы */
    case "reviews":
        include ENGINE_DIR . '/modules/reviews.php';
        break;

    /** Плеер */
    case "audio_player":
        include ENGINE_DIR . '/modules/audio_player.php';
        break;

    /** Рейтинг */
    case "rating":
        include ENGINE_DIR . '/modules/rating.php';
        break;

    /** Статистика сообществ */
    case "stats_groups":
        include ENGINE_DIR . '/modules/stats_groups.php';
        break;

    /** Выбор языка */
    case "lang":
        include ENGINE_DIR . '/modules/lang.php';
        break;

    /** Статистика страницы пользователя */
    case "my_stats":
        include ENGINE_DIR . '/modules/my_stats.php';
        break;
    default:
        $spBar = true;
        if ($go != 'main')
            msgbox('', $lang['no_str_bar'], 'info');
}
if (empty($metatags['title']))
    $metatags['title'] = $config['home'];
$speedbar = $user_speedbar ?? $lang['welcome'];
$headers = '<title>' . $metatags['title'] . '</title>
<meta name="generator" content="VII ENGINE" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />';