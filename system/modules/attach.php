<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Filesystem, Registry, Thumbnail};

NoAjaxQuery();

if (Registry::get('logged')) {
    $server_time = Registry::get('server_time');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $db = Registry::get('db');
    //Если нет папки альбома, то создаём её
    $album_dir = ROOT_DIR . "/uploads/attach/{$user_id}/";
    Filesystem::createDir($album_dir);

    //Разрешенные форматы
    $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

    //Получаем данные о фотографии
    $image_tmp = $_FILES['uploadfile']['tmp_name'];
    $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для определения формата
    $image_rename = substr(md5($server_time + random_int(1, 100000)), 0, 20); // имя фотографии
    $image_size = $_FILES['uploadfile']['size']; // размер файла
    $array = explode(".", $image_name);
    $type = end($array); // формат файла

    //Проверяем если, формат верный то пропускаем
    if (in_array(strtolower($type), $allowed_files)) {
        if ($image_size < 5000000) {
            $res_type = strtolower('.' . $type);

            if (move_uploaded_file($image_tmp, $album_dir . $image_rename . $res_type)) {
                //Создание оригинала
                $tmb = new Thumbnail($album_dir . $image_rename . $res_type);
                $tmb->size_auto('770');
                $tmb->jpeg_quality('95');
                $tmb->save($album_dir . $image_rename . $res_type);

                //Создание маленькой копии
                $tmb = new Thumbnail($album_dir . $image_rename . $res_type);
                $tmb->size_auto('140x100');
                $tmb->jpeg_quality('95');
                $tmb->save($album_dir . 'c_' . $image_rename . $res_type);

                //Вставляем фотографию
                $db->query("INSERT INTO `attach` SET photo = '{$image_rename}{$res_type}', ouser_id = '{$user_id}', add_date = '{$server_time}'");
                $ins_id = $db->insert_id();
                $config = settings_get();
                $img_url = $config['home_url'] . 'uploads/attach/' . $user_id . '/c_' . $image_rename . $res_type;

                //Результат для ответа
                echo $image_rename . $res_type . '|||' . $img_url . '|||' . $user_id;
            } else {
                echo 'big_size';
            }
        } else {
            echo 'big_size';
        }
    } else {
        echo 'bad_format';
    }
} else {
    echo 'no_log';
}