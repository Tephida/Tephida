<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;
use FluffyDollop\Support\{ Registry};
use FluffyDollop\Filesystem\Filesystem;
use Mozg\classes\Cache;

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');
    $user_info = $user_info ?? Registry::get('user_info');
    $params = array();
    switch ($act) {

        //################### Создание альбома ###################//
        case "create":
            NoAjaxQuery();

            $name = (new Request)->filter('name', 25000, true);
            $descr = (new Request)->filter('descr');
            $privacy = (new Request)->int('privacy');
            $privacy_comm = (new Request)->int('privacy_comm');
            if ($privacy <= 0 or $privacy > 3)
                $privacy = 1;
            if ($privacy_comm <= 0 or $privacy_comm > 3)
                $privacy_comm = 1;
            $sql_privacy = $privacy . '|' . $privacy_comm;

            if (!empty($name)) {

                //Выводи кол-во альбомов у юзера
                $row = $db->super_query("SELECT user_albums_num FROM `users` WHERE user_id = '{$user_info['user_id']}'");
                $config = settings_get();
                if ($row['user_albums_num'] < $config['max_albums']) {
                    //hash
                    $_IP = $_IP ?? null;
                    $hash = md5(md5($server_time) . $name . $descr . md5($user_info['user_id']) . md5($user_info['user_email']) . $_IP);
                    $date_create = date('Y-m-d H:i:s', $server_time);

                    $sql_ = $db->query("INSERT INTO `albums` (user_id, name, descr, ahash, adate, position, privacy) VALUES ('{$user_info['user_id']}', '{$name}', '{$descr}', '{$hash}', '{$date_create}', '0', '{$sql_privacy}')");
                    $id = $db->insert_id();
                    $db->query("UPDATE `users` SET user_albums_num = user_albums_num+1 WHERE user_id = '{$user_info['user_id']}'");

                    Cache::mozgMassClearCacheFile("user_{$user_info['user_id']}/albums|user_{$user_info['user_id']}/albums_all|user_{$user_info['user_id']}/albums_friends|user_{$user_info['user_id']}/albums_cnt_friends|user_{$user_info['user_id']}/albums_cnt_all|user_{$user_info['user_id']}/profile_{$user_info['user_id']}");
                    if ($sql_) {
                        echo '/albums/add/' . $id;
                    } else {
                        echo 'no';
                    }
                } else {
                    echo 'max';
                }
            } else {
                echo 'no_name';
            }

            break;

        //################### Страница создания альбома ###################//
        case "create_page":
            NoAjaxQuery();
            $tpl->load_template('albums_create.tpl');
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        //################### Страница добавление фотографий в альбом ###################//
        case "add":
            $aid = (new Request)->int('aid');
            $user_id = $user_info['user_id'];

            //Проверка на существование альбома
            $row = $db->super_query("SELECT name, aid FROM `albums` WHERE aid = '{$aid}' AND user_id = '{$user_id}'");
            if ($row) {
                $params['metatags']['title'] = $lang['add_photo'];
                $user_speedbar = $lang['add_photo_2'];
                $tpl->load_template('albums_addphotos.tpl');
                $tpl->set('{aid}', $aid);
                $tpl->set('{album-name}', stripslashes($row['name']));
                $tpl->set('{user-id}', $user_id);
                $tpl->set('{PHPSESSID}', $_COOKIE['PHPSESSID']);
                $tpl->compile('content');

                compile($tpl, $params);
            } else {
            }
            break;

        //################### Загрузка фотографии в альбом ###################//
        case "upload":
            NoAjaxQuery();

            $aid = (new Request)->int('aid');
            $user_id = $user_info['user_id'];

            //Проверка на существование альбома и то что загружает владелец альбома
            $row = $db->super_query("SELECT aid, photo_num, cover FROM `albums` WHERE aid = '{$aid}' AND user_id = '{$user_id}'");
            if ($row) {
                $config = settings_get();
                //Проверка на кол-во фоток в альбоме
                if ($row['photo_num'] < $config['max_album_photos']) {

                    //Директория юзеров
                    $uploaddir = ROOT_DIR . '/uploads/users/';

                    $album_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $aid . '/';

                    //Если нет папок юзера, то создаём их
                    try {
                        Filesystem::createDir($uploaddir . $user_id);
                        Filesystem::createDir($uploaddir . $user_id . '/albums');
                        //Если нет папки альбома, то создаём её
                        Filesystem::createDir(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $aid . '/');
                    } catch (Exception $e) {
                        //
                    }

                    //Разрешенные форматы
                    $allowed_files = explode(', ', $config['photo_format']);

                    //Получаем данные о фотографии
                    $image_tmp = $_FILES['uploadfile']['tmp_name'];
                    $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для определения формата
                    $image_rename = substr(md5($server_time + random_int(1, 100000)), 0, 20); // имя фотографии
                    $image_size = $_FILES['uploadfile']['size']; // размер файла
                    $array1 = explode(".", $image_name);
                    $type = end($array1); // формат файла

                    //Проверяем если, формат верный то пропускаем
                    if (in_array(strtolower($type), $allowed_files)) {
                        $config['max_photo_size'] = $config['max_photo_size'] * 1000;
                        if ($image_size < $config['max_photo_size']) {
                            $res_type = strtolower('.' . $type);

                            if (move_uploaded_file($image_tmp, $album_dir . $image_rename . $res_type)) {

                                //Создание оригинала
                                $tmb = new Thumbnail($album_dir . $image_rename . $res_type);
                                $tmb->size_auto('770');
                                $tmb->jpeg_quality('85');
                                $tmb->save($album_dir . $image_rename . $res_type);

                                //Создание маленькой копии
                                $tmb = new Thumbnail($album_dir . $image_rename . $res_type);
                                $tmb->size_auto('140x100');
                                $tmb->jpeg_quality('90');
                                $tmb->save($album_dir . 'c_' . $image_rename . $res_type);

                                $date = date('Y-m-d H:i:s', $server_time);

                                //Генерируем position фотки для "обзор фотографий"
                                $position_all = $_SESSION['position_all'];
                                if ($position_all) {
                                    $position_all = $position_all + 1;
                                    $_SESSION['position_all'] = $position_all;
                                } else {
                                    $position_all = 100000;
                                    $_SESSION['position_all'] = $position_all;
                                }

                                //Вставляем фотографию
                                $db->query("INSERT INTO `photos` (album_id, photo_name, user_id, date, position) VALUES ('{$aid}', '{$image_rename}{$res_type}', '{$user_id}', '{$date}', '{$position_all}')");
                                $ins_id = $db->insert_id();

                                //Проверяем на наличии обложки у альбома, если нету то ставим обложку загруженную фотку
                                if (!$row['cover'])
                                    $db->query("UPDATE `albums` SET cover = '{$image_rename}{$res_type}' WHERE aid = '{$aid}'");

                                $db->query("UPDATE `albums` SET photo_num = photo_num+1, adate = '{$date}' WHERE aid = '{$aid}'");

                                $img_url = $config['home_url'] . 'uploads/users/' . $user_id . '/albums/' . $aid . '/c_' . $image_rename . $res_type;

                                //Результат для ответа
                                echo $ins_id . '|||' . $img_url . '|||' . $user_id;

                                //Удаляем кеш позиций фотографий
//								if(!$photos_num) //WTF?
                                Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);

                                //Чистим кеш
                                Cache::mozgMassClearCacheFile("user_{$user_info['user_id']}/albums|user_{$user_info['user_id']}/albums_all|user_{$user_info['user_id']}/albums_friends|user_{$user_info['user_id']}/position_photos_album_{$aid}");

                                $img_url = str_replace($config['home_url'], '/', $img_url);

                                //Добавляем действия в ленту новостей
                                $generateLastTime = $server_time - 10800;
                                $row = $db->super_query("SELECT ac_id, action_text FROM `news` WHERE action_time > '{$generateLastTime}' AND action_type = 3 AND ac_user_id = '{$user_id}'");
                                if ($row) {
                                    $db->query("UPDATE `news` SET action_text = '{$ins_id}|{$img_url}||{$row['action_text']}', action_time = '{$server_time}' WHERE ac_id = '{$row['ac_id']}'");
                                } else {
                                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 3, action_text = '{$ins_id}|{$img_url}', action_time = '{$server_time}'");
                                }
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
                    echo 'max_img';
                }
            } else {
                echo 'hacking';
            }

            break;

        //################### Удаление фотографии из альбома ###################//
        case "del_photo":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $user_id = $user_info['user_id'];

            $row = $db->super_query("SELECT user_id, album_id, photo_name, comm_num, position FROM `photos` WHERE id = '{$id}'");

            //Если есть такая фотография и владельце действителен
            if ($row['user_id'] == $user_id) {

                //Директория удаления
                $del_dir = ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/';

                //Удаление фотки с сервера
                Filesystem::delete($del_dir . 'c_' . $row['photo_name']);
                Filesystem::delete($del_dir . $row['photo_name']);

                //Удаление фотки из БД
                $db->query("DELETE FROM `photos` WHERE id = '{$id}'");

                $check_photo_album = $db->super_query("SELECT id FROM `photos` WHERE album_id = '{$row['album_id']}'");
                $album_row = $db->super_query("SELECT cover FROM `albums` WHERE aid = '{$row['album_id']}'");

                //Если удаляемая фотография является обложкой, то обновляем обложку на последнюю фотографию, если фотки еще есть из альбома
                if ($album_row['cover'] == $row['photo_name'] and $check_photo_album) {
                    $row_last_photo = $db->super_query("SELECT photo_name FROM `photos` WHERE user_id = '{$user_id}' AND album_id = '{$row['album_id']}' ORDER by `id` DESC");
                    $set_cover = ", cover = '{$row_last_photo['photo_name']}'";
                }

                //Если в альбоме уже нет фоток, то удаляем обложку
                if (!$check_photo_album)
                    $set_cover = ", cover = ''";

                //Удаляем комментарии к фотографии
                $db->query("DELETE FROM `photos_comments` WHERE pid = '{$id}'");

                //Обновляем количество комментов у альбома
                $db->query("UPDATE `albums` SET photo_num = photo_num-1, comm_num = comm_num-{$row['comm_num']} {$set_cover} WHERE aid = '{$row['album_id']}'");

                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$user_info['user_id']}/albums|user_{$user_info['user_id']}/albums_all|user_{$user_info['user_id']}/albums_friends|user_{$row['user_id']}/position_photos_album_{$row['album_id']}");

                //Выводим и удаляем отметки если они есть
                $sql_mark = $db->super_query("SELECT muser_id FROM `photos_mark` WHERE mphoto_id = '" . $id . "' AND mapprove = '0'", true);
                if ($sql_mark) {
                    foreach ($sql_mark as $row_mark) {
                        $db->query("UPDATE `users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '" . $row_mark['muser_id'] . "'");
                    }
                }
                $db->query("DELETE FROM `photos_mark` WHERE mphoto_id = '" . $id . "'");
                //Удаляем оценки
                $db->query("DELETE FROM `photos_rating` WHERE photo_id = '" . $id . "'");
            }

            break;

        //################### Установка новой обложки для альбома ###################//
        case "set_cover":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $user_id = $user_info['user_id'];

            //Выводи фотку из БД, если она есть
            $row = $db->super_query("SELECT album_id, photo_name FROM `photos` WHERE id = '{$id}' AND user_id = '{$user_id}'");
            if ($row) {
                $db->query("UPDATE `albums` SET cover = '{$row['photo_name']}' WHERE aid = '{$row['album_id']}'");

                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$user_info['user_id']}/albums|user_{$user_info['user_id']}/albums_all|user_{$user_info['user_id']}/albums_friends");
            }

            break;

        //################### Сохранение описания к фотографии ###################//
        case "save_descr":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $user_id = $user_info['user_id'];
            $descr = (new Request)->filter('descr');

            //Выводим фотку из БД, если она есть
            $row = $db->super_query("SELECT id FROM `photos` WHERE id = '{$id}' AND user_id = '{$user_id}'");
            if ($row) {
                $db->query("UPDATE `photos` SET descr = '{$descr}' WHERE id = '{$id}' AND user_id = '{$user_id}'");

                //Ответ скрипта
                echo (new Request)->filter('descr');
            }
            break;

        //################### Страница редактирование фотографии ###################//
        case "editphoto":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $user_id = $user_info['user_id'];
            $row = $db->super_query("SELECT descr FROM `photos` WHERE id = '{$id}' AND user_id = '{$user_id}'");
            if ($row)
                echo (new Request)->filter('descr');

            break;

        //################### Сохранение сортировки альбомов ###################//
        case "save_pos_albums":
            NoAjaxQuery();
            $array = (new Request)->filter('album');
            $count = 1;
            $config = settings_get();
            //Если есть данные о массиве
            if ($array and $config['albums_drag'] == 'yes') {
                //Выводим массивом и обновляем порядок
                foreach ($array as $idval) {
                    $idval = intval($idval);
                    $db->query("UPDATE `albums` SET position = " . $count . " WHERE aid = '{$idval}' AND user_id = '{$user_info['user_id']}'");
                    $count++;
                }

                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$user_info['user_id']}/albums|user_{$user_info['user_id']}/albums_all|user_{$user_info['user_id']}/albums_friends");
            }

            break;

        //################### Сохранение сортировки фотографий ###################//
        case "save_pos_photos":
            NoAjaxQuery();
            $array = (new Request)->filter('photo');
            $count = 1;
            $config = settings_get();
            //Если есть данные о массиве
            if ($array && $config['photos_drag'] == 'yes') {
                //Выводим массивом и обновляем порядок
                $row = $db->super_query("SELECT album_id FROM `photos` WHERE id = '{$array[1]}'");
                if ($row) {
                    $photo_info = '';
                    foreach ($array as $idval) {
                        $idval = (int)$idval;
                        $db->query("UPDATE `photos` SET position = '{$count}' WHERE id = '{$idval}' AND user_id = '{$user_info['user_id']}'");
                        $photo_info .= $count . '|' . $idval . '||';
                        $count++;
                    }
                    Cache::mozgCreateCache('user_' . $user_info['user_id'] . '/position_photos_album_' . $row['album_id'], $photo_info);
                }
            }
            break;

        //################### Страница редактирование альбома ###################//
        case "edit_page":
            NoAjaxQuery();
            $user_id = $user_info['user_id'];
            $id = (new Request)->int('id');
            $row = $db->super_query("SELECT aid, name, descr, privacy FROM `albums` WHERE aid = '{$id}' AND user_id = '{$user_id}'");
            if ($row) {
                $album_privacy = explode('|', $row['privacy']);
                $tpl->load_template('albums_edit.tpl');
                $tpl->set('{id}', $row['aid']);
                $tpl->set('{name}', stripslashes($row['name']));
                $tpl->set('{descr}', stripslashes(myBrRn($row['descr'])));
                $tpl->set('{privacy}', $album_privacy[0]);
                $tpl->set('{privacy-text}', strtr($album_privacy[0], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
                $tpl->set('{privacy-comment}', $album_privacy[1]);
                $tpl->set('{privacy-comment-text}', strtr($album_privacy[1], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
                $tpl->compile('content');
                AjaxTpl($tpl);
            }
            break;

        //################### Сохранение настроек альбома ###################//
        case "save_album":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $user_id = $user_info['user_id'];
            $name = (new Request)->filter('name', 25000, true);
            $descr = (new Request)->filter('descr');

            $privacy = (new Request)->int('privacy');
            $privacy_comm = (new Request)->int('privacy_comm');
            if ($privacy <= 0 or $privacy > 3) {
                $privacy = 1;
            }
            if ($privacy_comm <= 0 or $privacy_comm > 3) {
                $privacy_comm = 1;
            }
            $sql_privacy = $privacy . '|' . $privacy_comm;

            //Проверка на существование юзера
            $chekc_user = $db->super_query("SELECT privacy FROM `albums` WHERE aid = '{$id}' AND user_id = '{$user_id}'");
            if ($chekc_user) {
                if (!empty($name)) {
                    $db->query("UPDATE `albums` SET name = '{$name}', descr = '{$descr}', privacy = '{$sql_privacy}' WHERE aid = '{$id}'");
                    echo stripslashes($name) . '|#|||#row#|||#|' . stripslashes($descr);

                    Cache::mozgMassClearCacheFile("user_{$user_id}/albums|user_{$user_id}/albums_all|user_{$user_id}/albums_friends|user_{$user_id}/albums_cnt_friends|user_{$user_id}/albums_cnt_all");
                } else
                    echo 'no_name';
            }
            break;

        //################### Страница изменения обложки ###################//
        case "edit_cover":
            NoAjaxQuery();

            $user_id = $user_info['user_id'];
            $id = (new Request)->int('id');

            if ($user_id and $id) {

                //Для навигатор
                $page = (new Request)->int('page', 1);
                $gcount = 36;
                $limit_page = ($page - 1) * $gcount;

                //Делаем SQL запрос на вывод
                $sql_ = $db->super_query("SELECT id, photo_name FROM `photos` WHERE album_id = '{$id}' AND user_id = '{$user_id}' ORDER by `position` ASC LIMIT {$limit_page}, {$gcount}", true);

                //Если есть SQL запрос то пропускаем
                if ($sql_) {

                    //Выводим данные об альбоме (кол-во фотографий)
                    $row_album = $db->super_query("SELECT photo_num FROM `albums` WHERE aid = '{$id}' AND user_id = '{$user_id}'");

                    $tpl->load_template('albums_editcover.tpl');
                    $tpl->set('[top]', '');
                    $tpl->set('[/top]', '');
                    $tpl->set('{photo-num}', $row_album['photo_num'] . ' ' . declWord($row_album['photo_num'], 'photos'));
                    $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                    $tpl->compile('content');

                    //Выводим массивом фотографии
                    $tpl->load_template('albums_editcover_photo.tpl');
                    $config = settings_get();
                    foreach ($sql_ as $row) {
                        $tpl->set('{photo}', $config['home_url'] . 'uploads/users/' . $user_id . '/albums/' . $id . '/c_' . $row['photo_name']);
                        $tpl->set('{id}', $row['id']);
                        $tpl->set('{aid}', $id);
                        $tpl->compile('content');
                    }
                    box_navigation($gcount, $row_album['photo_num'], $id, 'Albums.EditCover', '');

                    $tpl->load_template('albums_editcover.tpl');
                    $tpl->set('[bottom]', '');
                    $tpl->set('[/bottom]', '');
                    $tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si", "");
                    $tpl->compile('content');

                    AjaxTpl($tpl);
                } else {
                    echo $lang['no_photo_alnumx'];
                }
            } else {
            }

            break;

        //################### Страница всех фотографий юзера, для прикрепления своей фотки кому-то на стену ###################//
        case "all_photos_box":
            NoAjaxQuery();
            $user_id = $user_info['user_id'];
            $notes = (new Request)->int('notes');

            //Для навигатор
            $page = (new Request)->int('page', 1);
            $gcount = 36;
            $limit_page = ($page - 1) * $gcount;

            //Делаем SQL запрос на вывод
            $sql_ = $db->query("SELECT id, photo_name, album_id FROM `photos` WHERE user_id = '{$user_id}' ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}");
            $row_album = $db->super_query("SELECT SUM(photo_num) AS photo_num FROM `albums` WHERE user_id = '{$user_id}'");

            //Если есть Фотографии то пропускаем
            if ($row_album['photo_num']) {
                if ($notes) {
                    $tpl->load_template('notes/attatch_addphoto_top.tpl');
                } else {
                    $tpl->load_template('wall/attatch_addphoto_top.tpl');
                }

                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set('{photo-num}', $row_album['photo_num'] . ' ' . declWord($row_album['photo_num'], 'photos'));
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('content');

                //Выводим циклом фотографии
                if (!$notes) {
                    $tpl->load_template('albums_all_photos.tpl');
                } else {
                    $tpl->load_template('albums_box_all_photos_notes.tpl');
                }

                while ($row = $db->get_row($sql_)) {
                    $tpl->set('{photo}', '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/c_' . $row['photo_name']);
                    $tpl->set('{photo-name}', $row['photo_name']);
                    $tpl->set('{user-id}', $user_id);
                    $tpl->set('{photo-id}', $row['id']);
                    $tpl->set('{aid}', $row['album_id']);
                    $tpl->compile('content');
                }
                box_navigation($gcount, $row_album['photo_num'], $page, 'wall.attach_addphoto', $notes);

                $tpl->load_template('albums_editcover.tpl');
                $tpl->set('[bottom]', '');
                $tpl->set('[/bottom]', '');
                $tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si", "");
                $tpl->compile('content');

                AjaxTpl($tpl);
            } else {
                if ($notes) {
                    $scrpt_insert = "response[1] = response[1].replace('/c_', '/');wysiwyg.boxPhoto(response[1], 0, 0);";
                } else {
                    $scrpt_insert = "var imgname = response[1].split('/');wall.attach_insert('photo', response[1], 'attach|'+imgname[6].replace('c_', ''), response[2]);";
                }

                echo <<<HTML
<script type="text/javascript">
$(document).ready(function(){
	Xajax = new AjaxUpload('upload', {
		action: '/index.php?go=attach',
		name: 'uploadfile',
		onSubmit: function (file, ext) {
			if (!(ext && /^(jpg|png|jpeg|gif|jpe)$/.test(ext))) {
				addAllErr(lang_bad_format, 3300);
				return false;
			}
			Page.Loading('start');
		},
		onComplete: function (file, response){
			if(response == 'big_size'){
				addAllErr(lang_max_size, 3300);
				Page.Loading('stop');
			} else {
				var response = response.split('|||');
				{$scrpt_insert}
				Page.Loading('stop');
			}
		}
	});
});
</script>
HTML;
                echo $lang['no_photo_alnumx'] . '<br /><br /><div class="button_div_gray fl_l" style="margin-left:205px"><button id="upload">Загрузить новую фотографию</button></div>';
            }

            break;

        //################### Удаление альбома ###################//
        case "del_album":
            NoAjaxQuery();
            $hash = isset($_POST['hash']) ? substr($_POST['hash'], 0, 32) : null;
            $row = $db->super_query("SELECT aid, user_id, photo_num FROM `albums` WHERE ahash = '{$hash}'");

            if ($row) {
                $aid = $row['aid'];
                $user_id = $row['user_id'];

                //Удаляем альбом
                $db->query("DELETE FROM `albums` WHERE ahash = '{$hash}'");

                //Проверяем есть ли фотки в альбоме
                if ($row['photo_num']) {

                    //Удаляем фотки
                    $db->query("DELETE FROM `photos` WHERE album_id = '{$aid}'");

                    //Удаляем комментарии к альбому
                    $db->query("DELETE FROM `photos_comments` WHERE album_id = '{$aid}'");

                    //Удаляем фотки из папки на сервере
                    $fdir = opendir(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $aid);
                    while ($file = readdir($fdir)) {
                        Filesystem::delete(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $aid . '/' . $file);
                    }

                    Filesystem::delete(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $aid);
                }

                //Обновляем кол-во альбом в юзера
                $db->query("UPDATE `users` SET user_albums_num = user_albums_num-1 WHERE user_id = '{$user_id}'");

                //Удаляем кеш позиций фотографий и кеш профиля
                Cache::mozgClearCacheFile('user_' . $row['user_id'] . '/position_photos_album_' . $row['aid']);
                Cache::mozgClearCacheFile("user_{$user_info['user_id']}/profile_{$user_info['user_id']}");

                Cache::mozgMassClearCacheFile("user_{$user_id}/albums|user_{$user_id}/albums_all|user_{$user_id}/albums_friends|user_{$user_id}/albums_cnt_friends|user_{$user_id}/albums_cnt_all");
            }

            break;

        //################### Просмотр всех комментариев к альбому ###################//
        case "all_comments":
            $mobile_speedbar = 'Комментарии';

            $user_id = $user_info['user_id'];
            $uid = (new Request)->int('uid');
            $aid = (new Request)->int('aid');

            if ($aid) {
                $uid = false;
            }
            if ($uid) {
                $aid = false;
            }

            $page = (new Request)->int('page', 1);
            $gcount = 25;
            $limit_page = ($page - 1) * $gcount;

            $privacy = true;

            //Если вызваны комменты к альбому
            if ($aid && !$uid) {
                $row_album = $db->super_query("SELECT user_id, name, privacy FROM `albums` WHERE aid = '{$aid}'");
                $album_privacy = explode('|', $row_album['privacy']);
                $uid = $row_album['user_id'];
                if (!$uid) {
                    return '';
                }
            } else {
                $album_privacy = null;
                $row_album = null;
            }

            $CheckBlackList = CheckBlackList($uid);

            if ($user_id != $uid) //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
            {
                $check_friend = CheckFriends($uid);
            } else {
                $check_friend = null;
            }

            if ($aid && $album_privacy) {
                if ($album_privacy[0] == 1 || ($album_privacy[0] == 2 && $check_friend) || $user_id == $uid) {
                    $privacy = true;
                } else {
                    $privacy = false;
                }
            }

            //Приватность
            if ($privacy && !$CheckBlackList) {
                if ($uid && !$aid) {
                    $sql_tb3 = ", `albums` tb3";

                    if ($user_id == $uid) {
                        $privacy_sql = "";
                        $sql_tb3 = "";
                    } elseif ($check_friend) {
                        $privacy_sql = "AND tb1.album_id = tb3.aid AND SUBSTRING(tb3.privacy, 1, 1) regexp '[[:<:]](1|2)[[:>:]]'";
                        $cache_cnt_num = "_friends";
                    } else {
                        $privacy_sql = "AND tb1.album_id = tb3.aid AND SUBSTRING(tb3.privacy, 1, 1) regexp '[[:<:]](1)[[:>:]]'";
                        $cache_cnt_num = "_all";
                    }
                }

                $sql_tb3 = $sql_tb3 ?? null;
                $privacy_sql = $privacy_sql ?? null;

                //Если вызвана страница всех комментариев юзера, если нет, то значит вызвана страница определенного альбома
                if ($uid && !$aid) {
                    $sql_ = $db->super_query("SELECT tb1.user_id, text, date, id, hash, album_id, pid, owner_id, photo_name, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 {$sql_tb3} WHERE tb1.owner_id = '{$uid}' AND tb1.user_id = tb2.user_id {$privacy_sql} ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", true);
                } else {
                    $sql_ = $db->super_query("SELECT tb1.user_id, text, date, id, hash, album_id, pid, owner_id, photo_name, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 WHERE tb1.album_id = '{$aid}' AND tb1.user_id = tb2.user_id ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", true);
                }

                //Выводи имя владельца альбомов
                $row_owner = $db->super_query("SELECT user_name FROM `users` WHERE user_id = '{$uid}'");

                //Если вызвана страница всех комментов
                if ($uid && !$aid) {
                    $user_speedbar = $lang['comm_form_album_all'];
                    $params['metatags']['title'] = $lang['comm_form_album_all'];
                } else {
                    $user_speedbar = $lang['comm_form_album'];
                    $params['metatags']['title'] = $lang['comm_form_album'];
                }

                //Загружаем HEADER альбома
                $tpl->load_template('albums_top.tpl');
                $tpl->set('{user-id}', $uid);
                $tpl->set('{aid}', $aid);
                $tpl->set('{name}', grammaticalName($row_owner['user_name']));
                $tpl->set('{album-name}', stripslashes($row_album['name']));
                $tpl->set('[comments]', '');
                $tpl->set('[/comments]', '');
                $tpl->set_block("'\\[all-albums\\](.*?)\\[/all-albums\\]'si", "");
                $tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si", "");
                $tpl->set_block("'\\[editphotos\\](.*?)\\[/editphotos\\]'si", "");
                $tpl->set_block("'\\[all-photos\\](.*?)\\[/all-photos\\]'si", "");
                if ($uid && !$aid) {
                    $tpl->set_block("'\\[albums-comments\\](.*?)\\[/albums-comments\\]'si", "");
                } else {
                    $tpl->set('[albums-comments]', '');
                    $tpl->set('[/albums-comments]', '');
                    $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si", "");
                }
                if ($uid == $user_id) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                    $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                } else {
                    $tpl->set('[not-owner]', '');
                    $tpl->set('[/not-owner]', '');
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }
                $tpl->compile('info');

                //Если есть ответ о запросе, то выводим
                if ($sql_) {

                    $tpl->load_template('albums_comment.tpl');
                    foreach ($sql_ as $row_comm) {
                        $tpl->set('{comment}', stripslashes($row_comm['text']));
                        $tpl->set('{uid}', $row_comm['user_id']);
                        $tpl->set('{id}', $row_comm['id']);
                        $tpl->set('{hash}', $row_comm['hash']);
                        $tpl->set('{author}', $row_comm['user_search_pref']);

                        $config = settings_get();
                        //Выводим данные о фотографии
                        $tpl->set('{photo}', $config['home_url'] . 'uploads/users/' . $uid . '/albums/' . $row_comm['album_id'] . '/c_' . $row_comm['photo_name']);
                        $tpl->set('{pid}', $row_comm['pid']);
                        $tpl->set('{user-id}', $row_comm['owner_id']);

                        if ($aid) {
                            $tpl->set('{aid}', '_' . $aid);
                            $tpl->set('{section}', 'album_comments');
                        } else {
                            $tpl->set('{aid}', '');
                            $tpl->set('{section}', 'all_comments');
                        }

                        if ($row_comm['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comm['user_id'] . '/50_' . $row_comm['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }

                        OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                        $date_str = megaDate(strtotime($row_comm['date']));
                        $tpl->set('{date}', $date_str);
                        if ($row_comm['user_id'] == $user_info['user_id'] or $user_info['user_id'] == $uid) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                        } else {
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }

                        $tpl->compile('content');
                    }

                    if ($uid && !$aid) {
                        if ($user_id == $uid) {
                            $row_album = $db->super_query("SELECT SUM(comm_num) AS all_comm_num FROM `albums` WHERE user_id = '{$uid}'");
                        } else {
                            $row_album = $db->super_query("SELECT COUNT(*) AS all_comm_num FROM `photos_comments` tb1, `albums` tb3 WHERE tb1.owner_id = '{$uid}' {$privacy_sql}");
                        }
                    } else {
                        $row_album = $db->super_query("SELECT comm_num AS all_comm_num FROM `albums` WHERE aid = '{$aid}'");
                    }

                    if ($uid and !$aid) {
                        navigation($gcount, $row_album['all_comm_num'], $config['home_url'] . 'albums/comments/' . $uid . '/page/');
                    } else {
                        navigation($gcount, $row_album['all_comm_num'], $config['home_url'] . 'albums/view/' . $aid . '/comments/page/');
                    }

                    $user_speedbar = $row_album['all_comm_num'] . ' ' . declWord($row_album['all_comm_num'], 'comments');
                } else {
                    msgbox('', $lang['no_comments'], 'info_2');
                }

            } else {
                $user_speedbar = $lang['title_albums'];
                msgbox('', $lang['no_notes'], 'info');
            }
            compile($tpl, $params);
            break;

        //################### Страница изменения порядка фотографий ###################//
        case "edit_pos_photos":
            $user_id = $user_info['user_id'];
            $aid = (new Request)->int('aid');

            $check_album = $db->super_query("SELECT name FROM `albums` WHERE aid = '{$aid}' AND user_id = '{$user_id}'");

            if ($check_album) {
                /** fixme limit */
                $sql_ = $db->super_query("SELECT id, photo_name FROM `photos` WHERE album_id = '{$aid}' AND user_id = '{$user_id}' ORDER by `position` ASC", true);

                $params['metatags']['title'] = $lang['editphotos'];
                $user_speedbar = $lang['editphotos'];

                $tpl->load_template('albums_top.tpl');
                $tpl->set('{user-id}', $user_id);
                $tpl->set('{aid}', $aid);
                $tpl->set('{album-name}', stripslashes($check_album['name']));
                $tpl->set('[editphotos]', '');
                $tpl->set('[/editphotos]', '');
                $tpl->set_block("'\\[all-albums\\](.*?)\\[/all-albums\\]'si", "");
                $tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si", "");
                $tpl->set_block("'\\[all-photos\\](.*?)\\[/all-photos\\]'si", "");
                $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si", "");
                $tpl->set_block("'\\[albums-comments\\](.*?)\\[/albums-comments\\]'si", "");

                $config = settings_get();
                if ($config['photos_drag'] == 'no') {
                    $tpl->set_block("'\\[admin-drag\\](.*?)\\[/admin-drag\\]'si", "");
                } else {
                    $tpl->set('[admin-drag]', '');
                    $tpl->set('[/admin-drag]', '');
                }

                $tpl->compile('info');

                if ($sql_) {
                    //Добавляем ID для Drag-N-Drop jQuery
                    $tpl->result['content'] .= '<div id="dragndrop"><ul>';
                    $tpl->load_template('albums_editphotos.tpl');
                    foreach ($sql_ as $row) {
                        $tpl->set('{photo}', $config['home_url'] . 'uploads/users/' . $user_id . '/albums/' . $aid . '/c_' . $row['photo_name']);
                        $tpl->set('{id}', $row['id']);
                        $tpl->compile('content');
                    }
                    //Конец ID для Drag-N-Drop jQuery
                    $tpl->result['content'] .= '</div></ul>';
                } else {
                    msgbox('', $lang['no_photos'], 'info_2');
                }

                compile($tpl, $params);

            } else {
                $metatags['title'] = $lang['hacking'];
                $user_speedbar = $lang['no_infooo'];
                msgbox('', $lang['hacking'], 'info_2');
                compile($tpl);
            }
            break;

        //################### Просмотр альбома ###################//
        case "view":
            $mobile_speedbar = 'Просмотр альбома';

            $user_id = $user_info['user_id'];
            $aid = (new Request)->int('aid');

            $page = (new Request)->int('page', 1);
            $gcount = 25;
            $limit_page = ($page - 1) * $gcount;

            //Выводим данные о фотках
            $sql_photos = $db->super_query("SELECT id, photo_name FROM `photos` WHERE album_id = '{$aid}' ORDER by `position` ASC LIMIT {$limit_page}, {$gcount}", true);

            //Выводим данные об альбоме
            $row_album = $db->super_query("SELECT user_id, name, photo_num, privacy FROM `albums` WHERE aid = '{$aid}'");

            //ЧС
            $CheckBlackList = CheckBlackList($row_album['user_id']);
            if (!$CheckBlackList) {
                $album_privacy = explode('|', $row_album['privacy']);
                if (!$row_album) {
                    return '';
                }

                //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                if ($user_id != $row_album['user_id']) {
                    $check_friend = CheckFriends($row_album['user_id']);
                } else {
                    $check_friend = false;
                }

                //Приватность
                if ($album_privacy[0] == 1 || ($album_privacy[0] == 2 && $check_friend) || $user_info['user_id'] == $row_album['user_id']) {
                    //Выводим данные о владельце альбома(ов)
                    $row_owner = $db->super_query("SELECT user_name FROM `users` WHERE user_id = '{$row_album['user_id']}'");

                    $tpl->load_template('albums_top.tpl');
                    $tpl->set('{user-id}', $row_album['user_id']);
                    $tpl->set('{name}', grammaticalName($row_owner['user_name']));
                    $tpl->set('{aid}', $aid);
                    $tpl->set('[view]', '');
                    $tpl->set('[/view]', '');
                    $tpl->set_block("'\\[all-albums\\](.*?)\\[/all-albums\\]'si", "");
                    $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si", "");
                    $tpl->set_block("'\\[editphotos\\](.*?)\\[/editphotos\\]'si", "");
                    $tpl->set_block("'\\[albums-comments\\](.*?)\\[/albums-comments\\]'si", "");
                    $tpl->set_block("'\\[all-photos\\](.*?)\\[/all-photos\\]'si", "");
                    if ($row_album['user_id'] == $user_id) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                        $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    } else {
                        $tpl->set('[not-owner]', '');
                        $tpl->set('[/not-owner]', '');
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                    }
                    $tpl->set('{album-name}', stripslashes($row_album['name']));
                    $tpl->set('{all_p_num}', $row_album['photo_num']);
                    $tpl->set('{aid}', $aid);
                    $tpl->set('{count}', $limit_page);
                    $tpl->compile('info');

                    //Мета теги и формирование спидбара
                    $params['metatags']['title'] = stripslashes($row_album['name']) . ' | ' . $row_album['photo_num'] . ' ' . declWord($row_album['photo_num'], 'photos');
                    $user_speedbar = '<span id="photo_num">' . $row_album['photo_num'] . '</span> ' . declWord($row_album['photo_num'], 'photos');

                    if ($sql_photos) {
                        $tpl->load_template('album_photo.tpl');
                        $config = settings_get();
                        foreach ($sql_photos as $row) {
                            $tpl->set('{photo}', $config['home_url'] . 'uploads/users/' . $row_album['user_id'] . '/albums/' . $aid . '/c_' . $row['photo_name']);
                            $tpl->set('{id}', $row['id']);
                            $tpl->set('{all}', '');
                            $tpl->set('{uid}', $row_album['user_id']);
                            $tpl->set('{aid}', '_' . $aid);
                            $tpl->set('{section}', '');
                            if ($row_album['user_id'] == $user_id) {
                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');
                                $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                            } else {
                                $tpl->set('[not-owner]', '');
                                $tpl->set('[/not-owner]', '');
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }
                            $tpl->compile('content');
                        }
                        navigation($gcount, $row_album['photo_num'], $config['home_url'] . 'albums/view/' . $aid . '/page/');
                    } else
                        msgbox('', '<br /><br />В альбоме нет фотографий<br /><br /><br />', 'info_2');

                    //Проверяем на наличии файла с позициями фоток
                    $check_pos = Cache::mozgCache('user_' . $row_album['user_id'] . '/position_photos_album_' . $aid);

                    //Если нет, то вызываем функцию генерации
                    if (!$check_pos) {
                        GenerateAlbumPhotosPosition($row_album['user_id'], $aid);
                    }

                    compile($tpl, $params);
                } else {
                    $user_speedbar = $lang['error'];
                    msgbox('', $lang['no_notes'], 'info');
                    compile($tpl);
                }
            } else {
                $user_speedbar = $lang['title_albums'];
                msgbox('', $lang['no_notes'], 'info');
                compile($tpl);
            }
            break;

        //################### Страница с новыми фотографиями ###################//
        case "new_photos":
            $rowMy = $db->super_query("SELECT user_new_mark_photos FROM `users` WHERE user_id = '" . $user_info['user_id'] . "'");

            //Формирование тайтла браузера и спидбара
            $params['metatags']['title'] = 'Новые фотографии со мной';
            $user_speedbar = 'Новые фотографии со мной';

            //Загрузка верхушки
            $tpl->load_template('albums_top_newphotos.tpl');
            $tpl->set('{user-id}', $user_info['user_id']);
            $tpl->set('{num}', $rowMy['user_new_mark_photos']);
            $tpl->compile('info');

            //Выводим сами фотографии
            $page = (new Request)->int('page', 1);
            $gcount = 25;
            $limit_page = ($page - 1) * $gcount;
            $sql_ = $db->super_query("SELECT tb1.mphoto_id, tb2.photo_name, album_id, user_id FROM `photos_mark` tb1, `photos` tb2 WHERE tb1.mphoto_id = tb2.id AND tb1.mapprove = 0 AND tb1.muser_id = '" . $user_info['user_id'] . "' ORDER by `mdate` DESC LIMIT " . $limit_page . ", " . $gcount, true);
            $tpl->load_template('albums_top_newphoto.tpl');
            if ($sql_) {
                foreach ($sql_ as $row) {
                    $tpl->set('{uid}', $row['user_id']);
                    $tpl->set('{id}', $row['mphoto_id']);
                    $tpl->set('{aid}', '_' . $row['album_id']);
                    $tpl->set('{photo}', '/uploads/users/' . $row['user_id'] . '/albums/' . $row['album_id'] . '/c_' . $row['photo_name']);
                    $tpl->compile('content');
                }
                $config = settings_get();
                $rowCount = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos_mark` WHERE mapprove = 0 AND muser_id = '" . $user_info['user_id'] . "'");
                navigation($gcount, $rowCount['cnt'], $config['home_url'] . 'albums/newphotos/');
            } else
                msgbox('', '<br /><br /><br />Отметок не найдено.<br /><br /><br />', 'info_2');

            compile($tpl, $params);
            break;

        default:

            //################### Просмотр всех альбомов юзера ###################//
            $mobile_speedbar = 'Альбомы';

            $uid = (new Request)->int('uid');

            //Выводим данные о владельце альбома(ов)
            $row_owner = $db->super_query("SELECT user_search_pref, user_albums_num, user_new_mark_photos FROM `users` WHERE user_id = '{$uid}'");

            if ($row_owner) {
                //ЧС
                $CheckBlackList = CheckBlackList($uid);
                if (!$CheckBlackList) {
                    $author_info = explode(' ', $row_owner['user_search_pref']);

                    $params['metatags']['title'] = $lang['title_albums'] . ' ' . grammaticalName($author_info[0]) . ' ' . grammaticalName($author_info[1]);
                    $user_speedbar = $lang['title_albums'];

                    //Выводи данные об альбоме
                    /** fixme limit */
                    $sql_ = $db->super_query("SELECT aid, name, adate, photo_num, descr, comm_num, cover, ahash, privacy FROM `albums` WHERE user_id = '{$uid}' ORDER by `position` ASC", true);

                    //Если есть альбомы то выводи их
                    if ($sql_) {
                        $m_cnt = $row_owner['user_albums_num'];

                        $tpl->load_template('album.tpl');

                        //Добавляем ID для DragNDrop jQuery
                        $tpl->result['content'] .= '<div id="dragndrop"><ul>';

                        //Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
                        if ($user_info['user_id'] != $uid) {
                            $check_friend = CheckFriends($uid);
                        } else {
                            $check_friend = false;
                        }

                        foreach ($sql_ as $row) {

                            //Приватность
                            $album_privacy = explode('|', $row['privacy']);
                            if ($album_privacy[0] == 1 or $album_privacy[0] == 2 and $check_friend or $user_info['user_id'] == $uid) {
                                if ($user_info['user_id'] == $uid) {
                                    $tpl->set('[owner]', '');
                                    $tpl->set('[/owner]', '');
                                    $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                                } else {
                                    $tpl->set('[not-owner]', '');
                                    $tpl->set('[/not-owner]', '');
                                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                                }

                                $tpl->set('{name}', stripslashes($row['name']));
                                if ($row['descr'])
                                    $tpl->set('{descr}', '<div style="padding-top:4px;">' . stripslashes($row['descr']) . '</div>');
                                else
                                    $tpl->set('{descr}', '');

                                $tpl->set('{photo-num}', $row['photo_num'] . ' ' . declWord($row['photo_num'], 'photos'));
                                $tpl->set('{comm-num}', $row['comm_num'] . ' ' . declWord($row['comm_num'], 'comments'));

                                $date_str = megaDate(strtotime($row['adate']), 1, 1);
                                $tpl->set('{date}', $date_str);
                                $config = settings_get();
                                if ($row['cover'])
                                    $tpl->set('{cover}', $config['home_url'] . 'uploads/users/' . $uid . '/albums/' . $row['aid'] . '/c_' . $row['cover']);
                                else
                                    $tpl->set('{cover}', '/images/no_cover.png');

                                $tpl->set('{aid}', $row['aid']);
                                $tpl->set('{hash}', $row['ahash']);

                                $tpl->compile('content');
                            } else {
                                $m_cnt--;
                            }
                        }

                        //Конец ID для DragNDrop jQuery
                        $tpl->result['content'] .= '</div></ul>';

                        $row_owner['user_albums_num'] = $m_cnt;

                        if ($row_owner['user_albums_num']) {
                            if ($user_info['user_id'] == $uid) {
                                $user_speedbar = 'У Вас <span id="albums_num">' . $row_owner['user_albums_num'] . '</span> ' . declWord($row_owner['user_albums_num'], 'albums');
                            } else {
                                $user_speedbar = 'У ' . grammaticalName($author_info[0]) . ' ' . $row_owner['user_albums_num'] . ' ' . declWord($row_owner['user_albums_num'], 'albums');
                            }

                            $tpl->load_template('albums_top.tpl');
                            $tpl->set('{user-id}', $uid);
                            $tpl->set('{name}', grammaticalName($author_info[0]));
                            $tpl->set('[all-albums]', '');
                            $tpl->set('[/all-albums]', '');
                            $tpl->set_block("'\\[view\\](.*?)\\[/view\\]'si", "");
                            $tpl->set_block("'\\[comments\\](.*?)\\[/comments\\]'si", "");
                            $tpl->set_block("'\\[editphotos\\](.*?)\\[/editphotos\\]'si", "");
                            $tpl->set_block("'\\[albums-comments\\](.*?)\\[/albums-comments\\]'si", "");
                            $tpl->set_block("'\\[all-photos\\](.*?)\\[/all-photos\\]'si", "");

                            //Показ скрытых тексто только для владельца страницы
                            if ($user_info['user_id'] == $uid) {
                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');
                                $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                            } else {
                                $tpl->set('[not-owner]', '');
                                $tpl->set('[/not-owner]', '');
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }

                            if ($config['albums_drag'] == 'no') {
                                $tpl->set_block("'\\[admin-drag\\](.*?)\\[/admin-drag\\]'si", "");
                            }
                            else {
                                $tpl->set('[admin-drag]', '');
                                $tpl->set('[/admin-drag]', '');
                            }

                            if ($row_owner['user_new_mark_photos'] and $user_info['user_id'] == $uid) {
                                $tpl->set('[new-photos]', '');
                                $tpl->set('[/new-photos]', '');
                                $tpl->set('{num}', $row_owner['user_new_mark_photos']);
                            } else {
                                $tpl->set_block("'\\[new-photos\\](.*?)\\[/new-photos\\]'si", "");
                            }

                            $tpl->compile('info');
                        } else {
                            msgbox('', $lang['no_albums'], 'info_2');
                        }
                    } else {
                        $tpl->load_template('albums_info.tpl');
                        //Показ скрытых тексто только для владельца страницы
                        if ($user_info['user_id'] == $uid) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                            $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                        } else {
                            $tpl->set('[not-owner]', '');
                            $tpl->set('[/not-owner]', '');
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }
                        $tpl->compile('content');
                    }

                    compile($tpl, $params);
                } else {
                    $user_speedbar = $lang['error'];
                    msgbox('', $lang['no_notes'], 'info');
                    compile($tpl);
                }
            } else {

            }


    }
    $tpl->clear();
//	$db->free($sql_);
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}