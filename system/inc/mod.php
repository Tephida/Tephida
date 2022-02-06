<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG')) die('Hacking attempt!');
$mod = htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['mod'])))));
check_xss();
// Локализация для даты
$langdate = array(
    'January' => "января",
    'February' => "февраля",
    'March' => "марта",
    'April' => "апреля",
    'May' => "мая",
    'June' => "июня",
    'July' => "июля",
    'August' => "августа",
    'September' => "сентября",
    'October' => "октября",
    'November' => "ноября",
    'December' => "декабря",
    'Jan' => "янв",
    'Feb' => "фев",
    'Mar' => "мар",
    'Apr' => "апр",
    'Jun' => "июн",
    'Jul' => "июл", 'Aug' => "авг", 'Sep' => "сен", 'Oct' => "окт", 'Nov' => "ноя", 'Dec' => "дек",
    'Sunday' => "Воскресенье", 'Monday' => "Понедельник", 'Tuesday' => "Вторник", 'Wednesday' => "Среда",
    'Thursday' => "Четверг", 'Friday' => "Пятница", 'Saturday' => "Суббота", 'Sun' => "Вс", 'Mon' => "Пн",
    'Tue' => "Вт", 'Wed' => "Ср", 'Thu' => "Чт", 'Fri' => "Пт", 'Sat' => "Сб",);
$server_time = intval($_SERVER['REQUEST_TIME']);
switch ($mod) {
    //Настройки системы

    case "system":
        include 'system.php';
        break;
    //Управление БД

    case "db":
        include 'db.php';
        break;
    //dumper

    case "dumper":
        include 'dumper.php';
        break;
    //Личные настройки

    case "mysettings":
        include 'mysettings.php';
        break;
    //Пользователи

    case "users":
        include 'users.php';
        break;
    //Массовые действия

    case "massaction":
        include 'massaction.php';
        break;
    //Заметки

    case "notes":
        include 'notes.php';
        break;
    //Подарки

    case "gifts":
        include 'gifts.php';
        break;
    //Сообщества

    case "groups":
        include 'groups.php';
        break;
    //Шаблоны сайта

    case "tpl":
        include 'tpl.php';
        break;
    //Шаблоны сообщений

    case "mail_tpl":
        include 'mail_tpl.php';
        break;
    //Рассылка сообщений

    case "mail":
        include 'mail.php';
        break;
    //Фильтр по: IP, E-Mail

    case "ban":
        include 'ban.php';
        break;
    //Поиск и Замена

    case "search":
        include 'search.php';
        break;
    //Статические страницы

    case "static":
        include 'static.php';
        break;
    //Антивирус

    //Логи посещений

    case "logs":
        include 'logs.php';
        break;
    //Статистика

    case "stats":
        include 'stats.php';
        break;
    //Видео

    case "videos":
        include 'videos.php';
        break;
    //Музыка

    case "musics":
        include 'musics.php';
        break;
    //Альбомы

    case "albums":
        include 'albums.php';
        break;
    //Страны

    case "country":
        include 'country.php';
        break;
    //Города

    case "city":
        include 'city.php';
        break;
    //Список жалоб

    case "report":
        include 'report.php';
        break;
    //Доп. поля профилей

    case "xfields":
        include 'xfields.php';
        break;
    //Фильтр слов

    case "wordfilter":
        include 'wordfilter.php';
        break;
    //Игры

    case "apps":
        include 'apps.php';
        break;
    //Отзывы

    case "reviews":
        include 'reviews.php';
        break;
    //Отчеты по SMS

    case "sms":
        include 'sms.php';
        break;
    default:
        include 'main.php';
}
