<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

$mod = isset($_GET['mod']) ? htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['mod']))))) : "main";
// Локализация для даты
$langdate = [
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
    'Tue' => "Вт", 'Wed' => "Ср", 'Thu' => "Чт", 'Fri' => "Пт", 'Sat' => "Сб",];
$server_time = (int)$_SERVER['REQUEST_TIME'];
switch ($mod) {
    //Настройки системы

    case "system":
        include ADMIN_DIR . '/modules/system.php';
        break;

    case "fake":
        include ADMIN_DIR . '/modules/fake.php';
        break;

    //Управление БД

    case "db":
        include ADMIN_DIR . '/modules/db.php';
        break;
    //dumper

    case "dumper":
        include ADMIN_DIR . '/modules/dumper.php';
        break;
    //Личные настройки

    case "mysettings":
        include ADMIN_DIR . '/modules/mysettings.php';
        break;
    //Пользователи

    case "users":
        include ADMIN_DIR . '/modules/users.php';
        break;
    //Массовые действия

    case "massaction":
        include ADMIN_DIR . '/modules/massaction.php';
        break;
    //Заметки

    case "notes":
        include ADMIN_DIR . '/modules/notes.php';
        break;
    //Подарки

    case "gifts":
        include ADMIN_DIR . '/modules/gifts.php';
        break;
    //Сообщества

    case "groups":
        include ADMIN_DIR . '/modules/groups.php';
        break;

    //Шаблоны сообщений

    case "mail_tpl":
        include ADMIN_DIR . '/modules/mail_tpl.php';
        break;
    //Рассылка сообщений

    case "mail":
        include ADMIN_DIR . '/modules/mail.php';
        break;
    //Фильтр по: IP, E-Mail

    case "ban":
        include ADMIN_DIR . '/modules/ban.php';
        break;
    //Поиск и Замена

    case "search":
        include ADMIN_DIR . '/modules/search.php';
        break;
    //Статические страницы

    case "static":
        include ADMIN_DIR . '/modules/static.php';
        break;
    //Антивирус

    //Логи посещений

    case "logs":
        include ADMIN_DIR . '/modules/logs.php';
        break;
    //Статистика

    case "stats":
        include ADMIN_DIR . '/modules/stats.php';
        break;

    case "webstats":
        include ADMIN_DIR . '/modules/webstats.php';
        break;
    //Видео

    case "videos":
        include ADMIN_DIR . '/modules/videos.php';
        break;
    //Музыка

    case "musics":
        include ADMIN_DIR . '/modules/musics.php';
        break;
    //Альбомы

    case "albums":
        include ADMIN_DIR . '/modules/albums.php';
        break;
    //Страны

    case "country":
        include ADMIN_DIR . '/modules/country.php';
        break;
    //Города

    case "city":
        include ADMIN_DIR . '/modules/city.php';
        break;
    //Список жалоб

    case "report":
        include ADMIN_DIR . '/modules/report.php';
        break;

    //Фильтр слов
    case "wordfilter":
        include ADMIN_DIR . '/modules/wordfilter.php';
        break;
    //Игры

    //Отзывы

    case "reviews":
        include ADMIN_DIR . '/modules/reviews.php';
        break;
    //Отчеты по SMS

    case "sms":
        include ADMIN_DIR . '/modules/sms.php';
        break;

    case "templates":
        include ADMIN_DIR . '/modules/templates.php';
        break;

    default:
        include ADMIN_DIR . '/modules/main.php';
}