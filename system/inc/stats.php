<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG'))
    die('Hacking attempt!');

echoheader();
echohtmlstart('Общая статистика сайта');

$users = $db->super_query("SELECT COUNT(*) AS cnt FROM `users`");
$albums = $db->super_query("SELECT COUNT(*) AS cnt FROM `albums`");
$attach = $db->super_query("SELECT COUNT(*) AS cnt FROM `attach`");
$audio = $db->super_query("SELECT COUNT(*) AS cnt FROM `audio`");
$groups = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities`");
$groups_wall = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_wall`");
$invites = $db->super_query("SELECT COUNT(*) AS cnt FROM `invites`");
$notes = $db->super_query("SELECT COUNT(*) AS cnt FROM `notes`");
$videos = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos`");

$db->query("SHOW TABLE STATUS FROM `" . DBNAME . "`");
$mysql_size = 0;
while ($r = $db->get_array()) {
    if (str_contains($r['Name'], ""))
        $mysql_size += $r['Data_length'] + $r['Index_length'];
}
$db->free();
$mysql_size = formatsize($mysql_size);

$cache_size = formatsize(Filesystem::dirSize("uploads"));

echo <<<HTML

<div class="fllogall">Размер базы данных MySQL:</div>
 <div style="margin-bottom:10px">{$mysql_size}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Размер папки /uploads/:</div>
 <div style="margin-bottom:10px">{$cache_size}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Зарегистрировано пользователей:</div>
 <div style="margin-bottom:10px">{$users['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество созданных альбомов:</div>
 <div style="margin-bottom:10px">{$albums['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество прикрепленных фото:</div>
 <div style="margin-bottom:10px">{$attach['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество аудиозаписей:</div>
 <div style="margin-bottom:10px">{$audio['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество сообществ:</div>
 <div style="margin-bottom:10px">{$groups['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество записей на стенах сообществ:</div>
 <div style="margin-bottom:10px">{$groups_wall['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество приглашеных пользователей:</div>
 <div style="margin-bottom:10px">{$invites['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество заметок:</div>
 <div style="margin-bottom:10px">{$notes['cnt']}&nbsp;</div>
<div class="mgcler"></div>

<div class="fllogall">Количество видеозаписей:</div>
 <div style="margin-bottom:10px">{$videos['cnt']}&nbsp;</div>
<div class="mgcler"></div>

HTML;

echohtmlend();