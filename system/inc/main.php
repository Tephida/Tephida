<?php
/* 
	Appointment: Главная админки
	File: main.php
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die('Hacking attempt!');


$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_report`");
if($row['cnt']) $new_report = '<font color="red">('.$row['cnt'].')</font>';

$row_reviews = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_reviews` WHERE approve = '1'");
if($row_reviews['cnt']) $new_reviews = '<font color="red">('.$row_reviews['cnt'].')</font>';
	
echoheader();
echoblock('Настройка системы', 'Настройка общих параметров скрипта, а также настройка системы безопасности скрипта', 'system', 'settings');
echoblock('Управление БД', 'Резервное копирование и восстановление базы данных', 'db', 'db');
echoblock('Личные настройки', 'Управление и настройка вашего личного профиля пользователя.', 'mysettings', 'mysettings');
echoblock('Пользователи', 'Управление зарегистрированными на сайте пользователями, редактирование их профилей и блокировка аккаунта', 'users', 'users');
echoblock('Доп. поля профилей', 'В данном разделе проводится настройка дополнительных полей профиля пользователей', 'xfields', 'xfields');
echoblock('Видео', 'Управление видеозаписями, редактирование и удаление', 'videos', 'video');
echoblock('Музыка', 'Управление аудиозаписями, редактирование и удаление', 'musics', 'music');
echoblock('Альбомы', 'Управление альбомами, редактирование и удаление', 'albums', 'photos');
echoblock('Заметки', 'Управления заметками, которые опубликовали пользователи сайта', 'notes', 'notes');
echoblock('Подарки', 'Управление подарками на сайте, добавление, редактирование и удаление', 'gifts', 'gifts');
echoblock('Сообщества', 'Управление сообществами, редактирование и удаление', 'groups', 'groups');
echoblock('Жалобы '.$new_report, 'Список жалоб, поступивших от посетителей сайта на фотографии, записи, видеозаписи или заметки', 'report', 'report');
echoblock('Шаблоны сайта', 'Редактирование шаблонов, которые используются на вашем сайте', 'tpl', 'tpl');
echoblock('Шаблоны сообщений', 'Настройка шаблонов E-Mail сообщений, которые отсылает скрипт с сайта при уведомлении.', 'mail_tpl', 'mail_tpl');
echoblock('Рассылка сообщений', 'Создание и массовая отправка E-Mail сообщений, для зарегистрированных пользователей', 'mail', 'mail');
echoblock('Фильтр по: IP', 'Блокировка доступа на сайт для определенных IP', 'ban', 'ban');
echoblock('Поиск и Замена', 'Быстрый поиск и замена определенного текста по всей базе данных', 'search', 'search');
echoblock('Статические страницы', 'Создание и редактирование страниц, которые как правило редко изменяются и имеют постоянный адрес', 'static', 'static');
echoblock('Антивирус', 'Проверка папок и файлов скрипта на наличие подозрительных файлов', 'antivirus', 'antivirus');
echoblock('Логи посещений', 'Вывод IP и браузера пользователей при последнем входе на сайт', 'logs', 'logs');
echoblock('Страны', 'Добавление, удаление и редактирование стран', 'country', 'country');
echoblock('Города', 'Добавление, удаление и редактирование городов', 'city', 'city');
echoblock('Игры', 'Добавление, удаление и редактирование игр на сайте', 'apps', 'apps');
echoblock('Отзывы '.$new_reviews, 'Модерация и удаление отзывов.', 'reviews', 'reviews');
echoblock('Отчеты по SMS', 'Просмотр отчетов отправки SMS от пользователей', 'sms', 'sms');
echo <<<HTML
<script type="text/javascript" src="/system/inc/js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$.post('/{$admin_index}', {act: 'send'});
});
</script>
HTML;
echoblock('Фильтр слов', 'Настройка фильтра слов, который будет удалять или заменять указанные слова при добавлении какой-то информации на сайт.', 'wordfilter', 'wordfilter');
echohtmlend();
?>