<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Registry};
use FluffyDollop\Filesystem\Filesystem;
use FluffyDollop\Http\Request;
use Mozg\classes\Cache;
use Mozg\classes\Flood;

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $page = (new Request)->int('page', 1);
    $db = Registry::get('db');
    $gcount = 20;
    $limit_page = ($page - 1) * $gcount;

    $metatags['title'] = $lang['communities'];

    switch ($act) {

        //################### Отправка сообщества БД ###################//
        case "send":
            NoAjaxQuery();
            $title = (new Request)->filter('title', 60, true);
            if (Flood::check('groups')) {
                echo 'no_title';//fixme
            } else if (!empty($title)) {

                Flood::LogInsert('groups');
                $db->query("INSERT INTO `communities` SET title = '{$title}', type = 1, traf = 1, ulist = '|{$user_id}|', date = NOW(), admin = 'u{$user_id}|', real_admin = '{$user_id}', comments = 1");
                $cid = $db->insert_id();
                $db->query("INSERT INTO `friends` SET friend_id = '{$cid}', user_id = '{$user_id}', friends_date = NOW(), subscriptions = 2");
                $db->query("UPDATE `users` SET user_public_num = user_public_num+1 WHERE user_id = '{$user_id}'");

                Filesystem::createDir(ROOT_DIR . '/uploads/groups/' . $cid . '/');
                Filesystem::createDir(ROOT_DIR . '/uploads/groups/' . $cid . '/photos/');
                Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|groups/{$user_id}");

                echo $cid;
            } else {
                echo 'no_title';
            }
            break;

        //################### Выход из сообщества ###################//
        case "exit":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `friends` WHERE friend_id = '{$id}' AND user_id = '{$user_id}' AND subscriptions = 2");
            if ($check['cnt']) {
                $db->query("DELETE FROM `friends` WHERE friend_id = '{$id}' AND user_id = '{$user_id}' AND subscriptions = 2");
                $db->query("UPDATE `users` SET user_public_num = user_public_num-1 WHERE user_id = '{$user_id}'");
                $db->query("UPDATE `communities` SET traf = traf-1, ulist = REPLACE(ulist, '|{$user_id}|', '') WHERE id = '{$id}'");

                //Записываем в статистику "Вышедшие участники"
                $stat_date = date('Y-m-d', $server_time);
                $stat_x_date = date('Y-m', $server_time);
                $stat_date = strtotime($stat_date);
                $stat_x_date = strtotime($stat_x_date);

                $check_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats` WHERE gid = '{$id}' AND date = '{$stat_date}'");
                $check_user_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats_log` WHERE gid = '{$id}' AND user_id = '{$user_info['user_id']}' AND date = '{$stat_date}' AND act = '3'");

                if (!$check_user_stat['cnt']) {
                    if ($check_stat['cnt']) {
                        $db->query("UPDATE `communities_stats` SET exit_users = exit_users + 1 WHERE gid = '{$id}' AND date = '{$stat_date}'");
                    } else {
                        $db->query("INSERT INTO `communities_stats` SET gid = '{$id}', date = '{$stat_date}', exit_users = '1', date_x = '{$stat_x_date}'");
                    }
                    $db->query("INSERT INTO `communities_stats_log` SET user_id = '{$user_info['user_id']}', date = '{$stat_date}', act = '3', gid = '{$id}'");
                }
                Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|groups/{$user_id}");
            }
            break;

        //################### Страница загрузки главного фото сообщества ###################//
        case "loadphoto_page":
            NoAjaxQuery();
            $tpl->load_template('groups/load_photo.tpl');
            $tpl->set('{id}', (new Request)->int('id'));
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        //################### Загрузка и изменение главного фото сообщества ###################//
        case "loadphoto":
            NoAjaxQuery();

            $id = (new Request)->int('id');

            //Проверка на то, что фото обновляет адмиH
            $row = $db->super_query("SELECT admin, photo, del, ban FROM `communities` WHERE id = '{$id}'");
            if (stripos($row['admin'], "u{$user_id}|") !== false && $row['del'] == 0 && $row['ban'] == 0) {

                //Разрешенные форматы
                $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

                //Получаем данные о фотографии
                $image_tmp = $_FILES['uploadfile']['tmp_name'];
                $image_name = to_translit($_FILES['uploadfile']['name']); // оригинальное название для оприделения формата
                $image_rename = substr(md5($server_time + random_int(1, 100000)), 0, 20); // имя фотографии
                $image_size = $_FILES['uploadfile']['size']; // размер файла
                $array = explode(".", $image_name);
                $type = end($array); // формат файла

                //Проверяем если, формат верный то пропускаем
                if (in_array(strtolower($type), $allowed_files)) {
                    if ($image_size < 5000000) {
                        $res_type = strtolower('.' . $type);

                        $upload_dir = ROOT_DIR . "/uploads/groups/{$id}/";

                        if (move_uploaded_file($image_tmp, $upload_dir . $image_rename . $res_type)) {
                            //Создание оригинала
                            $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                            $tmb->size_auto('200', 1);
                            $tmb->jpeg_quality('97');
                            $tmb->save($upload_dir . $image_rename . $res_type);

                            //Создание маленькой копии 100
                            $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                            $tmb->size_auto('100x100');
                            $tmb->jpeg_quality('100');
                            $tmb->save($upload_dir . '100_' . $image_rename . $res_type);

                            //Создание маленькой копии 50
                            $tmb = new Thumbnail($upload_dir . $image_rename . $res_type);
                            $tmb->size_auto('50x50');
                            $tmb->jpeg_quality('100');
                            $tmb->save($upload_dir . '50_' . $image_rename . $res_type);

                            if ($row['photo']) {
                                Filesystem::delete($upload_dir . $row['photo']);
                                Filesystem::delete($upload_dir . '50_' . $row['photo']);
                                Filesystem::delete($upload_dir . '100_' . $row['photo']);
                            }

                            //Вставляем фотографию
                            $db->query("UPDATE `communities` SET photo = '{$image_rename}{$res_type}' WHERE id = '{$id}'");

                            //Результат для ответа
                            echo $image_rename . $res_type;

                            Cache::mozgClearCacheFolder('groups');
                            Cache::mozgClearCacheFile("wall/group{$id}");

                        } else {
                            echo 'big_size';
                        }
                    } else {
                        echo 'big_size';
                    }
                } else {
                    echo 'bad_format';
                }
            }

            break;

        //################### Удаление фото сообщества ###################//
        case "delphoto":
            NoAjaxQuery();
            $id = (new Request)->int('id');

            //Проверка на то, что фото удалит админ
            $row = $db->super_query("SELECT photo, admin FROM `communities` WHERE id = '{$id}'");
            if (stripos($row['admin'], "u{$user_id}|") !== false) {
                $upload_dir = ROOT_DIR . "/uploads/groups/{$id}/";
                Filesystem::delete($upload_dir . $row['photo']);
                Filesystem::delete($upload_dir . '50_' . $row['photo']);
                Filesystem::delete($upload_dir . '100_' . $row['photo']);
                $db->query("UPDATE `communities` SET photo = '' WHERE id = '{$id}'");

                Cache::mozgClearCacheFolder('groups');
                Cache::mozgClearCacheFile("wall/group{$id}");
            }

            break;

        //################### Вступление в сообщество ###################//
        case "login":
            NoAjaxQuery();
            $id = (new Request)->int('id');

            //Проверка на существования юзера в сообществе
            $row = $db->super_query("SELECT ulist, del, ban FROM `communities` WHERE id = '{$id}'");

            if (stripos($row['ulist'], "|{$user_id}|") === false && $row['del'] == 0 && $row['ban'] == 0) {

                $ulist = $row['ulist'] . "|{$user_id}|";

                //Обновляем кол-во людей в сообществе
                $db->query("UPDATE `communities` SET traf = traf+1, ulist = '{$ulist}' WHERE id = '{$id}'");

                //Подписываемся
                $db->query("INSERT INTO `friends` SET friend_id = '{$id}', user_id = '{$user_id}', friends_date = NOW(), subscriptions = 2");

                //Записываем в статистику "Новые участники"
                $stat_date = date('Y-m-d', $server_time);
                $stat_x_date = date('Y-m', $server_time);
                $stat_date = strtotime($stat_date);
                $stat_x_date = strtotime($stat_x_date);

                $check_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats` WHERE gid = '{$id}' AND date = '{$stat_date}'");
                $check_user_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats_log` WHERE gid = '{$id}' AND user_id = '{$user_info['user_id']}' AND date = '{$stat_date}' AND act = '2'");

                if (!$check_user_stat['cnt']) {
                    if ($check_stat['cnt']) {
                        $db->query("UPDATE `communities_stats` SET new_users = new_users + 1 WHERE gid = '{$id}' AND date = '{$stat_date}'");
                    } else {
                        $db->query("INSERT INTO `communities_stats` SET gid = '{$id}', date = '{$stat_date}', new_users = '1', date_x = '{$stat_x_date}'");
                    }
                    $db->query("INSERT INTO `communities_stats_log` SET user_id = '{$user_info['user_id']}', date = '{$stat_date}', act = '2', gid = '{$id}'");
                }

                //Проверка на приглашению юзеру
                $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_join` WHERE for_user_id = '{$user_id}' AND public_id = '{$id}'");

                //Если есть приглашение, то удаляем его
                if ($check['cnt']) {
                    $db->query("DELETE FROM `communities_join` WHERE for_user_id = '{$user_id}' AND public_id = '{$id}'");
                    $appSQLDel = ", invties_pub_num = invties_pub_num - 1";
                } else {
                    $appSQLDel = null;
                }

                //Обновляем кол-во сообществ у юзера
                $db->query("UPDATE `users` SET user_public_num = user_public_num + 1 {$appSQLDel} WHERE user_id = '{$user_id}'");

                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$user_id}/profile_{$user_id}|groups/{$user_id}");
            }

            break;

        //################### Страница добавления контактов ###################//
        case "addfeedback_pg":
            NoAjaxQuery();
            $tpl->load_template('groups/addfeedback_pg.tpl');
            $tpl->set('{id}', (new Request)->int('id'));
            $tpl->compile('content');
            AjaxTpl($tpl);

            break;

        //################### Добавления контакт в БД ###################//
        case "addfeedback_db":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $upage = (new Request)->int('upage');
            $office = (new Request)->filter('office', 25000, true);
            $phone = (new Request)->filter('phone', 25000, true);
            $email = (new Request)->filter('email', 25000, true);

            //Проверка на то, что действиие делает админ
            $checkAdmin = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$id}'");

            //Проверяем что такой юзер есть на сайте
            $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$upage}'");

            //Проверяем на то что юзера нет в списке контактов
            $checkSec = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_feedback` WHERE fuser_id = '{$upage}' AND cid = '{$id}'");

            if ($row['cnt'] && stripos($checkAdmin['admin'], "u{$user_id}|") !== false and !$checkSec['cnt']) {
                $db->query("UPDATE `communities` SET feedback = feedback+1 WHERE id = '{$id}'");
                $db->query("INSERT INTO `communities_feedback` SET cid = '{$id}', fuser_id = '{$upage}', office = '{$office}', fphone = '{$phone}', femail = '{$email}', fdate = '{$server_time}'");
            } else {
                echo 1;
            }

            break;

        //################### Удаление контакта из БД ###################//
        case "delfeedback":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $uid = (new Request)->int('uid');

            //Проверка на то, что действие делает админ
            $checkAdmin = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$id}'");

            //Проверяем на то что юзера есть в списке контактов
            $checkSec = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_feedback` WHERE fuser_id = '{$uid}' AND cid = '{$id}'");

            if (stripos($checkAdmin['admin'], "u{$user_id}|") !== false and $checkSec['cnt']) {
                $db->query("UPDATE `communities` SET feedback = feedback-1 WHERE id = '{$id}'");
                $db->query("DELETE FROM `communities_feedback` WHERE fuser_id = '{$uid}' AND cid = '{$id}'");
            }

            break;

        //################### Выводим фотографию юзера при указании ИД страницы ###################//
        case "checkFeedUser":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $row = $db->super_query("SELECT user_photo, user_search_pref FROM `users` WHERE user_id = '{$id}'");
            if ($row) echo $row['user_search_pref'] . "|" . $row['user_photo'];

            break;

        //################### Сохранение отредактированных данных контакт в БД ###################//
        case "editfeeddave":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $upage = (new Request)->int('uid');
            $office = (new Request)->filter('office', 25000, true);
            $phone = (new Request)->filter('phone', 25000, true);
            $email = (new Request)->filter('email', 25000, true);

            //Проверка на то, что действие делает админ
            $checkAdmin = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$id}'");

            //Проверяем на то что юзера есть в списке контактов
            $checkSec = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_feedback` WHERE fuser_id = '{$upage}' AND cid = '{$id}'");

            if (stripos($checkAdmin['admin'], "u{$user_id}|") !== false && $checkSec['cnt']) {
                $db->query("UPDATE `communities_feedback` SET office = '{$office}', fphone = '{$phone}', femail = '{$email}' WHERE fuser_id = '{$upage}' AND cid = '{$id}'");

                Cache::mozgClearCacheFile("wall/group{$id}");

            } else {
                echo 1;
            }

            break;

        //################### Все контакты (БОКС) ###################//
        case "allfeedbacklist":
            NoAjaxQuery();
            $id = (new Request)->int('id');

            //Выводим ИД админа
            $owner = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$id}'");

            $sql_ = $db->super_query("SELECT tb1.fuser_id, office, fphone, femail, tb2.user_search_pref, user_photo FROM `communities_feedback` tb1, `users` tb2 WHERE tb1.cid = '{$id}' AND tb1.fuser_id = tb2.user_id ORDER by `fdate` ASC", true);
            $tpl->load_template('groups/allfeedbacklist.tpl');
            if ($sql_) {
                foreach ($sql_ as $row) {
                    $tpl->set('{id}', $id);
                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{office}', stripslashes($row['office']));
                    $tpl->set('{phone}', stripslashes($row['fphone']));
                    $tpl->set('{user-id}', $row['fuser_id']);
                    if ($row['fphone'] && $row['femail']) {
                        $tpl->set('{email}', ', ' . stripslashes($row['femail']));
                    } else {
                        $tpl->set('{email}', stripslashes($row['femail']));
                    }
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $row['fuser_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    if (stripos($owner['admin'], "u{$user_id}|") !== false) {
                        $tpl->set('[admin]', '');
                        $tpl->set('[/admin]', '');
                    } else {
                        $tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si", "");
                    }
                    $tpl->compile('content');
                }
                AjaxTpl($tpl);
            } else {
                echo '<div align="center" style="padding-top:10px;color:#777;font-size:13px;">Список контактов пуст.</div>';
            }

            if (stripos($owner['admin'], "u{$user_id}|") !== false) {
                echo "<style>#box_bottom_left_text{padding-top:6px;float:left}</style><script>$('#box_bottom_left_text').html('<a href=\"/\" onClick=\"groups.addcontact({$id}); return false\">Добавить контакт</a>');</script>";
            }

            break;

        //################### Сохранение отредактированных данных группы ###################//
        case "saveinfo":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $comments = (new Request)->int('comments');
            $discussion = (new Request)->int('discussion');
            $title = (new Request)->filter('title', 25000, true);
            $adres_page = strtolower((new Request)->filter('adres_page', 25000, true));
            $descr = (new Request)->filter('descr', 5000);

            $web = (new Request)->filter('web', 25000, true);

            $web = str_replace(array('"', "'"), '', $web);

            if (!preg_match("/^[a-zA-Z0-9_-]+$/", $adres_page)) {
                $adress_ok = false;
            } else {
                $adress_ok = true;
            }

            //Проверка на то, что действие делает админ
            $checkAdmin = $db->super_query("SELECT admin FROM `communities` WHERE id = '" . $id . "'");

            if (stripos($checkAdmin['admin'], "u{$user_id}|") !== false && !empty($title) && $adress_ok) {
                if (preg_match('/public[0-9]/i', $adres_page)) {
                    $adres_page = '';
                }

                $adres_page = preg_replace('/\b(u([0-9]+)|friends|editmypage|albums|photo([0-9]+)_([0-9]+)|photo([0-9]+)_([0-9]+)_([0-9]+)|fave|notes|videos|video([0-9]+)_([0-9]+)|news|messages|wall([0-9]+)|settings|support|restore|blog|balance|nonsense|reg([0-9]+)|gifts([0-9]+)|groups|wallgroups([0-9]+)_([0-9]+)|audio|audio([0-9]+)|docs|apps|app([0-9]+)|public|forum([0-9]+)|public([0-9]+))\b/i', '', $adres_page);

                //Проверка на то, что адрес страницы свободен
                if ($adres_page) {
                    $checkAdres = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities` WHERE adres = '" . $adres_page . "' AND id != '" . $id . "'");
                } else {
                    $checkAdres = null;
                }

                if (!$checkAdres['cnt'] || empty($adres_page)) {
                    $db->query("UPDATE `communities` SET title = '" . $title . "', descr = '" . $descr . "', comments = '" . $comments . "', discussion = '{$discussion}', adres = '" . $adres_page . "', web = '{$web}' WHERE id = '" . $id . "'");
                    if (!$adres_page) {
                        echo 'no_new';
                    }
                } else {
                    echo 'err_adres';
                }

                Cache::mozgClearCacheFolder('groups');
                Cache::mozgClearCacheFile("wall/group{$id}");
            }

            break;

        //################### Выводим информацию о пользователе которого будем делать админом ###################//
        case "new_admin":
            NoAjaxQuery();
            $new_admin_id = (new Request)->int('new_admin_id');
            $row = $db->super_query("SELECT tb1.user_id, tb2.user_photo, user_search_pref, user_sex FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$new_admin_id}' AND tb1.user_id = tb2.user_id AND tb1.subscriptions = 2");
            if ($row && $user_id !== $new_admin_id) {
                if ($row['user_photo']) {
                    $ava = "/uploads/users/{$new_admin_id}/100_{$row['user_photo']}";
                } else {
                    $ava = "/templates/{$config['temp']}/images/100_no_ava.png";
                }
                if ($row['user_sex'] == 1) {
                    $gram = 'был';
                } else {
                    $gram = 'была';
                }
                echo "<div style=\"padding:15px\"><img src=\"{$ava}\" align=\"left\" style=\"margin-right:10px\" id=\"adm_ava\" />Вы хотите чтоб <b id=\"adm_name\">{$row['user_search_pref']}</b> {$gram} одним из руководителей страницы?</div>";
            } else {
                echo "<div style=\"padding:15px\"><div class=\"err_red\">Пользователь с таким адресом страницы не подписан на эту страницу.</div></div><script>$('#box_but').hide()</script>";
            }

            break;

        //################### Запись нового админа в БД ###################//
        case "send_new_admin":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $new_admin_id = (new Request)->int('new_admin_id');
            $row = $db->super_query("SELECT admin, ulist FROM `communities` WHERE id = '{$id}'");
            if (stripos($row['admin'], "u{$user_id}|") !== false and stripos($row['admin'], "u{$new_admin_id}|") === false and stripos($row['ulist'], "|{$user_id}|") !== false) {
                $admin = $row['admin'] . "u{$new_admin_id}|";
                $db->query("UPDATE `communities` SET admin = '{$admin}' WHERE id = '{$id}'");
            }

            break;

        //################### Удаление админа из БД ###################//
        case "deladmin":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $uid = (new Request)->int('uid');
            $row = $db->super_query("SELECT admin, ulist, real_admin FROM `communities` WHERE id = '{$id}'");
            if (stripos($row['admin'], "u{$user_id}|") !== false and stripos($row['admin'], "u{$uid}|") !== false and $uid != $row['real_admin']) {
                $admin = str_replace("u{$uid}|", '', $row['admin']);
                $db->query("UPDATE `communities` SET admin = '{$admin}' WHERE id = '{$id}'");
            }

            break;

        //################### Добавление записи на стену ###################//
        case "wall_send":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            $wall_text = (new Request)->filter('wall_text');
            $attach_files = (new Request)->filter('attach_files', 25000, true);

            //Проверка на админа
            $row = $db->super_query("SELECT admin, del, ban FROM `communities` WHERE id = '{$id}'");
            if (stripos($row['admin'], "u{$user_id}|") === false) {
                die();
            }

            if (!empty($wall_text) || !empty($attach_files) || $row['del'] == 0 && $row['ban'] == 0) {

                //Оприделение изображения к ссылке
                if (stripos($attach_files, 'link|') !== false) {
                    $attach_arr = explode('||', $attach_files);
                    $cnt_attach_link = 1;
                    foreach ($attach_arr as $attach_file) {
                        $attach_type = explode('|', $attach_file);
                        if ($attach_type[0] == 'link' && preg_match('/https:\/\/(.*?)+$/i', $attach_type[1]) && $cnt_attach_link == 1) {
                            $domain_url_name = explode('/', $attach_type[1]);
                            $rdomain_url_name = str_replace('https://', '', $domain_url_name[2]);
                            $rImgUrl = $attach_type[4];
                            $rImgUrl = str_replace("\\", "/", $rImgUrl);
                            $img_name_arr = explode(".", $rImgUrl);
                            $img_format = to_translit(end($img_name_arr));
                            $image_name = substr(md5($server_time . md5($rImgUrl)), 0, 15);

                            //Разрешенные форматы
                            $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

                            //Загружаем картинку на сайт
                            if (in_array(strtolower($img_format), $allowed_files) and preg_match("/https:\/\/(.*?)(.jpg|.png|.gif|.jpeg|.jpe)/i", $rImgUrl)) {

                                //Директория загрузки фото
                                $upload_dir = ROOT_DIR . '/uploads/attach/' . $user_id;
                                Filesystem::createDir($upload_dir);

                                //Подключаем класс для фотографий
                                if (Filesystem::copy($rImgUrl, $upload_dir . '/' . $image_name . '.' . $img_format)) {
                                    $tmb = new Thumbnail($upload_dir . '/' . $image_name . '.' . $img_format);
                                    $tmb->size_auto('100x80');
                                    $tmb->jpeg_quality(100);
                                    $tmb->save($upload_dir . '/' . $image_name . '.' . $img_format);

                                    $attach_files = str_replace($attach_type[4], '/uploads/attach/' . $user_id . '/' . $image_name . '.' . $img_format, $attach_files);
                                }
                            }
                            $cnt_attach_link++;
                        }
                    }
                }

                $attach_files = str_replace(array('vote|', '&amp;#124;', '&amp;raquo;', '&amp;quot;'), array('hack|', '&#124;', '&raquo;', '&quot;'), $attach_files);

                //Голосование
                $vote_title = (new Request)->filter('vote_title', 25000, true);
                $vote_answer_1 = (new Request)->filter('vote_answer_1', 25000, true);

                $ansers_list = array();

                if (!empty($vote_title) && !empty($vote_answer_1)) {

                    for ($vote_i = 1; $vote_i <= 10; $vote_i++) {
                        $vote_answer = (new Request)->filter('vote_answer_' . $vote_i, 25000, true);
                        $vote_answer = str_replace('|', '&#124;', $vote_answer);
                        if ($vote_answer) {
                            $ansers_list[] = $vote_answer;
                        }

                    }

                    $sql_answers_list = implode('|', $ansers_list);

                    //Вставляем голосование в БД
                    $db->query("INSERT INTO `votes` SET title = '{$vote_title}', answers = '{$sql_answers_list}'");

                    $attach_files .= "vote|{$db->insert_id()}||";

                }

                //Вставляем саму запись в БД
                $db->query("INSERT INTO `communities_wall` SET public_id = '{$id}', text = '{$wall_text}', attach = '{$attach_files}', add_date = '{$server_time}'");
                $dbid = $db->insert_id();
                $db->query("UPDATE `communities` SET rec_num = rec_num+1 WHERE id = '{$id}'");

                //Вставляем в ленту новостей
                $db->query("INSERT INTO `news` SET ac_user_id = '{$id}', action_type = 11, action_text = '{$wall_text}', obj_id = '{$dbid}', action_time = '{$server_time}'");

                //Загружаем все записи
                if (stripos($row['admin'], "u{$user_id}|") !== false) {
                    $public_admin = true;
                } else {
                    $public_admin = false;
                }

                $limit_select = 10;
                $pid = $id;
                include ENGINE_DIR . '/classes/wall.public.php';//fixme
                $wall = new wall();
                $wall->query("SELECT tb1.id, text, public_id, add_date, fasts_num, attach, likes_num, likes_users, tell_uid, public, tell_date, tell_comm, tb2.title, photo, comments, fixed FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.public_id = '{$id}' AND tb1.public_id = tb2.id AND fast_comm_id = 0 ORDER by `fixed` DESC, `add_date` DESC LIMIT 0, {$limit_select}");
                $wall->template('groups/record.tpl');
                $wall->compile('content');
                $wall->select($public_admin, $server_time);
                AjaxTpl($tpl);
            }

            break;

        //################### Добавление комментария к записи ###################//
        case "wall_send_comm":
            NoAjaxQuery();

            if (Flood::check('comments')) {
                //fixme
            } else {
                $rec_id = (new Request)->int('rec_id');
                $public_id = (new Request)->int('public_id');
                $wall_text = (new Request)->filter('wall_text');
                $answer_comm_id = (new Request)->int('answer_comm_id');

                //Проверка на админа и проверяем включены ли комменты
                $row = $db->super_query("SELECT tb1.fasts_num, public_id, tb2.admin, comments FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.public_id = tb2.id AND tb1.id = '{$rec_id}'");

                if ($row['comments'] || (stripos($row['admin'], "u{$user_id}|") !== false && !empty($wall_text))) {

                    Flood::LogInsert('comments');

                    //Если добавляется ответ на комментарий, то вносим в ленту новостей "ответы"
                    if ($answer_comm_id) {

                        //Выводим ид владельца комментария
                        $row_owner2 = $db->super_query("SELECT public_id, text FROM `communities_wall` WHERE id = '{$answer_comm_id}' AND fast_comm_id != '0'");

                        //Проверка на то, что юзер не отвечает сам себе
                        if ($user_id != $row_owner2['public_id'] and $row_owner2) {

                            $answer_text = $row_owner2['text'];

                            $check2 = $db->super_query("SELECT user_last_visit, user_name FROM `users` WHERE user_id = '{$row_owner2['public_id']}'");

                            $wall_text = str_replace($check2['user_name'], "<a href=\"/u{$row_owner2['public_id']}\" onClick=\"Page.Go(this.href); return false\" class=\"newcolor000\">{$check2['user_name']}</a>", $wall_text);

                            //Вставляем в ленту новостей
                            $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 6, action_text = '{$wall_text}', obj_id = '{$answer_comm_id}', for_user_id = '{$row_owner2['public_id']}', action_time = '{$server_time}', answer_text = '{$answer_text}', link = '/wallgroups{$row['public_id']}_{$rec_id}'");

                            //Вставляем событие в моментальные оповещения
                            $update_time = $server_time - 70;

                            if ($check2['user_last_visit'] >= $update_time) {

                                $db->query("INSERT INTO `updates` SET for_user_id = '{$row_owner2['public_id']}', from_user_id = '{$user_id}', type = '5', date = '{$server_time}', text = '{$wall_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/news/notifications'");

                                Cache::mozgCreateCache("user_{$row_owner2['public_id']}/updates", 1);

                                //ИНАЧЕ Добавляем +1 юзеру для оповещения
                            } else {

                                $cntCacheNews = Cache::mozgCache("user_{$row_owner2['public_id']}/new_news");
                                Cache::mozgCreateCache("user_{$row_owner2['public_id']}/new_news", ((int)$cntCacheNews + 1));
                            }
                        }
                    }

                    //Вставляем саму запись в БД
                    $db->query("INSERT INTO `communities_wall` SET public_id = '{$user_id}', text = '{$wall_text}', add_date = '{$server_time}', fast_comm_id = '{$rec_id}'");
                    $db->query("UPDATE `communities_wall` SET fasts_num = fasts_num+1 WHERE id = '{$rec_id}'");

                    ++$row['fasts_num'];

                    if ($row['fasts_num'] > 3) {
                        $comments_limit = $row['fasts_num'] - 3;
                    } else {
                        $comments_limit = 0;
                    }

                    $sql_comments = $db->super_query("SELECT tb1.id, public_id, text, add_date, tb2.user_photo, user_search_pref FROM `communities_wall` tb1, `users` tb2 WHERE tb1.public_id = tb2.user_id AND tb1.fast_comm_id = '{$rec_id}' ORDER by `add_date` ASC LIMIT {$comments_limit}, 3", true);

                    //Загружаем кнопку "Показать N записи"
                    $tpl->load_template('groups/record.tpl');
                    $tpl->set('{gram-record-all-comm}', declWord(($row['fasts_num'] - 3), 'prev') . ' ' . ($row['fasts_num'] - 3) . ' ' . declWord(($row['fasts_num'] - 3), 'comments'));
                    if ($row['fasts_num'] < 4) {
                        $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                    } else {
                        $tpl->set('{rec-id}', $rec_id);
                        $tpl->set('[all-comm]', '');
                        $tpl->set('[/all-comm]', '');
                    }
                    $tpl->set('{public-id}', $public_id);
                    $tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si", "");
                    $tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si", "");
                    $tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si", "");
                    $tpl->compile('content');

                    $tpl->load_template('groups/record.tpl');
                    //Собственно выводим комменты
                    foreach ($sql_comments as $row_comments) {
                        $tpl->set('{public-id}', $public_id);
                        $tpl->set('{name}', $row_comments['user_search_pref']);
                        if ($row_comments['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comments['public_id'] . '/50_' . $row_comments['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }
                        $tpl->set('{comm-id}', $row_comments['id']);
                        $tpl->set('{user-id}', $row_comments['public_id']);
                        $tpl->set('{rec-id}', $rec_id);

                        $expBR2 = explode('<br />', $row_comments['text']);
                        $textLength2 = count($expBR2);
                        $strTXT2 = strlen($row_comments['text']);
                        if ($textLength2 > 6 || $strTXT2 > 470) {
                            $row_comments['text'] = '<div class="wall_strlen" id="hide_wall_rec' . $row_comments['id'] . '" style="max-height:102px"">' . $row_comments['text'] . '</div><div class="wall_strlen_full" onMouseDown="wall.FullText(' . $row_comments['id'] . ', this.id)" id="hide_wall_rec_lnk' . $row_comments['id'] . '">Показать полностью..</div>';
                        }

                        //Обрабатываем ссылки
                        $row_comments['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row_comments['text']);

                        $tpl->set('{text}', stripslashes($row_comments['text']));
                        $date_str = megaDate($row_comments['add_date']);
                        $tpl->set('{date}', $date_str);
                        if (stripos($row['admin'], "u{$user_id}|") !== false || $user_id == $row_comments['public_id']) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                        } else {
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }

                        if ($user_id == $row_comments['public_id']) {
                            $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                        } else {

                            $tpl->set('[not-owner]', '');
                            $tpl->set('[/not-owner]', '');

                        }

                        $tpl->set('[comment]', '');
                        $tpl->set('[/comment]', '');
                        $tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si", "");
                        $tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si", "");
                        $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                        $tpl->compile('content');
                    }

                    //Загружаем форму ответа
                    $tpl->load_template('groups/record.tpl');
                    $tpl->set('{rec-id}', $rec_id);
                    $tpl->set('{user-id}', $public_id);
                    $tpl->set('[comment-form]', '');
                    $tpl->set('[/comment-form]', '');
                    $tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si", "");
                    $tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si", "");
                    $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                    $tpl->compile('content');
                    AjaxTpl($tpl);
                }
            }
            break;

        //################### Удаление записи ###################//
        case "wall_del":
            NoAjaxQuery();
            $rec_id = (new Request)->int('rec_id');
            $public_id = (new Request)->int('public_id');

            //Проверка на админа и проверяем включены ли комменты
            if ($public_id) {
                $row = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$public_id}'");
                $row_rec = $db->super_query("SELECT fast_comm_id, public_id, add_date FROM `communities_wall` WHERE id = '{$rec_id}'");
            } else {
                $row = $db->super_query("SELECT tb1.public_id, attach, fast_comm_id, tb2.admin FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.public_id = tb2.id AND tb1.id = '{$rec_id}'");
            }

            if (stripos($row['admin'], "u{$user_id}|") !== false || $user_id == $row_rec['public_id']) {
                if ($public_id) {
                    $db->query("UPDATE `communities_wall` SET fasts_num = fasts_num-1 WHERE id = '{$row_rec['fast_comm_id']}'");
                    $db->query("DELETE FROM `news` WHERE ac_user_id = '{$row_rec['public_id']}' AND action_type = '6' AND action_time = '{$row_rec['add_date']}'");
                    $db->query("DELETE FROM `communities_wall` WHERE id = '{$rec_id}'");
                } else if ($row['fast_comm_id'] == 0) {
                    $db->query("DELETE FROM `communities_wall` WHERE fast_comm_id = '{$rec_id}'");
                    $db->query("DELETE FROM `news` WHERE obj_id = '{$rec_id}' AND action_type = '11'");
                    $db->query("UPDATE `communities` SET rec_num = rec_num-1 WHERE id = '{$row['public_id']}'");

                    //Удаляем фотку из прикрепленной ссылке, если она есть
                    if (stripos($row['attach'], 'link|') !== false) {
                        $attach_arr = explode('link|', $row['attach']);
                        $attach_arr2 = explode('|/uploads/attach/' . $user_id . '/', $attach_arr[1]);
                        $attach_arr3 = explode('||', $attach_arr2[1]);
                        if ($attach_arr3[0]) {
                            Filesystem::delete(ROOT_DIR . '/uploads/attach/' . $user_id . '/' . $attach_arr3[0]);
                        }
                    }
                    $db->query("DELETE FROM `communities_wall` WHERE id = '{$rec_id}'");
                }
            }
            break;

        //################### Показ всех комментариев к записи ###################//
        case "all_comm":
            NoAjaxQuery();
            $rec_id = (new Request)->int('rec_id');
            $public_id = (new Request)->int('public_id');

            //Проверка на админа и проверяем включены ли комменты
            $row = $db->super_query("SELECT tb2.admin, comments FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.public_id = tb2.id AND tb1.id = '{$rec_id}'");

            if ($row['comments'] or stripos($row['admin'], "u{$user_id}|") !== false) {
                $sql_comments = $db->super_query("SELECT tb1.id, public_id, text, add_date, tb2.user_photo, user_search_pref FROM `communities_wall` tb1, `users` tb2 WHERE tb1.public_id = tb2.user_id AND tb1.fast_comm_id = '{$rec_id}' ORDER by `add_date` ASC", true);
                $tpl->load_template('groups/record.tpl');
                //Собственно выводим комменты
                foreach ($sql_comments as $row_comments) {
                    $tpl->set('{public-id}', $public_id);
                    $tpl->set('{name}', $row_comments['user_search_pref']);
                    if ($row_comments['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comments['public_id'] . '/50_' . $row_comments['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }

                    $tpl->set('{rec-id}', $rec_id);
                    $tpl->set('{comm-id}', $row_comments['id']);
                    $tpl->set('{user-id}', $row_comments['public_id']);

                    $expBR2 = explode('<br />', $row_comments['text']);
                    $textLength2 = count($expBR2);
                    $strTXT2 = strlen($row_comments['text']);
                    if ($textLength2 > 6 || $strTXT2 > 470) {
                        $row_comments['text'] = '<div class="wall_strlen" id="hide_wall_rec' . $row_comments['id'] . '" style="max-height:102px"">' . $row_comments['text'] . '</div><div class="wall_strlen_full" onMouseDown="wall.FullText(' . $row_comments['id'] . ', this.id)" id="hide_wall_rec_lnk' . $row_comments['id'] . '">Показать полностью..</div>';
                    }

                    //Обрабатываем ссылки
                    $row_comments['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row_comments['text']);

                    $tpl->set('{text}', stripslashes($row_comments['text']));
                    $date_str = megaDate($row_comments['add_date']);
                    $tpl->set('{date}', $date_str);
                    if (stripos($row['admin'], "u{$user_id}|") !== false || $user_id == $row_comments['public_id']) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                    } else {
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                    }

                    if ($user_id == $row_comments['public_id']) {
                        $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    } else {
                        $tpl->set('[not-owner]', '');
                        $tpl->set('[/not-owner]', '');
                    }

                    $tpl->set('[comment]', '');
                    $tpl->set('[/comment]', '');
                    $tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si", "");
                    $tpl->set_block("'\\[comment-form\\](.*?)\\[/comment-form\\]'si", "");
                    $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                    $tpl->compile('content');
                }

                //Загружаем форму ответа
                $tpl->load_template('groups/record.tpl');
                $tpl->set('{rec-id}', $rec_id);
                $tpl->set('{user-id}', $public_id);
                $tpl->set('[comment-form]', '');
                $tpl->set('[/comment-form]', '');
                $tpl->set_block("'\\[record\\](.*?)\\[/record\\]'si", "");
                $tpl->set_block("'\\[comment\\](.*?)\\[/comment\\]'si", "");
                $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                $tpl->compile('content');

                AjaxTpl($tpl);
            }

            break;

        //################### Страница загрузки фото в сообщество ###################//
        case "photos":
            NoAjaxQuery();
            $public_id = (new Request)->int('public_id');
            $rowPublic = $db->super_query("SELECT admin, photos_num FROM `communities` WHERE id = '{$public_id}'");
            if (stripos($rowPublic['admin'], "u{$user_id}|") !== false) {
                $page = (new Request)->int('page', 1);

                $gcount = 36;
                $limit_page = ($page - 1) * $gcount;

                //HEAD
                $tpl->load_template('public/photos/head.tpl');
                $tpl->set('{photo-num}', $rowPublic['photos_num'] . ' ' . declWord($rowPublic['photos_num'], 'photos'));
                $tpl->set('{public_id}', $public_id);
                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('info');

                //Выводим фотографии
                if ($rowPublic['photos_num']) {
                    $sql_ = $db->super_query("SELECT photo FROM `attach` WHERE public_id = '{$public_id}' ORDER by `add_date` DESC LIMIT {$limit_page}, {$gcount}", true);
                    $tpl->load_template('public/photos/photo.tpl');
                    foreach ($sql_ as $row) {
                        $tpl->set('{photo}', $row['photo']);
                        $tpl->set('{public-id}', $public_id);
                        $tpl->compile('content');
                    }
                    box_navigation($gcount, $rowPublic['photos_num'], $page, 'groups.wall_attach_addphoto', $public_id);
                } else {
                    msgbox('', '<div class="clear" style="margin-top:150px;margin-left:27px"></div>В альбоме сообщества нет загруженных фотографий.', 'info_2');
                }

                //BOTTOM
                $tpl->load_template('public/photos/head.tpl');
                $tpl->set('[bottom]', '');
                $tpl->set('[/bottom]', '');
                $tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si", "");
                $tpl->compile('content');

                AjaxTpl($tpl);
            }

            break;

        //################### Выводим инфу о видео при прикреплении видео на стену ###################//
        case "select_video_info":
            NoAjaxQuery();
            $video_id = (new Request)->int('video_id');
            $row = $db->super_query("SELECT photo FROM `videos` WHERE id = '" . $video_id . "'");
            if ($row) {
                $array1 = explode('/', $row['photo']);
                $photo = end($array1);
                echo $photo;
            } else {
                echo '1';
            }

            break;

        //################### Ставим мне нравится ###################//
        case "wall_like_yes":
            NoAjaxQuery();
            $rec_id = (new Request)->int('rec_id');
            $row = $db->super_query("SELECT likes_users FROM `communities_wall` WHERE id = '" . $rec_id . "'");
            if ($row && stripos($row['likes_users'], "u{$user_id}|") === false) {
                $likes_users = "u{$user_id}|" . $row['likes_users'];
                $db->query("UPDATE `communities_wall` SET likes_num = likes_num+1, likes_users = '{$likes_users}' WHERE id = '" . $rec_id . "'");
                $db->query("INSERT INTO `communities_wall_like` SET rec_id = '" . $rec_id . "', user_id = '" . $user_id . "', date = '" . $server_time . "'");
            }

            break;

        //################### Убераем мне нравится ###################//
        case "wall_like_remove":
            NoAjaxQuery();
            $rec_id = (new Request)->int('rec_id');
            $row = $db->super_query("SELECT likes_users FROM `communities_wall` WHERE id = '" . $rec_id . "'");
            if (stripos($row['likes_users'], "u{$user_id}|") !== false) {
                $likes_users = str_replace("u{$user_id}|", '', $row['likes_users']);
                $db->query("UPDATE `communities_wall` SET likes_num = likes_num-1, likes_users = '{$likes_users}' WHERE id = '" . $rec_id . "'");
                $db->query("DELETE FROM `communities_wall_like` WHERE rec_id = '" . $rec_id . "' AND user_id = '" . $user_id . "'");
            }

            break;

        //################### Выводим последних 7 юзеров кто поставил "Мне нравится" ###################//
        case "wall_like_users_five":
            NoAjaxQuery();
            $rec_id = (new Request)->int('rec_id');
            $sql_ = $db->super_query("SELECT tb1.user_id, tb2.user_photo FROM `communities_wall_like` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.rec_id = '{$rec_id}' ORDER by `date` DESC LIMIT 0, 7", true);
            if ($sql_) {
                foreach ($sql_ as $row) {
                    if ($row['user_photo']) {
                        $ava = '/uploads/users/' . $row['user_id'] . '/50_' . $row['user_photo'];
                    } else {
                        $ava = '/templates/' . $config['temp'] . '/images/no_ava_50.png';
                    }
                    echo '<a href="/u' . $row['user_id'] . '" id="Xlike_user' . $row['user_id'] . '_' . $rec_id . '" onClick="Page.Go(this.href); return false"><img src="' . $ava . '" width="32" /></a>';
                }
            }
            break;

        //################### Выводим всех юзеров которые поставили "мне нравится" ###################//
        case "all_liked_users":
            NoAjaxQuery();
            $rid = (new Request)->int('rid');
            $liked_num = (new Request)->int('liked_num');

            $page = (new Request)->int('page', 1);

            $gcount = 24;
            $limit_page = ($page - 1) * $gcount;

            if (!$liked_num) {
                $liked_num = 24;
            }

            if ($rid && $liked_num) {
                $sql_ = $db->super_query("SELECT tb1.user_id, tb2.user_photo, user_search_pref FROM `communities_wall_like` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.rec_id = '{$rid}' ORDER by `date` DESC LIMIT {$limit_page}, {$gcount}", true);

                if ($sql_) {
                    $tpl->load_template('profile_subscription_box_top.tpl');
                    $tpl->set('[top]', '');
                    $tpl->set('[/top]', '');
                    $tpl->set('{subcr-num}', 'Понравилось ' . $liked_num . ' ' . declWord($liked_num, 'like'));
                    $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                    $tpl->compile('content');

                    $tpl->result['content'] = str_replace('Всего', '', $tpl->result['content']);

                    $tpl->load_template('profile_friends.tpl');
                    foreach ($sql_ as $row) {
                        if ($row['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/50_' . $row['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }
                        $friend_info_online = explode(' ', $row['user_search_pref']);
                        $tpl->set('{user-id}', $row['user_id']);
                        $tpl->set('{name}', $friend_info_online[0]);
                        $tpl->set('{last-name}', $friend_info_online[1]);
                        $tpl->compile('content');
                    }
                    box_navigation($gcount, $liked_num, $rid, 'groups.wall_all_liked_users', $liked_num);

                    AjaxTpl($tpl);
                }
            }

            break;

        //################### Рассказать друзьям "Мне нравится" ###################//
        case "wall_tell":
            NoAjaxQuery();
            $rid = (new Request)->int('rec_id');

            //Проверка на существование записи
            $row = $db->super_query("SELECT add_date, text, public_id, attach, tell_uid, tell_date, public FROM `communities_wall` WHERE fast_comm_id = 0 AND id = '{$rid}'");

            if ($row) {
                if ($row['tell_uid']) {
                    $row['add_date'] = $row['tell_date'];
                    $row['author_user_id'] = $row['tell_uid'];
                    $row['public_id'] = $row['tell_uid'];
                } else {
                    $row['public'] = 1;
                }

                //Проверяем на существование этой записи у себя на стене
                $myRow = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE tell_uid = '{$row['public_id']}' AND tell_date = '{$row['add_date']}' AND author_user_id = '{$user_id}' AND public = '{$row['public']}'");
                if ($row['tell_uid'] !== $user_id && $myRow['cnt'] == false) {

                    //Вставляем себе на стену
                    $db->query("INSERT INTO `wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$row['text']}', add_date = '{$server_time}', fast_comm_id = 0, tell_uid = '{$row['public_id']}', tell_date = '{$row['add_date']}', public = '{$row['public']}', attach = '" . $row['attach'] . "'");
                    $dbid = $db->insert_id();
                    $db->query("UPDATE `users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$user_id}'");

                    //Вставляем в ленту новостей
                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$row['text']}', obj_id = '{$dbid}', action_time = '{$server_time}'");

                    //Чистим кеш
                    Cache::mozgClearCacheFile("user_{$user_id}/profile_{$user_id}");
                } else {
                    echo 1;
                }
            } else {
                echo 1;
            }

            break;

        //################### Показ всех подписок ###################//
        case "all_people":
            NoAjaxQuery();

            $page = (new Request)->int('page', 1);

            $gcount = 24;
            $limit_page = ($page - 1) * $gcount;

            $public_id = (new Request)->int('public_id');
            $subscr_num = (new Request)->int('num');

            $sql_ = $db->super_query("SELECT tb1.user_id, tb2.user_name, user_lastname, user_photo FROM `friends` tb1, `users` tb2 WHERE tb1.friend_id = '{$public_id}' AND tb1.user_id = tb2.user_id AND tb1.subscriptions = 2 ORDER by `friends_date` DESC LIMIT {$limit_page}, {$gcount}", true);

            if ($sql_) {
                $tpl->load_template('profile_subscription_box_top.tpl');
                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set('{subcr-num}', $subscr_num . ' ' . declWord($subscr_num, 'subscribers'));
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('content');

                $tpl->load_template('profile_friends.tpl');
                foreach ($sql_ as $row) {
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $row['user_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $tpl->set('{user-id}', $row['user_id']);
                    $tpl->set('{name}', $row['user_name']);
                    $tpl->set('{last-name}', $row['user_lastname']);
                    $tpl->compile('content');
                }

                box_navigation($gcount, $subscr_num, $public_id, 'groups.all_people', $subscr_num);

            }

            AjaxTpl($tpl);

            break;

        //################### Показ всех сообщества юзера на которые он подписан (BOX) ###################//
        case "all_groups_user":
            $page = (new Request)->int('page', 1);

            $gcount = 20;
            $limit_page = ($page - 1) * $gcount;

            $for_user_id = (new Request)->int('for_user_id');
            $subscr_num = (new Request)->int('num');

            $sql_ = $db->super_query("SELECT tb1.friend_id, tb2.id, title, photo, traf, adres FROM `friends` tb1, `communities` tb2 WHERE tb1.user_id = '{$for_user_id}' AND tb1.friend_id = tb2.id AND tb1.subscriptions = 2 ORDER by `traf` DESC LIMIT {$limit_page}, {$gcount}", true);

            if ($sql_) {
                $tpl->load_template('profile_subscription_box_top.tpl');
                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set('{subcr-num}', $subscr_num . ' ' . declWord($subscr_num, 'subscr'));
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('content');

                $tpl->load_template('profile_group.tpl');
                foreach ($sql_ as $row) {
                    if ($row['photo']) {
                        $tpl->set('{ava}', '/uploads/groups/' . $row['id'] . '/50_' . $row['photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $tpl->set('{name}', stripslashes($row['title']));
                    $tpl->set('{public-id}', $row['id']);
                    $tpl->set('{num}', '<span id="traf">' . $row['traf'] . ' ' . declWord($row['traf'], 'subscribers'));
                    if ($row['adres']) $tpl->set('{adres}', $row['adres']);
                    else $tpl->set('{adres}', 'public' . $row['id']);
                    $tpl->compile('content');
                }
                box_navigation($gcount, $subscr_num, $for_user_id, 'groups.all_groups_user', $subscr_num);
            }
            AjaxTpl($tpl);

            break;

        //################### Одна запись со стены ###################//
        case "wallgroups":

            $id = (new Request)->int('id');
            $pid = (new Request)->int('pid');

            $row = $db->super_query("SELECT id, adres, del, ban FROM `communities` WHERE id = '{$pid}'");

            if ($row and !$row['del'] and !$row['ban']) {

                $tpl->load_template('groups/wall_head.tpl');
                $tpl->set('{id}', $id);
                $tpl->set('{pid}', $pid);
                if ($row['adres']) {
                    $tpl->set('{adres}', $row['adres']);
                } else {
                    $tpl->set('{adres}', 'public' . $pid);
                }
                $tpl->compile('info');

                include ENGINE_DIR . '/classes/wall.public.php';
                $wall = new wall();
                $wall->query("SELECT tb1.id, text, public_id, add_date, fasts_num, attach, likes_num, likes_users, tell_uid, public, tell_date, tell_comm, tb2.title, photo, comments, adres FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.id = '{$id}' AND tb1.public_id = tb2.id AND fast_comm_id = 0");
                $wall->template('groups/record.tpl');
                $wall->compile('content');
                $wall->select($public_admin, $server_time);

                $tpl->result['content'] = str_replace('width:500px;', 'width:710px;', $tpl->result['content']);

                if (!$tpl->result['content']) {
                    msgbox('', '<br /><br /><br />Запись не найдена.<br /><br /><br />', 'info_2');
                }

            } else {
                msgbox('', '<br /><br />Запись не найдена.<br /><br /><br />', 'info_2');
            }

            compile($tpl);
            break;

        //################### Закрипление записи ###################//
        case "fasten":

            NoAjaxQuery();

            $rec_id = (new Request)->int('rec_id');

            //Выводим ИД группы
            $row = $db->super_query("SELECT public_id FROM `communities_wall` WHERE id = '{$rec_id}'");

            //Проверка на админа
            $row_pub = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$row['public_id']}'");

            if (stripos($row_pub['admin'], "u{$user_id}|") !== false) {
                //Убераем фиксацию у пред записи
                $db->query("UPDATE `communities_wall` SET fixed = '0' WHERE fixed = '1' AND public_id = '{$row['public_id']}'");
                //Ставим фиксацию записи
                $db->query("UPDATE `communities_wall` SET fixed = '1' WHERE id = '{$rec_id}'");
            }
            break;

        //################### Убираем фиксацию ###################//
        case "unfasten":

            NoAjaxQuery();

            $rec_id = (new Request)->int('rec_id');

            //Выводим ИД группы
            $row = $db->super_query("SELECT public_id FROM `communities_wall` WHERE id = '{$rec_id}'");

            //Проверка на админа
            $row_pub = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$row['public_id']}'");

            if (stripos($row_pub['admin'], "u{$user_id}|") !== false) {
                //Убираем фиксацию записи
                $db->query("UPDATE `communities_wall` SET fixed = '0' WHERE id = '{$rec_id}'");
            }

            break;

        //################### Окно приглашения в группу ###################//
        case "invitebox":

            NoAjaxQuery();

            $pub_id = (new Request)->int('id');

            $limit_friends = 20;
            $page_cnt = (new Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt *= $limit_friends;
            }

            //Выводим список участников группы
            $rowPub = $db->super_query("SELECT ulist FROM `communities` WHERE id = '{$pub_id}'");

            //Выводим список друзей
            $sql_ = $db->super_query("SELECT tb1.friend_id, tb2.user_photo, user_search_pref, user_sex FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 ORDER by `friends_date` DESC LIMIT {$page_cnt}, {$limit_friends}", true);

            if ($sql_) {

                $tpl->load_template('groups/inviteuser.tpl');
                foreach ($sql_ as $row) {

                    if ($row['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', "/images/100_no_ava.png");
                    }

                    $tpl->set('{user-id}', $row['friend_id']);
                    $tpl->set('{name}', $row['user_search_pref']);


                    //Проверка, юзер есть в сообществе или нет
                    if (stripos($rowPub['ulist'], '|' . $row['friend_id'] . '|') !== false) {

                        $tpl->set('{yes-group}', 'grInviteYesed');
                        $tpl->set('{yes-text}', '<div class="fl_r online grInviteOk">в сообществе</div>');
                        $tpl->set('{function}', '');

                    } else {

                        //Проверка, юзеру отправлялось приглашение или нет
                        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_join` WHERE for_user_id = '{$row['friend_id']}' AND public_id = '{$pub_id}'");

                        if ($check['cnt']) {

                            $tpl->set('{yes-group}', 'grInviteYesed');

                            if ($row['user_sex'] == 2) {
                                $tpl->set('{yes-text}', '<div class="fl_r online grInviteOk">приглашена</div>');
                            } else {
                                $tpl->set('{yes-text}', '<div class="fl_r online grInviteOk">приглашен</div>');
                            }
                            $tpl->set('{function}', '');
                        } else {
                            $tpl->set('{yes-group}', 'grIntiveUser');
                            $tpl->set('{yes-text}', '');
                            $tpl->set('{function}', 'groups.inviteSet');
                        }
                    }
                    $tpl->compile('friends');
                }
                $numFr = count($sql_);
            } else {
                $numFr = null;
            }

            if (!$page_cnt) {

                $tpl->load_template('groups/invitebox.tpl');
                $tpl->set('{friends}', $tpl->result['friends'] ?? '');
                $tpl->set('{id}', $pub_id);

                if ($numFr == $limit_friends) {
                    $tpl->set('[but]', '');
                    $tpl->set('[/but]', '');
                } else {
                    $tpl->set_block("'\\[but\\](.*?)\\[/but\\]'si", "");
                }
                $tpl->compile('content');
            } else {
                $tpl->result['content'] = $tpl->result['friends'] ?? '';
            }

            AjaxTpl($tpl);

            break;

        //################### Отправка приглашения ###################//
        case "invitesend":

            NoAjaxQuery();

            $pub_id = (new Request)->int('id');
            $limit = 50; #лимит в день

            //Выводим список участников группы
            $rowPub = $db->super_query("SELECT id, ulist FROM `communities` WHERE id = '{$pub_id}'");

            //Дата заявки
            $newData = date('Y-m-d', $server_time);

            //Считаем сколько заявок было отправлено за последние сутки
            $rowCnt = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_join` WHERE user_id = '{$user_id}' AND public_id = '{$pub_id}' AND date = '{$newData}'");

            //Создаем точку отчета для цикла foreach, чтоб если было уже 49 отправок, и юзер еще выбрал 49 то скрипт в масиве заметил это и прекратил действия
            $i = $rowCnt['cnt'];

            //Если заявок меньше указанного лимита, то пропускаем
            if ($rowCnt['cnt'] < $limit) {

                //Если такая группа есть
                if ($rowPub['id']) {

                    //Получаем список, которых надо пригласить и формируем его
                    $arr_list = explode('|', (new Request)->filter('ulist'));

                    foreach ($arr_list as $ruser_id) {
                        $ruser_id = (int)$ruser_id;
                        if ($ruser_id && $user_id !== $ruser_id && $i < $limit) {

                            //Проверка, такой юзер в базе есть или нет
                            $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$ruser_id}'");

                            //Проверка, юзер есть в сообществе или нет
                            if ($row['cnt'] && stripos($rowPub['ulist'], '|' . $ruser_id . '|') === false) {

                                //Проверка, юзеру отправлялось приглашение или нет
                                $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_join` WHERE for_user_id = '{$ruser_id}' AND public_id = '{$pub_id}'");

                                //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                                $check_friend = CheckFriends($ruser_id);

                                //Если нет приглашения, то отправляем приглашение
                                if (!$check['cnt'] && $check_friend) {
                                    ++$i;
                                    //Вставляем в таблицу приглашений заявку
                                    $db->query("INSERT INTO `communities_join` SET user_id = '{$user_id}', for_user_id = '{$ruser_id}', public_id = '{$pub_id}', date = '{$newData}'");
                                    //Добавляем юзеру +1 в приглашениях
                                    $db->query("UPDATE `users` SET invties_pub_num = invties_pub_num + 1 WHERE user_id = '{$ruser_id}'");
                                }

                            }

                        }

                    }

                }

            } else {
                echo 1;
            }

            break;

        //################### Вывод всех приглашений сообществ ###################//
        case "invites":

            $limit_num = 20;

            $page_cnt = (new Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt *= $limit_num;
            } else {
                $page_cnt = 0;
            }

            //Загружаем верхушку
            if (!$page_cnt) {

                $tpl->load_template('groups/invites_head.tpl');

                if ($user_info['invties_pub_num']) {
                    $tpl->set('{num}', $user_info['invties_pub_num'] . ' ' . declOfNum($user_info['invties_pub_num'], array('приглашение', 'приглашения', 'приглашений')));
                    $tpl->set('[yes]', '');
                    $tpl->set('[/yes]', '');
                    $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
                } else {
                    $tpl->set('[no]', '');
                    $tpl->set('[/no]', '');
                    $tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si", "");
                }
                $tpl->compile('info');
            }

            //Выводим сообщества
            if ($user_info['invties_pub_num']) {

                //SQL Запрос на вывод
                $sql_ = $db->super_query("SELECT tb1.user_id, tb2.id, title, photo, traf, adres, tb3.user_search_pref, user_photo FROM `communities_join` tb1, `communities` tb2, `users` tb3 WHERE tb1.for_user_id = '{$user_id}' AND tb1.public_id = tb2.id AND tb1.user_id = tb3.user_id ORDER by `id` DESC LIMIT {$page_cnt}, {$limit_num}", true);

                if ($sql_) {

                    $tpl->load_template('groups/invite.tpl');

                    foreach ($sql_ as $row) {

                        if ($row['photo']) {
                            $tpl->set('{photo}', "/uploads/groups/{$row['id']}/100_{$row['photo']}");
                        } else {
                            $tpl->set('{photo}', "/images/no_ava_groups_100.gif");
                        }

                        $tpl->set('{name}', stripslashes($row['title']));
                        $tpl->set('{traf}', $row['traf'] . ' ' . declOfNum($row['traf'], array('участник', 'участника', 'участников')));
                        $tpl->set('{id}', $row['id']);

                        if ($row['adres']) {
                            $tpl->set('{adres}', $row['adres']);
                        } else {
                            $tpl->set('{adres}', 'public' . $row['id']);
                        }

                        $tpl->set('{inviter-name}', $row['user_search_pref']);
                        $tpl->set('{inviter-id}', $row['user_id']);

                        if ($row['user_photo']) {
                            $tpl->set('{inviter-ava}', '/uploads/users/' . $row['user_id'] . '/50_' . $row['user_photo']);
                        } else {
                            $tpl->set('{inviter-ava}', '/images/100_no_ava.png');
                        }

                        $tpl->compile('content');

                    }

                }

            }

            //Загружаем низ
            if (!$page_cnt && $user_info['invties_pub_num'] > $limit_num) {
                $tpl->load_template('groups/invite_bottom.tpl');
                $tpl->compile('content');
            }

            //Если подгружаем
            if ($page_cnt) {
                AjaxTpl($tpl);
                exit();
            }
            compile($tpl);
            break;

        //################### Отклонение приглашения ###################//
        case "invite_no":

            NoAjaxQuery();

            $id = (new Request)->int('id');

            //Проверка на приглашению юзеру
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_join` WHERE for_user_id = '{$user_id}' AND public_id = '{$id}'");

            //Если есть приглашение, то удаляем его
            if ($check['cnt']) {
                $db->query("DELETE FROM `communities_join` WHERE for_user_id = '{$user_id}' AND public_id = '{$id}'");
                //Обновляем кол-во приглашений
                $db->query("UPDATE `users` SET invties_pub_num = invties_pub_num - 1 WHERE user_id = '{$user_id}'");
            }

            break;

        default:

            //################### Вывод всех сообществ ###################//
            $owner = $db->super_query("SELECT user_public_num FROM `users` WHERE user_id = '{$user_id}'");
            if ($act == 'admin') {
                $mobile_speedbar = 'Ваши сообщества';
                $tpl->load_template('groups/head_admin.tpl');
                $sql_sort = "SELECT id, title, photo, traf, adres FROM `communities` WHERE admin regexp '[[:<:]](u{$user_id})[[:>:]]' ORDER by `traf` DESC LIMIT {$limit_page}, {$gcount}";
                $sql_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities` WHERE admin regexp '[[:<:]](u{$user_id})[[:>:]]'");
                $owner['user_public_num'] = $sql_count['cnt'];
            } else {
                $mobile_speedbar = 'Сообщества';
                $sql_sort = "SELECT tb1.friend_id, tb2.id, title, photo, traf, adres FROM `friends` tb1, `communities` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.id AND tb1.subscriptions = 2 ORDER by `traf` DESC LIMIT {$limit_page}, {$gcount}";
                $tpl->load_template('groups/head.tpl');
            }

            if ($owner['user_public_num']) {
                $tpl->set('{num}', $owner['user_public_num'] . ' ' . declWord($owner['user_public_num'], 'groups'));
                $tpl->set('[yes]', '');
                $tpl->set('[/yes]', '');
                $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
            } else {
                $tpl->set('[no]', '');
                $tpl->set('[/no]', '');
                $tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si", "");
            }
            $tpl->compile('info');

            if ($owner['user_public_num']) {

                $sql_ = $db->super_query($sql_sort, true);

                $tpl->load_template('groups/group.tpl');
                foreach ($sql_ as $row) {
                    $tpl->set('{id}', $row['id']);
                    if ($row['adres']) {
                        $tpl->set('{adres}', $row['adres']);
                    } else {
                        $tpl->set('{adres}', 'public' . $row['id']);
                    }

                    $tpl->set('{name}', stripslashes($row['title']));
                    $tpl->set('{traf}', $row['traf'] . ' ' . declWord($row['traf'], 'groups_users'));

                    if ($act !== 'admin') {
                        $tpl->set('[admin]', '');
                        $tpl->set('[/admin]', '');
                    } else {
                        $tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si", "");
                    }

                    if ($row['photo']) {
                        $tpl->set('{photo}', "/uploads/groups/{$row['id']}/100_{$row['photo']}");
                    } else {
                        $tpl->set('{photo}', "/images/no_ava_groups_100.gif");
                    }

                    $tpl->compile('content');
                }

                if ($act == 'admin') {
                    $admn_act = 'act=admin&';
                } else {
                    $admn_act = '';
                }

                navigation($gcount, $owner['user_public_num'], 'groups?' . $admn_act . 'page=');

            }
            compile($tpl);
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}