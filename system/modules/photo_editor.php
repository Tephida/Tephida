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

if (Registry::get('logged')) {

    $act = requestFilter('act');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {

        //################## Отмена редактирования ##################//
        case "close":

            $tpl->load_template('photos/editor_close.tpl');
            $tpl->set('{photo}', requestFilter('image'));
            $tpl->compile('content');

            AjaxTpl();

            break;

        //################## Сохранение отредактированной фотки ##################//
        default:

            //Разрешенные форматы
            $allowed_files = explode(', ', $config['photo_format']);

            $res_image = requestFilter('image');
            $format = end(explode('.', $res_image));
            $pid = intFilter('pid');

            if (stripos($_SERVER['HTTP_REFERER'], 'pixlr.com') !== false and $pid and $format) {

                //Выводим информацию о фото
                $row = $db->super_query("SELECT photo_name, album_id FROM `photos` WHERE user_id = '{$user_id}' AND id = '{$pid}'");

                //Проверяем если, формат верный то пропускаем
                if (in_array(strtolower($format), $allowed_files) and $row['photo_name']) {

                    @copy($res_image, ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");

                    //Подключаем класс для фотографий
                    include ENGINE_DIR . '/classes/images.php';

                    //Создание оригинала
                    $tmb = new thumbnail(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
                    $tmb->size_auto('770');
                    $tmb->jpeg_quality('85');
                    $tmb->save(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");

                    //Создание маленькой копии
                    $tmb = new thumbnail(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}");
                    $tmb->size_auto('140x100');
                    $tmb->jpeg_quality('90');
                    $tmb->save(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$row['album_id']}/c_{$row['photo_name']}");

                    $tpl->load_template('photos/editor.tpl');
                    $tpl->set('{photo}', "/uploads/users/{$user_id}/albums/{$row['album_id']}/{$row['photo_name']}?{$server_time}");
                    $tpl->compile('content');

                    AjaxTpl();

                }

            } else
                echo 'Hacking attempt!';

    }

}

exit();