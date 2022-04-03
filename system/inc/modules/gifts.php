<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Filesystem;

//Если нажали "Добавить"
if (isset($_POST['save'])) {
    $price = intval($_POST['price']);

    //Разришенные форматы
    $allowed_files = array('jpg', 'png');

    //Получаем данные о фотографии ОРИГИНАЛ
    $image_tmp = $_FILES['original']['tmp_name'];
    $image_name = to_translit($_FILES['original']['name']); // оригинальное название для оприделения формата
    $image_size = $_FILES['original']['size']; // размер файла
    $type = end(explode(".", $image_name)); // формат файла

    //Получаем данные о фотографии КОПИЯ
    $image_tmp_2 = $_FILES['thumbnail']['tmp_name'];
    $image_name_2 = to_translit($_FILES['thumbnail']['name']); // оригинальное название для оприделения формата
    $image_size_2 = $_FILES['thumbnail']['size']; // размер файла
    $type_2 = end(explode(".", $image_name_2)); // формат файла

    //Проверям если, формат верный то пропускаем
    if ($price) {
        if (in_array(strtolower($type), $allowed_files) and in_array(strtolower($type_2), $allowed_files)) {
            if ($image_size < 200000) {
                if ($image_size_2 < 100000) {
                    $rand_name = Registry::get('server_time');
                    move_uploaded_file($image_tmp, ROOT_DIR . '/uploads/gifts/' . $rand_name . '.' . $type);
                    move_uploaded_file($image_tmp_2, ROOT_DIR . '/uploads/gifts/' . $rand_name . '.' . $type_2);
                    $db->query("INSERT INTO `gifts_list` SET img = '" . $rand_name . "', price = '" . $price . "'");
                    msgbox('Информация', 'Подарок успешно добавлен', '?mod=gifts');
                } else
                    msgbox('Ошибка', 'Уменьшеная копия привышает допустимый размер 100 кб', 'javascript:history.go(-1)');
            } else
                msgbox('Ошибка', 'Оригинал привышает допустимый размер 200 кб', 'javascript:history.go(-1)');
        } else
            msgbox('Ошибка', 'Неправильный формат', 'javascript:history.go(-1)');
    } else
        msgbox('Ошибка', 'Укажите цену подарка', 'javascript:history.go(-1)');

    die();
}

//Удаление
if ($_GET['act'] == 'del') {
    $id = intval($_GET['id']);
    $row = $db->super_query("SELECT img FROM `gifts_list` WHERE gid = '" . $id . "'");
    if ($row) {
        $db->query("DELETE FROM `gifts_list` WHERE gid = '" . $id . "'");
        Filesystem::delete(ROOT_DIR . "/uploads/gifts/" . $row['img'] . '.jpg');
        Filesystem::delete(ROOT_DIR . "/uploads/gifts/" . $row['img'] . '.png');
        header('Location: ?mod=gifts');
    }
}

//Сохраняем
if ($_GET['act'] == 'edit') {
    $id = intval($_GET['id']);
    $price = intval($_GET['price']);
    if ($price <= 0) $price = 1;
    $db->query("UPDATE`gifts_list` SET price = '" . $price . "' WHERE gid = '" . $id . "'");
    header('Location: ?mod=gifts');
}

echoheader();

$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `gifts_list`");

$sql_ = $db->super_query("SELECT * FROM `gifts_list` ORDER by `gid` DESC", true);
foreach ($sql_ as $row) {
    $gifts .= <<<HTML
<div style="float:left;width: 150px;height:150px;text-align:center;margin-bottom:15px;margin-top:10px">
<center><img src="/uploads/gifts/{$row['img']}.png" style="margin-bottom:15px" /></center>
Цена: <input type="text" id="price{$row['gid']}" class="inpu" value="{$row['price']}" /><br />
[ <a href="?mod=gifts" onClick="window.location.href='?mod=gifts&act=edit&id={$row['gid']}&price='+document.getElementById('price{$row['gid']}').value; return false">сохранить</a> ] [ <a href="?mod=gifts&act=del&id={$row['gid']}">удалить</a> ]
</div>
HTML;
}

echohtmlstart('Добавление подарка');

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:50px;}
textarea{width:450px;height:400px;}
</style>

<form action="" enctype="multipart/form-data" method="POST">

<input type="hidden" name="mod" value="notes" />

<div class="fllogall" style="width:180px">Цена:</div>
 <input type="text" name="price" class="inpu" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Оригинал .JPG, 256x256:</div>
 <input type="file" name="original" class="inpu" style="width:300px" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">Уменьшеная копия .PNG, 96x96:</div>
 <input type="file" name="thumbnail" class="inpu" style="width:300px" />
<div class="mgcler"></div>

<div class="fllogall" style="width:180px">&nbsp;</div>
 <input type="submit" value="Добавить" class="inp" name="save" style="margin-top:0px" />
</form>
HTML;

echohtmlstart('Список подарков (' . $numRows['cnt'] . ')');

echo <<<HTML
{$gifts}
<div class="clr"></div>
HTML;

echohtmlend();