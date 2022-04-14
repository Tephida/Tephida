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
use Mozg\classes\Cache;

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');

    switch ($act) {

        //################### Добавление видеозаписи в сообщество ###################//
        case "add":

            NoAjaxQuery();

            $pid = (new Request)->int('pid');
            $id = (new Request)->int('id');

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            $row = $db->super_query("SELECT video, photo, title, descr FROM `videos` WHERE id = '{$id}'");

            if ($public_admin && $row) {

                //Директория загрузки фото
                $upload_dir = ROOT_DIR . '/uploads/videos/' . $user_id;

                //Если нет папки юзера, то создаём её
                Filesystem::createDir($upload_dir);

                $img_name_arr = end(explode(".", $row['photo']));
                $expPhoto = substr(md5(time() . md5($row['photo'])), 0, 15) . '.' . $img_name_arr;
                Filesystem::copy($row['photo'], ROOT_DIR . "/uploads/videos/{$user_id}/{$expPhoto}");
                $config = settings_get();
                $newPhoto = "{$config['home_url']}uploads/videos/{$user_id}/{$expPhoto}";

                $db->query("INSERT INTO `videos` SET public_id = '{$pid}', owner_user_id = '{$user_id}', video = '{$row['video']}', photo = '{$newPhoto}', title = '{$row['title']}', descr = '{$row['descr']}', add_date = NOW(), privacy = '1'");

                $db->query("UPDATE `communities` SET videos_num = videos_num + 1 WHERE id = '{$pid}'");

                Cache::mozgClearCacheFile("groups/video{$pid}");

            }

            break;

        //################### Удаление видео ###################//
        case "del":

            NoAjaxQuery();

            $pid = (new Request)->int('pid');
            $id = (new Request)->int('id');

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            $row = $db->super_query("SELECT photo, public_id, owner_user_id FROM `videos` WHERE id = '{$id}'");

            if ($public_admin && $row['public_id'] == $pid) {

                //Директория загрузки фото
                $upload_dir = ROOT_DIR . '/uploads/videos/' . $row['owner_user_id'];
                $expPho = end(explode('/', $row['photo']));
                Filesystem::delete($upload_dir . '/' . $expPho);
                $db->query("DELETE FROM `videos` WHERE id = '{$id}'");
                $db->query("UPDATE `communities` SET videos_num = videos_num - 1 WHERE id = '{$pid}'");
                Cache::mozgClearCacheFile("groups/video{$pid}");
            }
            break;

        //################### Окно редактирования видео ###################//
        case "edit":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            $id = (new Request)->int('id');
            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");
            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            $row = $db->super_query("SELECT public_id, title, descr FROM `videos` WHERE id = '{$id}'");

            if ($public_admin && $row['public_id'] == $pid) {
                $tpl->load_template('public_videos/edit.tpl');
                $tpl->set('{title}', stripslashes($row['title']));
                $tpl->set('{descr}', myBrRn(stripslashes($row['descr'])));
                $tpl->compile('content');
                AjaxTpl($tpl);
            }

            break;

        //################### Сохранение отред. данных ###################//
        case "edit_save":

            NoAjaxQuery();

            $pid = (new Request)->int('pid');
            $id = (new Request)->int('id');

            $title = (new Request)->filter('title', 25000, true);
            $descr = (new Request)->filter('descr', 3000);

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            $row = $db->super_query("SELECT public_id FROM `videos` WHERE id = '{$id}'");
            if ($public_admin && $row['public_id'] == $pid && !empty($title)) {
                $db->query("UPDATE `videos` SET title = '{$title}', descr = '{$descr}' WHERE id = '{$id}'");
                echo stripslashes($descr);
                Cache::mozgClearCacheFile("groups/video{$pid}");
            }
            break;

        //################### Поиск по видеозаписям ###################//
        case "search":

            NoAjaxQuery();

            $sql_limit = 20;

            $page_cnt = (new Request)->int('page');
            if ($page_cnt > 0) {
                $page_cnt *= $sql_limit;
            }

            $pid = (new Request)->int('pid');
            $query = strip_data((new Request)->filter('query'));
            $query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
            $adres = strip_tags((new Request)->filter('adres'));
            $row_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos` WHERE title LIKE '%{$query}%' AND public_id = '0'");
            $sql_ = $db->super_query("SELECT id, owner_user_id, title, descr, photo, comm_num, add_date FROM `videos` WHERE title LIKE '%{$query}%' AND public_id = '0' ORDER by `add_date` DESC LIMIT {$page_cnt}, {$sql_limit}", true);
            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");
            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            $tpl->load_template('public_videos/search_result.tpl');

            if ($sql_) {

                if (!$page_cnt) {
                    $tpl->result['content'] .= "<script>Page.langNumric('langNumric', '{$row_count['cnt']}', 'видеозапись', 'видеозаписи', 'видеозаписей', 'видеозапись', 'видеозаписей');</script><div class=\"allbar_title\" style=\"margin-bottom:0;border-bottom:0\">В поиске найдено <span id=\"seAudioNum\">{$row_count['cnt']}</span> <span id=\"langNumric\"></span>  |  <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a>  |  <a href=\"/public/videos{$pid}\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все видеозаписи</a></div>";
                }

                foreach ($sql_ as $row) {

                    $tpl->set('{photo}', stripslashes($row['photo']));
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{id}', $row['id']);
                    $tpl->set('{pid}', $pid);
                    $tpl->set('{user-id}', $row['owner_user_id']);

                    if ($row['descr']) {
                        $tpl->set('{descr}', stripslashes($row['descr']) . '...');
                    } else {
                        $tpl->set('{descr}', '');
                    }

                    $tpl->set('{comm}', $row['comm_num'] . ' ' . declWord($row['comm_num'], 'comments'));
                    $date_str = megaDate(strtotime($row['add_date']));
                    $tpl->set('{date}', $date_str);
                    //Права админа
                    if ($public_admin) {

                        $tpl->set('[admin-group]', '');
                        $tpl->set('[/admin-group]', '');
                        $tpl->set_block("'\\[all-users\\](.*?)\\[/all-users\\]'si", "");

                    } else {

                        $tpl->set_block("'\\[admin-group\\](.*?)\\[/admin-group\\]'si", "");
                        $tpl->set('[all-users]', '');
                        $tpl->set('[/all-users]', '');

                    }

                    $tpl->compile('content');

                }

            } else if (!$page_cnt) {
                $tpl->result['info'] .= "<div class=\"allbar_title\">Нет видеозаписей  |  <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a>  |  <a href=\"/public/videos{$pid}\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все видеозаписи</a></div>";
                msgbox('', '<br /><br /><br />По запросу <b>' . stripslashes($query) . '</b> не найдено ни одной видеозаписи.<br /><br /><br />', 'info_2');
            }
            AjaxTpl($tpl);
            break;

        //################### Страница всех видео ###################//
        default:

            $metatags['title'] = 'Видеозаписи сообщества';

            $pid = (new Request)->int('pid');

            $sql_limit = 20;
            $page_cnt = (new Request)->int('page');
            if ($page_cnt > 0) {
                $page_cnt *= $sql_limit;
            }

            if ($page_cnt) {
                NoAjaxQuery();
            }

            $infoGroup = $db->super_query("SELECT videos_num, adres, admin FROM `communities` WHERE id = '{$pid}'");

            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                $public_admin = true;
            } else {
                $public_admin = false;
            }

            if ($infoGroup['videos_num']) {

                $sql_ = $db->super_query("SELECT id, photo, title, descr, comm_num, add_date, owner_user_id FROM `videos` WHERE public_id = '{$pid}' ORDER by `add_date` DESC LIMIT {$page_cnt}, {$sql_limit}", true);

                if ($sql_) {

                    $tpl->load_template('public_videos/video.tpl');

                    $tpl->result['content'] .= '<div id="allGrAudis">';

                    foreach ($sql_ as $row) {

                        $tpl->set('{photo}', stripslashes($row['photo']));
                        $tpl->set('{title}', stripslashes($row['title']));
                        $tpl->set('{id}', $row['id']);
                        $tpl->set('{pid}', $pid);
                        $tpl->set('{user-id}', $row['owner_user_id']);

                        if ($row['descr']) {
                            $tpl->set('{descr}', stripslashes($row['descr']) . '...');
                        } else {
                            $tpl->set('{descr}', '');
                        }

                        $tpl->set('{comm}', $row['comm_num'] . ' ' . declWord($row['comm_num'], 'comments'));
                        $date_str = megaDate(strtotime($row['add_date']));
                        $tpl->set('{date}', $date_str);
                        //Права админа
                        if ($public_admin) {
                            $tpl->set('[admin-group]', '');
                            $tpl->set('[/admin-group]', '');
                            $tpl->set_block("'\\[all-users\\](.*?)\\[/all-users\\]'si", "");
                        } else {
                            $tpl->set_block("'\\[admin-group\\](.*?)\\[/admin-group\\]'si", "");
                            $tpl->set('[all-users]', '');
                            $tpl->set('[/all-users]', '');
                        }
                        $tpl->compile('content');
                    }

                    if ($infoGroup['videos_num'] > $sql_limit && !$page_cnt) {
                        $tpl->result['content'] .= '<div id="ListAudioAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="ListAudioAddedLoadAjax()" id="wall_l_href_se_audiox"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_loadx">Показать больше видеозаписей</div></div>';
                    }

                    $tpl->result['content'] .= '</div>';

                }

            }

            if (!$page_cnt) {

                $tpl->load_template('public_videos/top.tpl');
                $tpl->set('{pid}', $pid);

                if ($infoGroup['adres']) {
                    $tpl->set('{adres}', $infoGroup['adres']);
                } else {
                    $tpl->set('{adres}', 'public' . $pid);
                }

                if ($infoGroup['videos_num']) {
                    $tpl->set('{videos-num}', $infoGroup['videos_num'] . ' <span id="langNumricAll"></span>');
                } else {
                    $tpl->set('{videos-num}', 'Нет видеозаписей');
                }

                $tpl->set('{x-videos-num}', $infoGroup['videos_num']);

                if (!$infoGroup['videos_num']) {
                    $tpl->set('[no]', '');
                    $tpl->set('[/no]', '');
                    $tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si", "");
                } else {
                    $tpl->set('[yes]', '');
                    $tpl->set('[/yes]', '');
                    $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
                }
                $tpl->compile('info');
            }
            if ($page_cnt) {
                AjaxTpl($tpl);
            }
            compile($tpl);
    }

//    $tpl->clear();
//    $db->free();

} else {

    $user_speedbar = 'Информация';
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}