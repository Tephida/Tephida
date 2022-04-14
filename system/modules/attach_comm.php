<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;
use FluffyDollop\Support\Registry;

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');
    switch ($act) {

        //################### Удаление комментария ###################//
        case "delcomm":

            $id = (new Request)->int('id');
            $purl = to_translit((new Request)->filter('purl'));

            //Выводим данные о комментарии
            $row = $db->super_query("SELECT tb1.forphoto, auser_id, tb2.ouser_id FROM `attach_comm` tb1, `attach` tb2 WHERE tb1.id = '{$id}' AND tb1.forphoto = '{$purl}'");
            $tab_photos = false;

            //Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
            if (!$row) {

                //Проверка в таблице PREFIX_photos
                $row_photos = $db->super_query("SELECT tb1.pid, owner_id, tb2.user_id FROM `photos_comments` tb1, `photos` tb2 WHERE tb1.id = '{$id}' AND tb1.photo_name = '{$purl}'");
                $tab_photos = true;

                $row['auser_id'] = $row_photos['owner_id'];
                $row['ouser_id'] = $row_photos['user_id'];
                $row['pid'] = $row_photos['pid'];

            }

            if ($row['auser_id'] == $user_id or $row['ouser_id'] == $user_id) {

                //Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
                if ($tab_photos) {

                    $db->query("DELETE FROM `photos_comments` WHERE id = '{$id}'");
                    $db->query("UPDATE `photos` SET comm_num = comm_num-1 WHERE id = '{$row['pid']}'");

                    $row2 = $db->super_query("SELECT album_id FROM `photos` WHERE id = '{$row['pid']}'");

                    $db->query("UPDATE `albums` SET comm_num = comm_num-1 WHERE aid = '{$row2['album_id']}'");

                } else {

                    //Обновляем кол-во комментов
                    $db->query("UPDATE `attach` SET acomm_num = acomm_num-1 WHERE photo = '{$row['forphoto']}'");

                    //Удаляем комментарий
                    $db->query("DELETE FROM `attach_comm` WHERE forphoto = '{$row['forphoto']}' AND id = '{$id}'");

                }

            }

            compile($tpl);
            break;

        //################### Добавления комментария ###################//
        case "addcomm":

            $text = (new Request)->filter('text');
            $purl = to_translit((new Request)->filter('purl'));

            //Проверка на существования фотки в таблице PREFIX_attach
            $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `attach` WHERE photo = '{$purl}'");
            $tab_photos = false;

            //Если нет фотки в таблице PREFIX_attach то проверяем в таблице PREFIX_photos
            if (!$row['cnt']) {

                $row = $db->super_query("SELECT album_id, user_id, photo_name, id FROM `photos` WHERE photo_name = '{$purl}'");
                $tab_photos = true;

                if ($row['album_id'])
                    $row['cnt'] = 1;

            }

            //Если фотка есть
            if (!empty($text) and $row['cnt']) {

                if ($tab_photos) {

                    $hash = md5($user_id . $server_time . $_IP . $user_info['user_email'] . rand(0, 1000000000)) . $text . $purl;

                    $db->query("INSERT INTO `photos_comments` (pid, user_id, text, date, hash, album_id, owner_id, photo_name) VALUES ('{$row['id']}', '{$user_id}', '{$text}', NOW(), '{$hash}', '{$row['album_id']}', '{$row['user_id']}', '{$row['photo_name']}')");
                    $id = $db->insert_id();

                    $db->query("UPDATE `photos` SET comm_num = comm_num+1 WHERE id = '{$row['id']}'");

                    $db->query("UPDATE `albums` SET comm_num = comm_num+1 WHERE aid = '{$row['album_id']}'");

                } else {

                    //Вставляем сам комментарий
                    $db->query("INSERT INTO `attach_comm` SET forphoto = '{$purl}', auser_id = '{$user_id}', text = '{$text}', adate = '{$server_time}'");
                    $id = $db->insert_id();

                    //Обновляем кол-во комментов
                    $db->query("UPDATE `attach` SET acomm_num = acomm_num+1 WHERE photo = '{$purl}'");

                }

                $tpl->load_template('attach/comment.tpl');
                $tpl->set('{id}', $id);
                $tpl->set('{uid}', $user_id);
                $tpl->set('{comment}', stripslashes($text));
                $tpl->set('{purl}', $purl);
                $tpl->set('{author}', $user_info['user_search_pref']);
                $tpl->set('{online}', $lang['online']);
                $tpl->set('{date}', langdate('сегодня в H:i', $server_time));
                if ($user_info['user_photo']) {
                    $tpl->set('{ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }
                $tpl->set('[owner]', '');
                $tpl->set('[/owner]', '');
                $tpl->compile('content');

                AjaxTpl($tpl);

            }

            compile($tpl);
            break;

        //################### Показ пред.комментариев ###################//
        case "prevcomm":

            $foSQLurl = to_translit((new Request)->filter('purl'));

            //Выводим данные о владельце фото
            $row = $db->super_query("SELECT ouser_id, acomm_num FROM `attach` WHERE photo = '{$foSQLurl}'");


            //Если нет, то проверяем в таблице PREFIX_photos
            if (!$row) {

                $row = $db->super_query("SELECT user_id, comm_num FROM `photos` WHERE photo_name = '{$foSQLurl}'");
                $row['acomm_num'] = $row['comm_num'];
                $row['ouser_id'] = $row['user_id'];
                $tab_photos = true;
            } else {
                $tab_photos = false;
            }

            $limit = 10;
            $first_id = (new Request)->int('first_id');
            $page_post = (new Request)->int('page');
            if ($page_post <= 0) {
                $page_post = 1;
            }

            $start_limit = $row['acomm_num'] - ($page_post * $limit) - 3;
            if ($start_limit < 0) {
                $start_limit = 0;
            }

            if ($tab_photos) {
                $sql_comm = $db->super_query("SELECT tb1.user_id, text, date, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}' AND id < '{$first_id}' ORDER by `date` ASC LIMIT {$start_limit}, {$limit}", true);
            } else {
                $sql_comm = $db->super_query("SELECT tb1.auser_id, text, adate, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `attach_comm` tb1, `users` tb2 WHERE tb1.auser_id = tb2.user_id AND tb1.forphoto = '{$foSQLurl}' AND id < '{$first_id}' ORDER by `adate` ASC LIMIT {$start_limit}, {$limit}", true);
            }

            $tpl->load_template('attach/comment.tpl');

            foreach ($sql_comm as $row_comm) {

                if ($tab_photos) {

                    $row_comm['adate'] = strtotime($row_comm['date']);
                    $row_comm['auser_id'] = $row_comm['user_id'];

                }

                $tpl->set('{comment}', stripslashes($row_comm['text']));
                $tpl->set('{uid}', $row_comm['auser_id']);
                $tpl->set('{id}', $row_comm['id']);
                $tpl->set('{purl}', $foSQLurl);
                $tpl->set('{author}', $row_comm['user_search_pref']);

                if ($row_comm['user_photo']) {
                    $tpl->set('{ava}', '/uploads/users/' . $row_comm['auser_id'] . '/50_' . $row_comm['user_photo']);
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }

                OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                $date_str = megaDate($row_comm['adate']);
                $tpl->set('{date}', $date_str);
                if ($row_comm['auser_id'] == $user_id or $row['ouser_id'] == $user_id) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                } else {
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }

                $tpl->compile('content');

            }

            AjaxTpl($tpl);

            break;

        default:

            $photo_url = (new Request)->filter('photo');
            $resIMGurl = explode('/', $photo_url);
            $foSQLurl = end($resIMGurl);
            $foSQLurl = to_translit($foSQLurl);

            //Выводим данные о владельце фото
            $row = $db->super_query("SELECT tb1.ouser_id, acomm_num, add_date, tb2.user_search_pref, user_country_city_name FROM `attach` tb1, `users` tb2 WHERE tb1.ouser_id = tb2.user_id AND tb1.photo = '{$foSQLurl}'");

            //Если нет, то проверяем в таблице PREFIX_photos
            if (!$row) {

                $row = $db->super_query("SELECT tb1.user_id, comm_num, date, tb2.user_search_pref, user_country_city_name FROM `photos` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}'");
                $row['acomm_num'] = $row['comm_num'];
                $row['ouser_id'] = $row['user_id'];
                $row['add_date'] = strtotime($row['date']);
                $tab_photos = true;

            } else {
                $tab_photos = false;
            }

            if ($row) {

                //Выводим комментарии если они есть
                if ($row['acomm_num']) {

                    if ($row['acomm_num'] > 7) {
                        $limit_comm = $row['acomm_num'] - 3;
                    } else {
                        $limit_comm = 0;
                    }

                    if ($tab_photos) {
                        $sql_comm = $db->super_query("SELECT tb1.user_id, text, date, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.photo_name = '{$foSQLurl}' ORDER by `date` ASC LIMIT {$limit_comm}, {$row['acomm_num']}", true);
                    } else {
                        $sql_comm = $db->super_query("SELECT tb1.auser_id, text, adate, id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `attach_comm` tb1, `users` tb2 WHERE tb1.auser_id = tb2.user_id AND tb1.forphoto = '{$foSQLurl}' ORDER by `adate` ASC LIMIT {$limit_comm}, {$row['acomm_num']}", true);
                    }

                    $tpl->load_template('attach/comment.tpl');

                    foreach ($sql_comm as $row_comm) {

                        if ($tab_photos) {

                            $row_comm['adate'] = strtotime($row_comm['date']);
                            $row_comm['auser_id'] = $row_comm['user_id'];

                        }

                        $tpl->set('{comment}', stripslashes($row_comm['text']));
                        $tpl->set('{uid}', $row_comm['auser_id']);
                        $tpl->set('{id}', $row_comm['id']);
                        $tpl->set('{purl}', $foSQLurl);
                        $tpl->set('{author}', $row_comm['user_search_pref']);

                        if ($row_comm['user_photo']) {
                            $tpl->set('{ava}', '/uploads/users/' . $row_comm['auser_id'] . '/50_' . $row_comm['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }

                        OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                        $date_str = megaDate($row_comm['adate']);
                        $tpl->set('{date}', $date_str);
                        if ($row_comm['auser_id'] == $user_id || $row['ouser_id'] == $user_id) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                        } else {
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }

                        $tpl->compile('comments');
                    }

                }

                $tpl->load_template('attach/addcomm.tpl');

                //Кнопка показ пред сообщений
                if ($row['acomm_num'] > 7) {
                    $tpl->set('[comm]', '');
                    $tpl->set('[/comm]', '');
                } else {
                    $tpl->set_block("'\\[comm\\](.*?)\\[/comm\\]'si", "");
                }

                $tpl->set('{author}', $row['user_search_pref']);
                $tpl->set('{uid}', $row['ouser_id']);
                $tpl->set('{purl}', $foSQLurl);
                $tpl->set('{purl-js}', substr($foSQLurl, 0, 20));

                if ($row['add_date']) {
                    $date_str = megaDate($row['add_date']);
                    $tpl->set('{date}', $date_str);
                } else {
                    $tpl->set('{date}', '');
                }

                $author_info = explode('|', $row['user_country_city_name']);
                if ($author_info[0]) {
                    $tpl->set('{author-info}', $author_info[0]);
                } else {
                    $tpl->set('{author-info}', '');
                }
                if ($author_info[1]) {
                    $tpl->set('{author-info}', $author_info[0] . ', ' . $author_info[1] . '<br />');
                }

                $tpl->set('{comments}', $tpl->result['comments'] ?? '');
                $tpl->compile('content');

                AjaxTpl($tpl);

            }
    }
}