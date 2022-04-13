<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Registry};
use FluffyDollop\Http\Request;

if (Registry::get('logged')) {

    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################## Отмена редактирования ##################//
        case "close":

            $tpl->load_template('photos/editor_close.tpl');
            $tpl->set('{photo}', (new Request)->filter('image'));
            $tpl->compile('content');

            AjaxTpl($tpl);

            break;

        //################## Сохранение отредактированной фотки ##################//
        default:

            $config = settings_get();
            //Разрешенные форматы
            $allowed_files = explode(', ', $config['photo_format']);

            $res_image = (new Request)->filter('image');
            $array = explode('.', $res_image);
            $format = end($array);
            $pid = (new Request)->int('pid');

            if (stripos($_SERVER['HTTP_REFERER'], 'pixlr.com') !== false && $pid && $format) {

                //Выводим информацию о фото
                $row = $db->super_query("SELECT photo_name, album_id FROM `photos` WHERE user_id = '{$user_id}' AND id = '{$pid}'");

                //Проверяем если, формат верный то пропускаем
                if (in_array(strtolower($format), $allowed_files) && $row['photo_name']) {

                    Filesystem::copy($res_image, ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");

                    //Создание оригинала
                    $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
                    $tmb->size_auto('770');
                    $tmb->jpeg_quality('85');
                    $tmb->save(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");

                    //Создание маленькой копии
                    $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
                    $tmb->size_auto('140x100');
                    $tmb->jpeg_quality('90');
                    $tmb->save(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/c_{$row['photo_name']}");

                    $tpl->load_template('photos/editor.tpl');
                    $tpl->set('{photo}', "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}?{$server_time}");
                    $tpl->compile('content');
                    AjaxTpl($tpl);
                }
            } else {
                echo 'Hacking attempt!';
            }
    }
}