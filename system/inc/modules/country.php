<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

//Добавление
use Mozg\classes\Cache;

if (isset($_POST['add'])) {
    $country = (new \FluffyDollop\Http\Request)->filter('country', 25000, true);
    if (isset($country) and !empty($country)) {
        $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `country` WHERE name = '" . $country . "'");
        if (!$row['cnt']) {
            $db->query("INSERT INTO `country` SET name = '" . $country . "'");
            Cache::systemMozgClearCacheFile('country');
            msgbox('Информация', 'Страна успешно добавлена', '?mod=country');
        } else
            msgbox('Ошибка', 'Такая страна уже добавлена', 'javascript:history.go(-1)');
    } else
        msgbox('Ошибка', 'Введите название страницы', 'javascript:history.go(-1)');

    die();
}

//Удаление
if ($_GET['act'] === 'del') {
    $id = (int)$_GET['id'];
    $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `country` WHERE id = '" . $id . "'");
    if ($row['cnt']) {
        $db->query("DELETE FROM `country` WHERE id = '" . $id . "'");
        Cache::systemMozgClearCacheFile('country');
        header("Location: ?mod=country");
    }
    die();
}

$sql_ = $db->super_query("SELECT * FROM `country` ORDER by `name` ASC", true);
$countryes = '';
foreach ($sql_ as $row) {
    $countryes .= <<<HTML
<div style="margin-bottom:5px;border-bottom:1px dashed #ccc;padding-bottom:5px">&raquo;&nbsp; <span style="font-size:13px"><b>{$row['name']}</b></span> &nbsp; <span style="color:#777">[ <a href="?mod=country&act=del&id={$row['id']}" style="color:#777">удалить</a> ]</span></div>
HTML;
}

echoheader();
echohtmlstart('Добавление страны');

echo <<<HTML
<form method="POST" action="">
Введите название страны: &nbsp;&nbsp;<input type="text" class="inpu" name="country" />
<input type="submit" value="Добавить" name="add" class="inp" style="margin-top:0px" />
</form>
HTML;

echohtmlstart('Список стран');

echo <<<HTML
{$countryes}
HTML;

echohtmlend();