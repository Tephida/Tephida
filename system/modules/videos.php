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

NoAjaxQuery();

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $limit_vieos = 20;
    $server_time = Registry::get('server_time');

    switch ($act) {

        //################### Страница добавления видео ###################//
        case "add":
            NoAjaxQuery();
            $tpl->load_template('videos/add.tpl');
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        //################### Добавление видео в БД ###################//
        case "send":
            NoAjaxQuery();
            $config = settings_get();
            if ($config['video_mod_add'] == 'yes') {
                $good_video_lnk = (new Request)->filter('good_video_lnk');
                $title = (new Request)->filter('title', 25000, true);
                $descr = (new Request)->filter('descr', 3000);
                $privacy = (new Request)->int('privacy');
                if ($privacy <= 0 || $privacy > 3) {
                    $privacy = 1;
                }

                //Если youtube то добавляем префикс src=" и составляем ответ для скрипта, для вставки в БД
                if (preg_match("/src=\"https:\/\/www.youtube.com|src=\"https:\/\/youtube.com/i", 'src="' . $good_video_lnk)) {
                    $good_video_lnk = str_replace(array('#', '!'), '', $good_video_lnk);
                    $exp_y = explode('v=', $good_video_lnk);
                    $exp_x = explode('&', $exp_y[1]);
                    $result_video_lnk = '<iframe width="770" height="420" src="https://www.youtube.com/embed/' . $exp_x[0] . '"  allowfullscreen></iframe>';
                }

                //Если vimeo, То добавляем префикс src="
                if (preg_match("/src=\"http:\/\/www.vimeo.com|src=\"http:\/\/vimeo.com/i", 'src="' . $good_video_lnk)) {
                    $exp_frutube = explode('com/', $good_video_lnk);
                    $result_video_lnk = '<iframe src="https://player.vimeo.com/video/' . $exp_frutube[1] . '" width="770" height="420" frameborder="0"></iframe>';
                }

                $result_video_lnk = $result_video_lnk ?? null;

                //Формируем данные о фото
                $photo = (new Request)->filter('photo');
                $photo = str_replace("\\", "/", $photo);
                $img_name_arr = explode(".", $photo);
                $img_format = to_translit(end($img_name_arr));
                $image_name = substr(md5(time() . md5($good_video_lnk)), 0, 15);

                //Разрешенные форматы
                $allowed_files = array('jpg', 'jpeg', 'jpe', 'png', 'gif');

                //Загружаем картинку на сайт
                if (in_array(strtolower($img_format), $allowed_files) && preg_match("/https:\/\//i", $photo) && $result_video_lnk) {

                    //Директория загрузки фото
                    $upload_dir = ROOT_DIR . '/uploads/videos/' . $user_id;

                    //Если нет папки юзера, то создаём её
                    Filesystem::createDir($upload_dir);

//					copy($photo, $upload_dir.'/'.$image_name.'.'.$img_format);

//                    if (!copy($photo, $upload_dir.'/'.$image_name.'.'.$img_format)) {
//                        echo "не удалось скопировать ".$upload_dir.'/'.$image_name.'.'.$img_format." ...\n";
//                        echo $photo;
//                        exit();
//                    }

                    file_put_contents($upload_dir . '/' . $image_name . '.' . $img_format, file_get_contents($photo));
                    if (!file_exists($upload_dir . '/' . $image_name . '.' . $img_format)) {
                        echo "не удалось скопировать " . $upload_dir . '/' . $image_name . '.' . $img_format . " ...\n";
                        echo $photo;
                        exit();
                    }

                    $tmb = new Thumbnail($upload_dir . '/' . $image_name . '.' . $img_format);
                    $tmb->size_auto('175x131');
                    $tmb->jpeg_quality(100);
                    $tmb->save($upload_dir . '/' . $image_name . '.' . $img_format);
                }

                if ($result_video_lnk && $title) {
                    $photo = $config['home_url'] . 'uploads/videos/' . $user_id . '/' . $image_name . '.' . $img_format;
                    $db->query("INSERT INTO `videos` SET owner_user_id = '{$user_id}', video = '{$result_video_lnk}', photo = '{$photo}', title = '{$title}', descr = '{$descr}', add_date = NOW(), privacy = '{$privacy}'");
                    $dbid = $db->insert_id();

                    $db->query("UPDATE `users` SET user_videos_num = user_videos_num+1 WHERE user_id = '{$user_id}'");

                    $photo = str_replace($config['home_url'], '/', $photo);

                    //Добавляем действия в ленту новостей
                    $generateLastTime = $server_time - 10800;
                    $row = $db->super_query("SELECT ac_id, action_text FROM `news` WHERE action_time > '{$generateLastTime}' AND action_type = 2 AND ac_user_id = '{$user_id}'");
                    if ($row) {
                        $db->query("UPDATE `news` SET action_text = '{$dbid}|{$photo}||{$row['action_text']}', action_time = '{$server_time}' WHERE ac_id = '{$row['ac_id']}'");
                    } else {
                        $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 2, action_text = '{$dbid}|{$photo}', action_time = '{$server_time}'");
                    }

                    //Чистим кеш
                    Cache::mozgMassClearCacheFile("user_{$user_id}/page_videos_user|user_{$user_id}/page_videos_user_friends|user_{$user_id}/page_videos_user_all|user_{$user_id}/profile_{$user_id}|user_{$user_id}/videos_num_all|user_{$user_id}/videos_num_friends");

                    if ((new Request)->int('notes') == 1) {
                        echo "{$photo}|{$user_id}|{$dbid}";
                    }
                }
            } else {
                echo 'error';
            }

            break;

        //################### Парсер . Загрузка данных о видео ###################//
        case "load":
            NoAjaxQuery();

            $video_lnk = (new Request)->filter('video_lnk');

            if (preg_match("/https:\/\/www.youtube.com|https:\/\/youtube.com|https:\/\/www.vimeo.com|https:\/\/vimeo.com/i", $video_lnk)) {

                //Открываем ссылку

                //Если ссылка youtube, то формируем xml ссылку для получения данных
                if (preg_match("/https:\/\/www.youtube.com|https:\/\/youtube.com/i", $video_lnk)) {
                    $exp_y = explode('v=', $video_lnk);
                    $exp_x = explode('&', $exp_y[1]);
                    $sock = fopen('https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $exp_x[0] . '&format=xml', 'r');
                } elseif (preg_match("/https:\/\/www.vimeo.com|https:\/\/vimeo.com/i", $video_lnk)) {
                    $sock = fopen('https://vimeo.com/api/oembed.xml?url=' . $video_lnk, 'r');
                } else {
                    $sock = fopen($video_lnk, 'r');
                }

                if (!$sock) {
                    echo 'no_serviece';
                } else {
                    $html = '';

                    //Если сервис youtube, rutube, smotri то просто выводи
                    if (preg_match("/https:\/\/www.youtube.com|https:\/\/youtube.com/i", $video_lnk)) {
                        while (!feof($sock)) {
                            $html .= fgets($sock);
                        }
                    }

                    //Если сервис Vimeo, то сразу применяем кодировку utf-8, win-1251
                    if (preg_match("/https:\/\/www.vimeo.com|https:\/\/vimeo.com/i", $video_lnk)) {
                        while (!feof($sock)) {
                            $html .= fgets($sock);
                        }
                    }

                    fclose($sock);

                    //Если сервис Vimeo, то выводим без кодировки
                    $data = str_replace(array('[', ']'), array('&iqu;', '&iqu2;'), $html);

                    //Если сервис youtube применяем кодировку utf-8, win-1251
                    $data_all = str_replace(array('[', ']'), array('&iqu;', '&iqu2;'), $html);

                    //Если видеосервис youtube
                    if (preg_match("/https:\/\/www.youtube.com|https:\/\/youtube.com/i", $video_lnk)) {
                        preg_match_all('`(<title>[^\[]+\</title>)`si', $data_all, $parse);
                        $res_title = rn_replace(str_replace(array('<title>', '</title>'), '', $parse[1][0]));

                        //Делаем фотку для youtube
                        $parse_start = explode('v=', $video_lnk);
                        $parse_end = explode('&', $parse_start[1]);
                        $res_img = "https://img.youtube.com/vi/{$parse_end[0]}/0.jpg";
                    }

                    //Если видео сервис vimeo
                    if (preg_match("/http:\/\/www.vimeo.com|http:\/\/vimeo.com/i", $video_lnk)) {
                        preg_match_all('`(<title>[^\[]+\</title>)`si', $data, $parse);
                        $res_title = str_replace(array('<title>', '</title>'), '', $parse[1][0]);

                        preg_match_all('`(<thumbnail_url>[^\[]+\</thumbnail_url>)`si', $data, $parse_img);
                        $res_img = str_replace(array('<thumbnail_url>', '</thumbnail_url>'), '', $parse_img[1][0]);

                        preg_match_all('`(<description>[^\[]+\</description>)`si', $data, $parse_descr);
                        $res_descr = myBrRn(rn_replace($parse_descr[1][0]));
                    }

                    $result_img = $res_img ?? null;
                    $res_title = $res_title ?? null;
                    $res_descr = $res_descr ?? null;

                    $result_title = trim(strip_tags(strtr($res_title, array('&#39;' => "'", '&quot;' => '"', '&iqu;' => '[', '&iqu2;' => ']'))));
                    $result_descr = trim(strip_tags($res_descr));

                    if ($result_img && $result_title) {
                        echo "{$result_img}:|:{$result_title}:|:{$result_descr}";
                    } else {
                        echo 'no_serviece';
                    }
                }
            } else {
                echo 'no_serviece';
            }

            break;

        //################### Удаление видео ###################//
        case "delet":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');

            if ($vid) {
                $row = $db->super_query("SELECT owner_user_id, photo, public_id FROM `videos` WHERE id = '{$vid}'");
                if ($row['owner_user_id'] == $user_id and !$row['public_id']) {
                    $db->query("DELETE FROM `videos` WHERE id = '{$vid}'");
                    $db->query("DELETE FROM `videos_comments` WHERE video_id = '{$vid}'");
                    $db->query("UPDATE `users` SET user_videos_num = user_videos_num-1 WHERE user_id = '{$row['owner_user_id']}'");

                    //Удаляем фотку
                    $exp_photo = explode('/', $row['photo']);
                    $photo_name = end($exp_photo);
                    Filesystem::delete(ROOT_DIR . '/uploads/videos/' . $row['owner_user_id'] . '/' . $photo_name);

                    //Чистим кеш
                    Cache::mozgMassClearCacheFile("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all|user_{$row['owner_user_id']}/profile_{$row['owner_user_id']}|user_{$row['owner_user_id']}/videos_num_all|user_{$row['owner_user_id']}/videos_num_friends|wall/video{$vid}");
                }
            }

            break;

        //################### Страница редактирования видео ###################//
        case "edit":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');
            if ($vid) {
                $row = $db->super_query("SELECT title, descr, privacy FROM `videos` WHERE id = '{$vid}' AND owner_user_id = '{$user_id}'");
                if ($row) {
                    $tpl->load_template('videos/editpage.tpl');
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{descr}', stripslashes(myBrRn($row['descr'])));
                    $tpl->set('{privacy}', $row['privacy']);
                    $tpl->set('{privacy-text}', strtr($row['privacy'], array('1' => 'Все пользователи', '2' => 'Только друзья', '3' => 'Только я')));
                    $tpl->compile('content');
                    AjaxTpl($tpl);
                }
            }

            break;

        //################### Сохранение отредактированных данных ###################//
        case "editsave":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');

            if ($vid) {
                $title = (new Request)->filter('title', 25000, true);
                $descr = (new Request)->filter('descr', 3000);
                $privacy = (new Request)->int('privacy');
                if ($privacy <= 0 || $privacy > 3) {
                    $privacy = 1;
                }

                //Проверка на существования записи
                $row = $db->super_query("SELECT owner_user_id, public_id FROM `videos` WHERE id = '{$vid}'");
                if ($row['owner_user_id'] == $user_id && !$row['public_id']) {
                    $db->query("UPDATE `videos` SET title = '{$title}', descr = '{$descr}', privacy = '{$privacy}' WHERe id = '{$vid}'");
                    echo stripslashes($descr);
                    //Чистим кеш
                    Cache::mozgMassClearCacheFile("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all|user_{$row['owner_user_id']}/videos_num_all|user_{$row['owner_user_id']}/videos_num_friends|wall/video{$vid}");
                }
            }
            break;

        //################### Просмотр видео ###################//
        case "view":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');
            $close_link = (new Request)->filter('close_link');

            $get_user_id = (new Request)->int('user_id');

            $db = Registry::get('db');
            $user_info = $user_info ?? Registry::get('user_info');
//Выводи данные о видео если оно есть
            $row = $db->super_query("SELECT tb1.video, title, add_date, descr, owner_user_id, views, comm_num, privacy, public_id, tb2.user_search_pref FROM `videos` tb1, `users` tb2 WHERE tb1.id = '{$vid}' AND tb1.owner_user_id = tb2.user_id");

            if ($row) {
                //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                if ($user_id !== $get_user_id) {
                    $check_friend = CheckFriends($row['owner_user_id']);
                } else {
                    $check_friend = null;
                }

                //Blacklist
                $CheckBlackList = CheckBlackList($row['owner_user_id']);

                //Приватность
                if ((!$CheckBlackList && $row['privacy'] == 1 && $row['privacy'] == 2 && $check_friend) || $user_info['user_id'] == $row['owner_user_id']) {
                    $privacy = true;
                } else {
                    $privacy = false;
                }

                if ($privacy) {
                    $config = settings_get();
                    //Выводим комментарии если они есть
                    if ($row['comm_num'] && $config['video_mod_comm'] == 'yes') {
                        if ($row['public_id']) {
                            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$row['public_id']}'");
                            if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                                $public_admin = true;
                            } else {
                                $public_admin = false;
                            }
                        } else {
                            $public_admin = false;
                        }
                        if ($row['comm_num'] > 3) {
                            $limit_comm = $row['comm_num'] - 3;
                        } else {
                            $limit_comm = 0;
                        }
                        $sql_comm = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `videos_comments` tb1, `users` tb2 WHERE tb1.video_id = '{$vid}' AND tb1.author_user_id = tb2.user_id ORDER by `add_date` ASC LIMIT {$limit_comm}, {$row['comm_num']}", true);
                        $tpl->load_template('videos/comment.tpl');
                        foreach ($sql_comm as $row_comm) {
                            OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                            $tpl->set('{uid}', $row_comm['author_user_id']);
                            $tpl->set('{author}', $row_comm['user_search_pref']);
                            $tpl->set('{comment}', stripslashes($row_comm['text']));
                            $tpl->set('{id}', $row_comm['id']);
                            $date_str = megaDate(strtotime($row_comm['add_date']));
                            $tpl->set('{date}', $date_str);
                            if ($row_comm['author_user_id'] == $user_id || $row['owner_user_id'] == $user_id || $public_admin) {
                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');
                            } else {
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }
                            if ($row_comm['user_photo']) {
                                $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comm['author_user_id'] . '/50_' . $row_comm['user_photo']);
                            } else {
                                $tpl->set('{ava}', '/images/no_ava_50.png');
                            }
                            $tpl->compile('comments');
                        }
                    }

                    $tpl->load_template('videos/full.tpl');
                    $tpl->set('{vid}', $vid);
                    $tpl->set('{video}', $row['video']);
                    if ($row['views']) {
                        $tpl->set('{views}', $row['views'] . ' ' . declWord($row['views'], 'video_views') . '<br /><br />');
                    } else {
                        $tpl->set('{views}', '');
                    }
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{descr}', stripslashes($row['descr']));
                    $tpl->set('{author}', $row['user_search_pref']);
                    $tpl->set('{uid}', $row['owner_user_id']);
                    $tpl->set('{comments}', $tpl->result['comments'] ?? '');
                    $tpl->set('{comm-num}', $row['comm_num']);
                    $tpl->set('{owner-id}', $row['owner_user_id']);
                    $tpl->set('{close-link}', $close_link);
                    $date_str = megaDate(strtotime($row['add_date']));
                    $tpl->set('{date}', $date_str);
                    if ($row['owner_user_id'] == $user_id) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                        $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    } else {
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        $tpl->set('[not-owner]', '');
                        $tpl->set('[/not-owner]', '');
                    }
                    if ($row['public_id']) {
                        $tpl->set_block("'\\[public\\](.*?)\\[/public\\]'si", "");
                    } else {
                        $tpl->set('[public]', '');
                        $tpl->set('[/public]', '');
                    }
                    if ($config['video_mod_add_my'] == 'no') {
                        $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    }
                    $tpl->set('{prev-text-comm}', declWord(($row['comm_num'] - 3), 'prev') . ' ' . ($row['comm_num'] - 3) . ' ' . declWord(($row['comm_num'] - 3), 'comments'));
                    if ($row['comm_num'] < 4) {
                        $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                    } else {
                        $tpl->set('[all-comm]', '');
                        $tpl->set('[/all-comm]', '');
                    }
                    if ($config['video_mod_comm'] == 'yes') {
                        $tpl->set('[admin-comments]', '');
                        $tpl->set('[/admin-comments]', '');
                    } else {
                        $tpl->set_block("'\\[admin-comments\\](.*?)\\[/admin-comments\\]'si", "");
                    }

                    $tpl->compile('content');
                    AjaxTpl($tpl);

                    $db->query("UPDATE LOW_PRIORITY `videos` SET views = views+1 WHERE id = '" . $vid . "'");
                } else {
                    echo 'err_privacy';
                }
            } else {
                echo 'no_video';
            }

            break;

        //################### Добавления комментария в базу ###################//
        case "addcomment":
            NoAjaxQuery();
            $config = settings_get();
            if ($config['video_mod_comm'] == 'yes') {
                $vid = (new Request)->int('vid');
                $comment = (new Request)->filter('comment');

                //Проверка на существования видео
                $check_video = $db->super_query("SELECT owner_user_id, photo, public_id FROM `videos` WHERE id = '{$vid}'");

                //ЧС
                $CheckBlackList = CheckBlackList($check_video['owner_user_id']);
                if (!$CheckBlackList) {
                    if ($check_video && empty($comment)) {
                        $db->query("INSERT INTO `videos_comments` SET author_user_id = '{$user_id}', video_id = '{$vid}', text = '{$comment}', add_date = NOW()");
                        $id = $db->insert_id();
                        $db->query("UPDATE `videos` SET comm_num = comm_num+1 WHERE id = '{$vid}'");

                        $tpl->load_template('videos/comment.tpl');
                        $tpl->set('{online}', $lang['online']);
                        $tpl->set('{uid}', $user_id);
                        $tpl->set('{author}', $user_info['user_search_pref']);
                        $tpl->set('{comment}', stripslashes($comment));
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                        $tpl->set('{id}', $id);
                        $tpl->set('{date}', langdate('сегодня в H:i', time()));
                        if ($user_info['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $user_id . '/50_' . $user_info['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/no_ava_50.png');
                        }
                        $tpl->compile('content');
                        if (!$check_video['public_id']) {
                            //Добавляем действие в ленту новостей "ответы" владельцу фотографии
                            if ($user_id != $check_video['owner_user_id']) {
                                $check_video['photo'] = str_replace($config['home_url'], '/', $check_video['photo']);
                                $comment = str_replace("|", "&#124;", $comment);
                                $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 9, action_text = '{$comment}|{$check_video['photo']}|{$vid}', obj_id = '{$id}', for_user_id = '{$check_video['owner_user_id']}', action_time = '{$server_time}'");

                                //Вставляем событие в моментальные оповещения
                                $row_userOW = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$check_video['owner_user_id']}'");
                                $update_time = $server_time - 70;

                                if ($row_userOW['user_last_visit'] >= $update_time) {
                                    $db->query("INSERT INTO `updates` SET for_user_id = '{$check_video['owner_user_id']}', from_user_id = '{$user_id}', type = '3', date = '{$server_time}', text = '{$comment}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/video{$check_video['owner_user_id']}_{$vid}'");
                                    Cache::mozgCreateCache("user_{$check_video['owner_user_id']}/updates", 1);
                                    //ИНАЧЕ Добавляем +1 юзеру для оповещения
                                } else {
                                    $cntCacheNews = Cache:: mozgCache('user_' . $check_video['owner_user_id'] . '/new_news');
                                    Cache::mozgCreateCache('user_' . $check_video['owner_user_id'] . '/new_news', ($cntCacheNews + 1));
                                }

                                //Отправка уведомления на E-mail
                                if ($config['news_mail_3'] == 'yes') {
                                    $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $check_video['owner_user_id'] . "'");
                                    if ($rowUserEmail['user_email']) {
                                        $mail = new \FluffyDollop\Support\ViiMail($config);
                                        $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
                                        $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '3'");
                                        $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
                                        $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
                                        $rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'] . 'video' . $check_video['owner_user_id'] . '_' . $vid, $rowEmailTpl['text']);
                                        $mail->send($rowUserEmail['user_email'], 'Новый комментарий к Вашей видеозаписи', $rowEmailTpl['text']);
                                    }
                                }
                            }
                            //Чистим кеш
                            Cache::mozgMassClearCacheFile("user_{$check_video['owner_user_id']}/page_videos_user|user_{$check_video['owner_user_id']}/page_videos_user_friends|user_{$check_video['owner_user_id']}/page_videos_user_all");
                        } else {
                            Cache::mozgClearCacheFile("groups/video{$check_video['public_id']}");
                        }
                        AjaxTpl($tpl);
                    }
                }
            } else {
                echo 'error';
            }
            break;

        //################### Удаления комментария ###################//
        case "delcomment":

            NoAjaxQuery();
            $comm_id = (new Request)->int('comm_id');
            //Проверка на существования комментария, и выводим ИД владельца видео
            $row = $db->super_query("SELECT tb1.video_id, author_user_id, tb2.owner_user_id, public_id FROM `videos_comments` tb1, `videos` tb2 WHERE tb1.id = '{$comm_id}' AND tb1.video_id = tb2.id");
            if ($row['public_id']) {
                $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$row['public_id']}'");
                if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                    $public_admin = true;
                } else {
                    $public_admin = false;
                }

                if ($public_admin && $row) {
                    $db->query("DELETE FROM `videos_comments` WHERE id = '{$comm_id}'");
                    $db->query("DELETE FROM `news` WHERE obj_id = '{$comm_id}' AND action_type = 9");
                    $db->query("UPDATE `videos` SET comm_num = comm_num-1 WHERE id = '{$row['video_id']}'");
                    Cache::mozgClearCacheFile("groups/video{$row['public_id']}");
                }

            } else if ($row['author_user_id'] == $user_id || $row['owner_user_id'] == $user_id) {
                $db->query("DELETE FROM `videos_comments` WHERE id = '{$comm_id}'");
                $db->query("DELETE FROM `news` WHERE obj_id = '{$comm_id}' AND action_type = 9");
                $db->query("UPDATE `videos` SET comm_num = comm_num-1 WHERE id = '{$row['video_id']}'");
                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all");
            }

            break;

        //################### Показ всех комментариев ###################//
        case "all_comm":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');
            $comm_num = (new Request)->int('num');
            $owner_id = (new Request)->int('owner_id');
            $row = $db->super_query("SELECT public_id FROM `videos` WHERE id = '{$vid}'");
            if ($row['public_id']) {
                $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$row['public_id']}'");
                if (str_contains($infoGroup['admin'], "u{$user_id}|")) {
                    $public_admin = true;
                } else {
                    $public_admin = false;
                }

            } else {
                $public_admin = false;
            }

            if ($comm_num > 3 && $vid && $owner_id) {
                $limit_comm = $comm_num - 3;
                $sql_comm = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `videos_comments` tb1, `users` tb2 WHERE tb1.video_id = '{$vid}' AND tb1.author_user_id = tb2.user_id ORDER by `add_date` ASC LIMIT 0, {$limit_comm}", true);
                $tpl->load_template('videos/comment.tpl');
                foreach ($sql_comm as $row_comm) {

                    $tpl->set('{uid}', $row_comm['author_user_id']);
                    $tpl->set('{author}', $row_comm['user_search_pref']);
                    $tpl->set('{comment}', stripslashes($row_comm['text']));
                    $tpl->set('{id}', $row_comm['id']);
                    OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                    $date_str = megaDate(strtotime($row_comm['add_date']));
                    $tpl->set('{date}', $date_str);
                    if ($row_comm['author_user_id'] == $user_id && $owner_id == $user_id && $public_admin) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                    } else {
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                    }
                    $config = settings_get();
                    if ($row_comm['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comm['author_user_id'] . '/50_' . $row_comm['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $tpl->compile('content');
                }
            }
            AjaxTpl($tpl);
            break;

        //################### Страница всех видео юзера, для прикрепления видео кому-то на стену ###################//
        case "all_videos":
            NoAjaxQuery();
            $notes = (new Request)->int('notes');

            //Для навигатор
            $page = (new Request)->int('page', 1);
            $gcount = 24;
            $limit_page = ($page - 1) * $gcount;

            //Делаем SQL запрос на вывод
            $sql_ = $db->super_query("SELECT id, photo, title FROM `videos` WHERE owner_user_id = '{$user_id}' AND public_id = '0' ORDER by `add_date` DESC LIMIT {$limit_page}, {$gcount}", 1);

            //Выводим кол-во видео
            $count = $db->super_query("SELECT user_videos_num FROM `users` WHERE user_id = '{$user_id}'");

            if ($count['user_videos_num']) {
                if ($notes) {
                    $tpl->load_template('videos/box_all_video_notes_top.tpl');
                } else {
                    $tpl->load_template('videos/box_all_video_top.tpl');
                }

                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set('{photo-num}', $count['user_videos_num'] . ' ' . declWord($count['user_videos_num'], 'videos'));
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('content');

                //Выводим циклом видео
                if (!$notes) {
                    $tpl->load_template('videos/box_all_video.tpl');
                } else {
                    $tpl->load_template('videos/box_all_video_notes.tpl');
                }

                foreach ($sql_ as $row) {
                    $tpl->set('{photo}', $row['photo']);
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{video-id}', $row['id']);
                    $tpl->set('{user-id}', $user_id);
                    $tpl->compile('content');
                }
                box_navigation($gcount, $count['user_videos_num'], $page, 'wall.attach_addvideo', $notes);

                $tpl->load_template('albums_editcover.tpl');
                $tpl->set('[bottom]', '');
                $tpl->set('[/bottom]', '');
                $tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si", "");
                $tpl->compile('content');
            } else
                if ($notes) {
                    echo $lang['videos_box_none'] . '<div class="button_div_gray fl_l" style="margin-left:210px;margin-top:20px"><button onClick="videos.add(1)">Добавить новый видеоролик</button></div>';
                } else {
                    echo $lang['videos_box_none'];
                }
            AjaxTpl($tpl);
            break;

        //################### Страница всех видео юзера, для прикрепления видео в сообщество ###################//
        case "all_videos_public":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            //Для навигатор
            $page = (new Request)->int('page', 1);
            $gcount = 24;
            $limit_page = ($page - 1) * $gcount;
            //Делаем SQL запрос на вывод
            $sql_ = $db->super_query("SELECT id, photo, title FROM `videos` WHERE public_id = '{$pid}' ORDER by `add_date` DESC LIMIT {$limit_page}, {$gcount}", true);
            //Выводим кол-во видео
            $count = $db->super_query("SELECT videos_num FROM `communities` WHERE id = '{$pid}'");
            if ($count['videos_num']) {
                $tpl->load_template('videos/box_all_video_top.tpl');
                $tpl->set('[top]', '');
                $tpl->set('[/top]', '');
                $tpl->set('{photo-num}', $count['videos_num'] . ' ' . declWord($count['videos_num'], 'videos'));
                $tpl->set_block("'\\[bottom\\](.*?)\\[/bottom\\]'si", "");
                $tpl->compile('content');
                //Выводим циклом видео
                $tpl->load_template('videos/box_all_video.tpl');
                foreach ($sql_ as $row) {
                    $tpl->set('{photo}', $row['photo']);
                    $tpl->set('{title}', stripslashes($row['title']));
                    $tpl->set('{video-id}', $row['id']);
                    $tpl->set('{user-id}', $user_id);
                    $tpl->compile('content');
                }
                box_navigation($gcount, $count['videos_num'], $page, 'wall.attach_addvideo_public', $pid);
                $tpl->load_template('albums_editcover.tpl');
                $tpl->set('[bottom]', '');
                $tpl->set('[/bottom]', '');
                $tpl->set_block("'\\[top\\](.*?)\\[/top\\]'si", "");
                $tpl->compile('content');
            } else {
                echo '<div class="info_center" style="padding-top:170px">Нет ни одной видеозаписи.</div>';
            }
            AjaxTpl($tpl);
            break;

        //################### Бесконечная подгрузка видео из БД ###################//
        case "page":
            NoAjaxQuery();
            $get_user_id = (new Request)->int('get_user_id');
            $last_id = (new Request)->int('last_id');
            if (!$get_user_id) {
                $get_user_id = $user_id;
            }

            //ЧС
            $CheckBlackList = CheckBlackList($get_user_id);
            if (!$CheckBlackList) {
                if ($last_id) {
                    //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                    if ($user_id != $get_user_id) {
                        $check_friend = CheckFriends($get_user_id);
                    }

                    $check_friend = $check_friend ?? null;

                    //Настройки приватности
                    if ($user_id == $get_user_id) {
                        $sql_privacy = "";
                    } elseif ($check_friend) {
                        $sql_privacy = "AND privacy regexp '[[:<:]](1|2)[[:>:]]'";
                    } else {
                        $sql_privacy = "AND privacy = 1";
                    }

                    //SQL Запрос
                    $sql_ = $db->super_query("SELECT id, title, photo, comm_num, add_date, SUBSTRING(descr, 1, 180) AS descr FROM `videos` WHERE owner_user_id = '{$get_user_id}' AND id < '{$last_id}' {$sql_privacy} AND public_id = '0' ORDER by `add_date` DESC LIMIT 0, {$limit_vieos}", true);

                    //Если есть ответ из БД
                    if ($sql_) {
                        $tpl->load_template('videos/short.tpl');
                        foreach ($sql_ as $row) {
                            $tpl->set('{photo}', stripslashes($row['photo']));
                            $tpl->set('{title}', stripslashes($row['title']));
                            $tpl->set('{id}', $row['id']);
                            $tpl->set('{user-id}', $get_user_id);
                            if ($row['descr']) {
                                $tpl->set('{descr}', stripslashes($row['descr']) . '...');
                            } else {
                                $tpl->set('{descr}', '');
                            }
                            $tpl->set('{comm}', $row['comm_num'] . ' ' . declWord($row['comm_num'], 'comments'));
                            $date_str = megaDate(strtotime($row['add_date']));
                            $tpl->set('{date}', $date_str);
                            if ($get_user_id == $user_id) {
                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');
                            } else {
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }
                            $tpl->compile('content');
                        }
                    }
                    AjaxTpl($tpl);
                }
            }

            break;

        //################### Добавление видео к себе в список ###################//
        case "addmylist":
            NoAjaxQuery();
            $vid = (new Request)->int('vid');
            $row = $db->super_query("SELECT video, photo, title, descr FROM `videos` WHERE id = '{$vid}'");
            $config = settings_get();
            if ($row && $config['video_mod_add_my'] == 'yes') {
                //Директория загрузки фото
                $upload_dir = ROOT_DIR . '/uploads/videos/' . $user_id;

                //Если нет папки юзера, то создаём её
                Filesystem::createDir($upload_dir);

                $expPhoto = end(explode('/', $row['photo']));
                Filesystem::copy($row['photo'], ROOT_DIR . "/uploads/videos/{$user_id}/{$expPhoto}");
                $newPhoto = "{$config['home_url']}uploads/videos/{$user_id}/{$expPhoto}";
                $db->query("INSERT INTO `videos` SET owner_user_id = '{$user_id}', video = '{$row['video']}', photo = '{$newPhoto}', title = '{$row['title']}', descr = '{$row['descr']}', add_date = NOW(), privacy = 1");
                $dbid = $db->insert_id();
                $db->query("UPDATE `users` SET user_videos_num = user_videos_num+1 WHERE user_id = '{$user_id}'");

                //Чистим кеш
                Cache::mozgMassClearCacheFile("user_{$user_id}/page_videos_user|user_{$user_id}/page_videos_user_friends|user_{$user_id}/page_videos_user_all|user_{$user_id}/profile_{$user_id}|user_{$user_id}/videos_num_all|user_{$user_id}/videos_num_friends");
            }

            break;

        default:

            //################### Вывод всех видео ###################//
            $get_user_id = (new Request)->int('get_user_id');
            if (!$get_user_id) {
                $get_user_id = $user_id;
            }

            //ЧС
            $CheckBlackList = CheckBlackList($get_user_id);
            if (!$CheckBlackList) {

                //Выводи кол-во видео записей
                $owner = $db->super_query("SELECT user_videos_num, user_search_pref FROM `users` WHERE user_id = '{$get_user_id}'");
                if ($owner) {
                    $name_info = explode(' ', $owner['user_search_pref']);
                    $metatags['title'] = $lang['videos'] . ' ' . grammaticalName($name_info[0]) . ' ' . grammaticalName($name_info[1]);
//Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
                    if ($user_id != $get_user_id) {
                        $check_friend = CheckFriends($get_user_id);
                    } else {
                        $check_friend = null;
                    }

                    //Настройки приватности
                    if ($user_id == $get_user_id) {
                        $sql_privacy = "";
                        $cache_pref = '';
                    } elseif ($check_friend) {
                        $sql_privacy = "AND privacy regexp '[[:<:]](1|2)[[:>:]]'";
                        $cache_pref = '_friends';
                    } else {
                        $sql_privacy = "AND privacy = 1";
                        $cache_pref = '_all';
                    }

                    //Если страницу смотрит другой юзер, то считаем кол-во видео
                    if ($user_id != $get_user_id) {
                        $video_cnt = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos` WHERE owner_user_id = '{$get_user_id}' {$sql_privacy} AND public_id = '0'");
                        $owner['user_videos_num'] = $video_cnt['cnt'];
                    }

                    if ($get_user_id == $user_id) {
                        $user_speedbar = 'У Вас <span id="nums">' . ($owner['user_videos_num'] ? $owner['user_videos_num'] : false) . '</span> ' . declWord($owner['user_videos_num'], 'videos');
                    } else {
                        $user_speedbar = 'У ' . grammaticalName($name_info[0]) . ' ' . ($owner['user_videos_num'] ? $owner['user_videos_num'] : false) . ' ' . declWord($owner['user_videos_num'], 'videos');
                    }
                    if ($owner['user_videos_num']) {

                        //SQL Запрос
                        $sql_ = $db->super_query("SELECT id, title, photo, comm_num, add_date, SUBSTRING(descr, 1, 180) AS descr FROM `videos` WHERE owner_user_id = '{$get_user_id}' {$sql_privacy} AND public_id = '0' ORDER by `add_date` DESC LIMIT 0, {$limit_vieos}", true);

                        //Загружаем меню по видео
                        $tpl->load_template('videos/head.tpl');
                        $tpl->set('{user-id}', $get_user_id);
                        $tpl->set('{videos_num}', $owner['user_videos_num']);
                        $tpl->set('{name}', grammaticalName($name_info[0]));
                        if ($get_user_id == $user_id) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                            $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                        } else {
                            $tpl->set('[not-owner]', '');
                            $tpl->set('[/not-owner]', '');
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }
                        $config = settings_get();
                        if ($config['video_mod_add'] == 'yes') {
                            $tpl->set('[admin-video-add]', '');
                            $tpl->set('[/admin-video-add]', '');
                        } else {
                            $tpl->set_block("'\\[admin-video-add\\](.*?)\\[/admin-video-add\\]'si", "");
                        }

                        $tpl->compile('info');

                        if ($sql_) {
                            $tpl->load_template('videos/short.tpl');
                            $tpl->result['content'] .= '<span id="video_page" class="scroll_page">';
                            foreach ($sql_ as $row) {
                                $tpl->set('{photo}', stripslashes($row['photo']));
                                $tpl->set('{title}', stripslashes($row['title']));
                                $tpl->set('{user-id}', $get_user_id);
                                $tpl->set('{id}', $row['id']);
                                if ($row['descr']) {
                                    $tpl->set('{descr}', stripslashes($row['descr']) . '...');
                                } else {
                                    $tpl->set('{descr}', '');
                                }
                                $tpl->set('{comm}', $row['comm_num'] . ' ' . declWord($row['comm_num'], 'comments'));
                                $date_str = megaDate(strtotime($row['add_date']));
                                $tpl->set('{date}', $date_str);
                                if ($get_user_id == $user_id) {
                                    $tpl->set('[owner]', '');
                                    $tpl->set('[/owner]', '');
                                } else {
                                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                                }
                                $tpl->compile('content');
                            }
                            $tpl->result['content'] .= '</span>';

                        } else {
                            msgbox('', $lang['videos_nones_videos_user'], 'info_2');
                        }
                    } else {
                        if ($get_user_id == $user_id) {
                            msgbox('', $lang['videos_nones_videos_user'], 'info_2');
                        } else {
                            msgbox('', $owner['user_search_pref'] . ' ' . $lang['videos_none'], 'info_2');
                        }
                    }
                } else {
                }
                compile($tpl);
            } else {
                $user_speedbar = $lang['error'];
                msgbox('', $lang['no_notes'], 'info');
                compile($tpl);
            }
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}