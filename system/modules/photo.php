<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;
use FluffyDollop\Support\{Registry};
use Mozg\classes\Cache;

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');

    switch ($act) {
        //################### Добавления комментария ###################//

        case "addcomm":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            $comment = (new Request)->filter('comment');
            $date = date('Y-m-d H:i:s', $server_time);
            $hash = md5($user_id . $server_time . $_IP . $user_info['user_email'] . random_int(0, 1000000000)) . $comment . $pid;
            $check_photo = $db->super_query("SELECT album_id, user_id, photo_name FROM `photos` WHERE id = '{$pid}'");
            //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
            if ($user_info['user_id'] !== $check_photo['user_id']) {
                $check_friend = CheckFriends($check_photo['user_id']);
                $row_album = $db->super_query("SELECT privacy FROM `albums` WHERE aid = '{$check_photo['album_id']}'");
                $album_privacy = explode('|', $row_album['privacy']);
            } else {
                $album_privacy = null;
                $check_friend = null;
            }

            //ЧС
            $CheckBlackList = CheckBlackList($check_photo['user_id']);
            //Проверка на существование фотки и приватность
            if ((!$CheckBlackList && $check_photo && $album_privacy[1] == 1) || ($album_privacy[1] == 2 && $check_friend) || $user_info['user_id'] == $check_photo['user_id']) {
                $db->query("INSERT INTO `photos_comments` (pid, user_id, text, date, hash, album_id, owner_id, photo_name) VALUES ('{$pid}', '{$user_id}', '{$comment}', '{$date}', '{$hash}', '{$check_photo['album_id']}', '{$check_photo['user_id']}', '{$check_photo['photo_name']}')");
                $id = $db->insert_id();
                $db->query("UPDATE `photos` SET comm_num = comm_num+1 WHERE id = '{$pid}'");
                $db->query("UPDATE `albums` SET comm_num = comm_num+1 WHERE aid = '{$check_photo['album_id']}'");
                $date = langdate('сегодня в H:i', $server_time);
                $tpl->load_template('photo_comment.tpl');
                $tpl->set('{author}', $user_info['user_search_pref']);
                $tpl->set('{comment}', stripslashes($comment));
                $tpl->set('{uid}', $user_id);
                $tpl->set('{hash}', $hash);
                $tpl->set('{id}', $id);
                if ($user_info['user_photo']) {
                    $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $user_id . '/50_' . $user_info['user_photo']);
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }
                $tpl->set('{online}', $lang['online']);
                $tpl->set('{date}', langdate('сегодня в H:i', $server_time));
                $tpl->set('[owner]', '');
                $tpl->set('[/owner]', '');
                $tpl->compile('content');
                //Добавляем действие в ленту новостей "ответы" владельцу фотографии
                if ($user_id !== $check_photo['user_id']) {
                    $comment = str_replace("|", "&#124;", $comment);
                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 8, action_text = '{$comment}|{$check_photo['photo_name']}|{$pid}|{$check_photo['album_id']}', obj_id = '{$id}', for_user_id = '{$check_photo['user_id']}', action_time = '{$server_time}'");
                    //Вставляем событие в моментальные оповещания
                    $row_userOW = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$check_photo['user_id']}'");
                    $update_time = $server_time - 70;
                    if ($row_userOW['user_last_visit'] >= $update_time) {
                        $db->query("INSERT INTO `updates` SET for_user_id = '{$check_photo['user_id']}', from_user_id = '{$user_id}', type = '2', date = '{$server_time}', text = '{$comment}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/photo{$check_photo['user_id']}_{$pid}_{$check_photo['album_id']}'");
                        Cache::mozgCreateCache("user_{$check_photo['user_id']}/updates", 1);
                        //ИНАЧЕ Добавляем +1 юзеру для оповещания
                    } else {
                        //Добавляем +1 юзеру для оповещания
                        $cntCacheNews = Cache::mozgCache('user_' . $check_photo['user_id'] . '/new_news');
                        Cache::mozgCreateCache('user_' . $check_photo['user_id'] . '/new_news', ($cntCacheNews + 1));
                    }
                    //Отправка уведомления на E-mail
                    if ($config['news_mail_4'] == 'yes') {
                        $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $check_photo['user_id'] . "'");
                        if ($rowUserEmail['user_email']) {
                            $mail = new \FluffyDollop\Support\ViiMail($config);
                            $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
                            $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '4'");
                            $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
                            $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
                            $rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'] . 'photo' . $check_photo['user_id'] . '_' . $vid . '_' . $check_photo['album_id'], $rowEmailTpl['text']);
                            $mail->send($rowUserEmail['user_email'], 'Новый комментарий к Вашей фотографии', $rowEmailTpl['text']);
                        }
                    }
                }
                //Чистим кеш кол-во комментов
                Cache::mozgMassClearCacheFile("user_{$check_photo['user_id']}/albums_{$check_photo['user_id']}_comm|user_{$check_photo['user_id']}/albums_{$check_photo['user_id']}_comm_all|user_{$check_photo['user_id']}/albums_{$check_photo['user_id']}_comm_friends");
                AjaxTpl($tpl);
            } else {
                echo 'err_privacy';
            }
            break;
            //################### Удаление комментария ###################//
            
        case "del_comm":
            NoAjaxQuery();
            $hash = (new Request)->filter('hash', 32);
            $check_comment = $db->super_query("SELECT id, pid, album_id, owner_id FROM `photos_comments` WHERE hash = '{$hash}'");
            if ($check_comment) {
                $db->query("DELETE FROM `photos_comments` WHERE hash = '{$hash}'");
                $db->query("DELETE FROM `news` WHERE obj_id = '{$check_comment['id']}' AND action_type = 8");
                $db->query("UPDATE `photos` SET comm_num = comm_num-1 WHERE id = '{$check_comment['pid']}'");
                $db->query("UPDATE `albums` SET comm_num = comm_num-1 WHERE aid = '{$check_comment['album_id']}'");
                //Чистим кеш кол-во комментов
                Cache::mozgMassClearCacheFile("user_{$check_comment['owner_id']}/albums_{$check_comment['owner_id']}_comm|user_{$check_comment['owner_id']}/albums_{$check_comment['owner_id']}_comm_all|user_{$check_comment['owner_id']}/albums_{$check_comment['owner_id']}_comm_friends");
            }

            break;
            //################### Помещение фотографии на свою страницу ###################//
            
        case "crop":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            $i_left = (new Request)->int('i_left');
            $i_top = (new Request)->int('i_top');
            $i_width = (new Request)->int('i_width');
            $i_height = (new Request)->int('i_height');
            $check_photo = $db->super_query("SELECT photo_name, album_id FROM `photos` WHERE id = '{$pid}' AND user_id = '{$user_id}'");
            if ($check_photo && $i_width >= 100 && $i_height >= 100 && $i_left >= 0) {
                $imgInfo = explode('.', $check_photo['photo_name']);
                $newName = substr(md5($server_time . $check_photo['check_photo']), 0, 15) . "." . $imgInfo[1];
                $newDir = ROOT_DIR . "/uploads/users/{$user_id}/";

                //Создаём оригинал
                $tmb = new Thumbnail(ROOT_DIR . "/uploads/users/{$user_id}/albums/{$check_photo['album_id']}/{$check_photo['photo_name']}");
                $tmb->size_auto($i_width . "x" . $i_height, 0, "{$i_left}|{$i_top}");
                $tmb->jpeg_quality(90);
                $tmb->save($newDir . "o_{$newName}");
                //Создание главной фотографии
                $tmb = new Thumbnail($newDir . "o_{$newName}");
                $tmb->size_auto(200, 1);
                $tmb->jpeg_quality(100);
                $tmb->save($newDir . $newName);
                //Создание уменьшенной копии 50х50
                $tmb = new Thumbnail($newDir . "o_{$newName}");
                $tmb->size_auto('50x50');
                $tmb->jpeg_quality(100);
                $tmb->save($newDir . '50_' . $newName);
                //Создание уменьшенной копии 100х100
                $tmb = new Thumbnail($newDir . "o_{$newName}");
                $tmb->size_auto('100x100');
                $tmb->jpeg_quality(100);
                $tmb->save($newDir . '100_' . $newName);

                $tmb = new Thumbnail($newDir . "o_{$newName}");
                $tmb->size_auto('140x100');
                $tmb->jpeg_quality(100);
                $tmb->save($newDir . 'с_' . $newName);

                //Добавляем на стену
                $row = $db->super_query("SELECT user_sex FROM `users` WHERE user_id = '{$user_id}'");
                if ($row['user_sex'] == 2) {
                    $sex_text = 'обновила';
                } else {
                    $sex_text = 'обновил';
                }
                $wall_text = "<div class=\"profile_update_photo\"><a href=\"\" onClick=\"Photo.Profile(\'{$user_id}\', \'{$newName}\'); return false\"><img src=\"/uploads/users/{$user_id}/o_{$newName}\" style=\"margin-top:3px\"></a></div>";
                $db->query("INSERT INTO `wall` SET author_user_id = '{$user_id}', for_user_id = '{$user_id}', text = '{$wall_text}', add_date = '{$server_time}', type = '{$sex_text} фотографию на странице:'");
                $dbid = $db->insert_id();
                $db->query("UPDATE `users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$user_id}'");
                //Добавляем в ленту новостей
                $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$wall_text}', obj_id = '{$dbid}', action_time = '{$server_time}'");
                //Обновляем имя фотки в бд
                $db->query("UPDATE `users` SET user_photo = '{$newName}', user_wall_id = '{$dbid}' WHERE user_id = '{$user_id}'");
                Cache::mozgClearCacheFile("user_{$user_id}/profile_{$user_id}");
                Cache::mozgClearCache();
            }

            break;
            //################### Показ всех комментариев ###################//
            
        case "all_comm":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            $num = (new Request)->int('num');
            if ($num > 7) {
                $limit = $num - 3;
                $sql_comm = $db->super_query("SELECT tb1.user_id,text,date,id,hash,pid, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.pid = '{$pid}' ORDER by `date` ASC LIMIT 0, {$limit}", true);
                $tpl->load_template('photo_comment.tpl');
                foreach ($sql_comm as $row_comm) {
                    $tpl->set('{comment}', stripslashes($row_comm['text']));
                    $tpl->set('{uid}', $row_comm['user_id']);
                    $tpl->set('{id}', $row_comm['id']);
                    $tpl->set('{hash}', $row_comm['hash']);
                    $tpl->set('{author}', $row_comm['user_search_pref']);
                    if ($row_comm['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comm['user_id'] . '/50_' . $row_comm['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                    $date_str = megaDate(strtotime($row_comm['date']));
                    $tpl->set('{date}', $date_str);
                    $row_photo = $db->super_query("SELECT user_id FROM `photos` WHERE id = '{$row_comm['pid']}'");
                    if ($row_comm['user_id'] == $user_info['user_id'] || $row_photo['user_id'] == $user_info['user_id']) {
                        $tpl->set('[owner]', '');
                        $tpl->set('[/owner]', '');
                    } else {
                        $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                    }
                    $tpl->compile('content');
                }
                AjaxTpl($tpl);
            }
            break;
            //################### Просмотр ПРОСТОЙ фотографии не из альбома ###################//
            
        case "profile":
            $uid = (new Request)->int('uid');
            $photo_name = (new Request)->filter('photo');
            if (isset($_POST['type'])) {
                $photo = ROOT_DIR . "/uploads/attach/{$uid}/c_{$photo_name}";
            } else {
                $photo = ROOT_DIR . "/uploads/users/{$uid}/o_{$photo_name}";
            }
            if (file_exists($photo)) {
                $tpl->load_template('photos/photo_profile.tpl');
                $tpl->set('{uid}', $uid);
                if (isset($_POST['type'])) {
                    $tpl->set('{photo}', "/uploads/attach/{$uid}/{$photo_name}");
                }
                else {
                    $tpl->set('{photo}', "/uploads/users/{$uid}/o_{$photo_name}");
                }
                $tpl->set('{close-link}', (new Request)->filter('close_link'));
                $tpl->compile('content');
                AjaxTpl($tpl);
            } else {
                echo 'no_photo';
            }
            break;
            //################### Поворот фотографии ###################//
            
        case "rotation":
            $id = (new Request)->int('id');
            $row = $db->super_query("SELECT photo_name, album_id, user_id FROM `photos` WHERE id = '" . $id . "'");
            $photo_position = (new Request)->filter('pos');
            if (($row['photo_name'] && $photo_position == 'left') || ($photo_position == 'right' && $user_id == $row['user_id'])) {
                $filename = ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/' . $row['photo_name'];
                if ($photo_position == 'right') {
                    $degrees = -90;
                }
                if ($photo_position == 'left') {
                    $degrees = 90;
                }

                $degrees = $degrees ?? 0;

                $source = imagecreatefromjpeg($filename);
                $rotate = imagerotate($source, $degrees, 0);
                imagejpeg($rotate, ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/' . $row['photo_name'], 93);
                //Создание маленькой копии
                $tmb = new Thumbnail(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/' . $row['photo_name']);
                $tmb->size_auto('140x100');
                $tmb->jpeg_quality('100');
                $tmb->save(ROOT_DIR . '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/c_' . $row['photo_name']);
                echo '/uploads/users/' . $user_id . '/albums/' . $row['album_id'] . '/' . $row['photo_name'];
            }
            break;
            //################### Ставим оценку ###################//
            
        case "addrating":
            NoAjaxQuery();
            $rating = (new Request)->int('rating');
            if ($rating <= 0 OR $rating > 6) $rating = 5;
            $pid = (new Request)->int('pid');
            //Проверка на существование фото в базе
            $row = $db->super_query("SELECT user_id, album_id, photo_name FROM `photos` WHERE id = '{$pid}'");
            //Проверка ставил человек на это фото уже оценку или нет
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos_rating` WHERE user_id = '{$user_id}' AND photo_id = '{$pid}'");
            if ($row['user_id'] AND !$check['cnt'] AND $row['user_id'] != $user_id) {
                //Если человек ставит оценку 5+, то проверяем баланс
                if ($rating == 6) {
                    $rate_price = $config['rate_price']; #Цена оценки 5+
                    //Выводим текущий баланс юзера
                    $row_user = $db->super_query("SELECT user_balance FROM `users` WHERE user_id = '{$user_id}'");
                    if ($row_user['user_balance'] >= $rate_price) {
                        $price = true;
                    } else {
                        $price = false;
                    }
                    //Если хватает голосов, то отнимаем их, и пропускаем оценку
                    if ($price) {
                        $db->query("UPDATE `users` SET user_balance = user_balance-{$rate_price} WHERE user_id = '{$user_id}'");
                        $rating_max = ", rating_max = rating_max+1";
                    } else exit('1');
                }
                //Вставляем в лог, что юзер поставил оценку
                $db->query("INSERT INTO `photos_rating` SET photo_id = '{$pid}', user_id = '{$user_id}', date = '{$server_time}', rating = '{$rating}', owner_user_id = '{$row['user_id']}'");
                $id = $db->insert_id();
                //Обновляем данные у фото
                $db->query("UPDATE `photos` SET rating_num = rating_num+{$rating}, rating_all = rating_all+1 {$rating_max} WHERE id = '{$pid}'");
                //Вставляем в ленту "Ответы"
                if ($rating == 1) {
                    $action_update_text = '<div class="rating rating3 fl_r" style="background:url(\'/templates/' . $config['temp'] . '/images/rating3.png\') no-repeat;padding-top:10px">' . $rating . '</div>';
                } else if ($rating == 6) {
                    $action_update_text = '<div class="rating rating3 fl_r"  style="background:url(\'/templates/' . $config['temp'] . '/images/rating2.png\') no-repeat;padding-top:10px">5+</div>';
                } else {
                    $action_update_text = '<div class="rating rating3 fl_r" style="padding-top:10px">' . $rating . '</div>';
                }

                $action_update_text_news = str_replace(' fl_r', ' rate_fnews_bott', $action_update_text);
                $db->query("INSERT INTO `news` SET ac_user_id = '{$user_info['user_id']}', action_type = 12, action_text = '{$action_update_text_news}|{$row['photo_name']}|{$pid}|{$row['album_id']}', obj_id = '{$id}', for_user_id = '{$row['user_id']}', action_time = '{$server_time}'");
                //Вставляем событие в моментальные оповещания
                $row_owner = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$row['user_id']}'");
                $update_time = $server_time - 70;
                if ($row_owner['user_last_visit'] >= $update_time) {
                    $db->query("INSERT INTO `updates` SET for_user_id = '{$row['user_id']}', from_user_id = '{$user_info['user_id']}', type = '9', date = '{$server_time}', text = '{$action_update_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/photo{$row['user_id']}_{$pid}_{$row['album_id']}'");
                    Cache::mozgCreateCache("user_{$row['user_id']}/updates", 1);
                    //ИНАЧЕ делаем +1 для ленты
                    
                } else {
                    $cntCacheNews = Cache::mozgCache("user_{$row['user_id']}/new_news");
                    Cache::mozgCreateCache("user_{$row['user_id']}/new_news", ($cntCacheNews + 1));
                }
            }
            break;

        //################### Просмотр оценок ###################//
        case "view_rating":
            NoAjaxQuery();
            $pid = (new Request)->int('pid');
            $lid = (new Request)->int('lid');
            //Проверка на то, что есть фото
            $check = $db->super_query("SELECT rating_all FROM `photos` WHERE user_id = '{$user_info['user_id']}' AND id = '{$pid}'");
            //Если фото есть, то продолжаем вывод
            if ($check) {
                //Выводим список последних 10 оценок
                if ($lid) {
                    $where_sql = " AND id < '{$lid}'";
                }
                $sql_ = $db->super_query("SELECT tb1.id, rating, date, tb2.user_id, user_search_pref, user_photo FROM `photos_rating` tb1, `users` tb2 WHERE photo_id = '{$pid}' AND tb1.user_id = tb2.user_id {$where_sql} ORDER by `date` DESC LIMIT 0, 10", true);
                if ($sql_) {
                    $tpl->load_template('photos/rate_user.tpl');
                    foreach ($sql_ as $row) {
                        $tpl->set('{id-rate}', $row['id']);
                        $tpl->set('{name}', $row['user_search_pref']);
                        $tpl->set('{user-id}', $row['user_id']);
                        $tpl->set('{user-id}', $row['user_id']);
                        if ($row['rating'] == 1) {
                            $tpl->set('{rate}', '<div class="rating rating3" style="background:url(\'/images/rating3.png\')">' . $row['rating'] . '</div>');
                        } else if ($row['rating'] == 6) {
                            $tpl->set('{rate}', '<div class="rating rating3"  style="background:url(\'/images/rating2.png\')">5+</div>');
                        } else {
                            $tpl->set('{rate}', '<div class="rating rating3">' . $row['rating'] . '</div>');
                        }
                        if ($row['user_photo']) {
                            $tpl->set('{ava}', "/uploads/users/{$row['user_id']}/50_{$row['user_photo']}");
                        } else {
                            $tpl->set('{ava}', "/images/no_ava_50.png");
                        }
                        $date_str = megaDate($row['date']);
                        $tpl->set('{date}', $date_str);
                        $tpl->compile('rates_users');
                    }
                } else {
                    if (!$lid)
                        $tpl->result['rates_users'] = '<div class="info_center"><br /><br />Пока что никто не оценил Вашу фотографию.<br /><br /><br /></div>';
                }
                //Загружаем шаблон вывода
                if (!$lid) {
                    $tpl->load_template('photos/rating_main.tpl');
                    $tpl->set('{id}', $pid);
                    $tpl->set('{rates-users}', $tpl->result['rates_users']);
                    if ($check['rating_all'] > 10) {
                        $tpl->set('[prev]', '');
                        $tpl->set('[/prev]', '');
                    } else {
                        $tpl->set_block("'\\[prev\\](.*?)\\[/prev\\]'si", "");
                    }
                    $tpl->compile('content');
                } else {
                    $tpl->result['content'] = $tpl->result['rates_users'];
                }
                AjaxTpl($tpl);
            }

            break;
            //################### Удаление оценки ###################//
            
        case "del_rate":
            NoAjaxQuery();
            $id = (new Request)->int('id');
            //Выводим ИД фото и проверяем на админа фотки
            $row = $db->super_query("SELECT photo_id, rating FROM `photos_rating` WHERE id = '{$id}' AND owner_user_id = '{$user_info['user_id']}'");
            if ($row['photo_id']) {
                if ($row['rating'] == 6) {
                    $rating_max = ", rating_max = rating_max-1";
                }
                //Удаляем оценку
                $db->query("DELETE FROM `photos_rating` WHERE id = '{$id}'");
                //Удаляем оценку из ленты новостей
                $db->query("DELETE FROM `news` WHERE obj_id = '{$id}' AND action_type = '12'");
                //Обновляем данные у фото
                $db->query("UPDATE `photos` SET rating_num = rating_num-{$row['rating']}, rating_all = rating_all-1 {$rating_max} WHERE id = '{$row['photo_id']}'");//fixme
            }

            break;
        default:
            //################### Просмотр фотографии ###################//
            NoAjaxQuery();
            $uid = (new Request)->int('uid');
            $photo_id = (new Request)->int('pid');
            $fuser = (new Request)->int('fuser');
            $section = (new Request)->filter('section');
            //ЧС
            $CheckBlackList = CheckBlackList($uid);
            if (!$CheckBlackList) {
                //Получаем ID альбома
                $check_album = $db->super_query("SELECT album_id FROM `photos` WHERE id = '{$photo_id}'");
                //Если фотография вызвана не со стены
                if (!$fuser && $check_album) {
                    //Проверяем на наличии файла с позициями только для этого фоток
                    $check_pos = Cache::mozgCache('user_' . $uid . '/position_photos_album_' . $check_album['album_id']);

                    //Чистим кеш
                    Cache::mozgClearCacheFile('user_' . $user_id . '/position_photos_album_' . $check_album['album_id']);
                    //Если нет, то вызываем функцию генерации
                    if (!$check_pos) {
                        GenerateAlbumPhotosPosition($uid, $check_album['album_id']);
                        $check_pos = Cache::mozgCache('user_' . $uid . '/position_photos_album_' . $check_album['album_id']);
                    }
                    $position = xfieldsdataload($check_pos);
                } else {
                    $check_pos = null;
                    $position = null;
                }


                $row = $db->super_query("SELECT tb1.id, photo_name, comm_num, descr, date, position, rating_num, rating_all, rating_max, tb2.user_id, user_search_pref, user_country_city_name FROM `photos` tb1, `users` tb2 WHERE id = '{$photo_id}' AND tb1.user_id = tb2.user_id");
                if ($row) {
                    //Вывод названия альбома, приватноть из БД
                    $info_album = $db->super_query("SELECT name, privacy FROM `albums` WHERE aid = '{$check_album['album_id']}'");
                    $album_privacy = explode('|', $info_album['privacy']);
                    //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                    if ($user_info['user_id'] !== $row['user_id']) {
                        $check_friend = CheckFriends($row['user_id']);
                    } else {
                        $check_friend = null;
                    }
                    //Приватность
                    if ($album_privacy[0] == 1 || ($album_privacy[0] == 2 && $check_friend) || $user_info['user_id'] == $row['user_id']) {
                        //Если фотография вызвана не со стены
                        if (!$fuser) {
                            $exp_photo_num = count(explode('||', $check_pos));
                            $row_album['photo_num'] = $exp_photo_num - 1;
                        } else {
                            $row_album = null;
                        }


                        //Выводим комментарии если они есть
                        if ($row['comm_num'] > 0) {
                            $tpl->load_template('photo_comment.tpl');
                            if ($row['comm_num'] > 7) {
                                $limit_comm = $row['comm_num'] - 3;
                            } else {
                                $limit_comm = 0;
                            }
                            $sql_comm = $db->super_query("SELECT tb1.user_id,text,date,id,hash, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `photos_comments` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND tb1.pid = '{$photo_id}' ORDER by `date` ASC LIMIT {$limit_comm}, {$row['comm_num']}", true);
                            foreach ($sql_comm as $row_comm) {
                                $tpl->set('{comment}', stripslashes($row_comm['text']));
                                $tpl->set('{uid}', $row_comm['user_id']);
                                $tpl->set('{id}', $row_comm['id']);
                                $tpl->set('{hash}', $row_comm['hash']);
                                $tpl->set('{author}', $row_comm['user_search_pref']);
                                if ($row_comm['user_photo']) {
                                    $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row_comm['user_id'] . '/50_' . $row_comm['user_photo']);
                                } else {
                                    $tpl->set('{ava}', '/images/no_ava_50.png');
                                }
                                OnlineTpl($row_comm['user_last_visit'], $row_comm['user_logged_mobile']);
                                $date_str = megaDate(strtotime($row_comm['date']));
                                $tpl->set('{date}', $date_str);
                                if ($row_comm['user_id'] == $user_info['user_id'] || $row['user_id'] == $user_info['user_id']) {
                                    $tpl->set('[owner]', '');
                                    $tpl->set('[/owner]', '');
                                } else {
                                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                                }
                                $tpl->compile('comments');
                            }
                        }
                        //Сама фотография
                        $tpl->load_template('photo_view.tpl');
                        $tpl->set('{photo}', $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/albums/' . $check_album['album_id'] . '/' . $row['photo_name'] . '?' . $server_time);
                        $sizephoto = getimagesize(ROOT_DIR . '/uploads/users/' . $row['user_id'] . '/albums/' . $check_album['album_id'] . '/' . $row['photo_name']);
                        $tpl->set('{height}', $sizephoto[1]);
                        $tpl->set('{descr}', stripslashes($row['descr']));
                        $tpl->set('{photo-num}', $row_album['photo_num']);
                        $tpl->set('{id}', $row['id']);
                        $tpl->set('{aid}', $check_album['album_id']);
                        $tpl->set('{album-name}', stripslashes($info_album['name']));
                        $tpl->set('{uid}', $row['user_id']);
                        //Составляем адрес строки который будет после закрытия и опридиляем секцию
                        if ($section == 'all_comments') {
                            $tpl->set('{close-link}', '/albums/comments/' . $row['user_id']);
                            $tpl->set('{section}', '_sec=all_comments');
                        } elseif ($section == 'album_comments') {
                            $tpl->set('{close-link}', '/albums/view/' . $check_album['album_id'] . '/comments/');
                            $tpl->set('{section}', '_' . $check_album['album_id'] . '_sec=album_comments');
                        } elseif ($section == 'user_page') {
                            $tpl->set('{close-link}', '/u' . $row['user_id']);
                            $tpl->set('{section}', '_sec=user_page');
                        } elseif ($section == 'wall') {
                            $tpl->set('{close-link}', '/u' . $fuser);
                            $tpl->set('{section}', '_sec=wall/fuser=' . $fuser);
                        } elseif ($section == 'notes') {
                            $tpl->set('{close-link}', '/notes/view/' . $fuser);
                            $tpl->set('{section}', '_sec=notes/id=' . $fuser);
                        } elseif ($section == 'loaded') {
                            $tpl->set('{close-link}', '/albums/add/' . $check_album['album_id']);
                            $tpl->set('{section}', '_sec=loaded');
                        } elseif ($section == 'news') {
                            $fuser = 1;
                            $tpl->set('{close-link}', '/news');
                            $tpl->set('{section}', '_sec=news');
                        } elseif ($section == 'msg') {
                            $tpl->set('{close-link}', '/messages/show/' . $fuser);
                            $tpl->set('{section}', '_sec=msg');
                        } elseif ($section == 'newphotos') {
                            $tpl->set('{close-link}', '/albums/newphotos');
                            $tpl->set('{section}', '_' . $check_album['album_id'] . '_sec=newphotos');
                        } else {
                            $tpl->set('{close-link}', '/albums/view/' . $check_album['album_id']);
                            $tpl->set('{section}', '_' . $check_album['album_id']);
                        }
                        if (!$fuser) {
                            $tpl->set('[all]', '');
                            $tpl->set('[/all]', '');
                            $tpl->set_block("'\\[wall\\](.*?)\\[/wall\\]'si", "");
                        } else {
                            $tpl->set('[wall]', '');
                            $tpl->set('[/wall]', '');
                            $tpl->set_block("'\\[all\\](.*?)\\[/all\\]'si", "");
                        }
                        $tpl->set('{jid}', $row['position']);
                        $tpl->set('{comm_num}', ($row['comm_num'] - 3) . ' ' . declWord(($row['comm_num'] - 3), 'comments'));
                        $tpl->set('{num}', $row['comm_num']);
                        $tpl->set('{author}', $row['user_search_pref']);

                        if ($row['user_country_city_name'] == '' || $row['user_country_city_name'] = ' |') {
                            $tpl->set('{author-info}', '');
                        } else {
                            $author_info = explode('|', $row['user_country_city_name']);
                            if ($author_info[0]) {
                                $tpl->set('{author-info}', $author_info[0]);
                            } else {
                                $tpl->set('{author-info}', '');
                            }
                            if ($author_info[1]) {
                                $tpl->set('{author-info}', $author_info[0] . ', ' . $author_info[1] . '<br />');
                            }
                        }

                        $date_str = megaDate(strtotime($row['date']), 1, 1);
                        $tpl->set('{date}', $date_str);
                        if ($uid == $user_info['user_id']) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                            $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                        } else {
                            $tpl->set('[not-owner]', '');
                            $tpl->set('[/not-owner]', '');
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }
                        $tpl->set('{comments}', $tpl->result['comments'] ?? '');
                        //Показываем стрелочки если фотографий больше одной и фотография вызвана не со стены
                        if ($row_album['photo_num'] > 1 && !$fuser) {
                            //Если фотография вызвана из альбома "все фотографии" или вызвана со страницы юзера
                            if ($row['position'] == $row_album['photo_num']) {
                                $next_photo = $position[1] ?? null;
                            } else {
                                $next_photo = $position[($row['position'] + 1)];
                            }
                            if ($row['position'] == 1) {
                                $prev_photo = $position[($row['position'] + $row_album['photo_num'] - 1)];
                            } else {
                                $prev_photo = $position[($row['position'] - 1)];
                            }
                            $tpl->set('{next-id}', $next_photo);
                            $tpl->set('{prev-id}', $prev_photo);
                        } else {
                            $tpl->set('{next-id}', $row['id']);
                            $tpl->set('{prev-id}', $row['id']);
                        }
                        if ($row['comm_num'] < 8) {
                            $tpl->set_block("'\\[all-comm\\](.*?)\\[/all-comm\\]'si", "");
                        } else {
                            $tpl->set('[all-comm]', '');
                            $tpl->set('[/all-comm]', '');
                        }
                        //Приватность комментариев
                        if ($album_privacy[1] == 1 || ($album_privacy[1] == 2 && $check_friend) || $user_info['user_id'] == $row['user_id']) {
                            $tpl->set('[add-comm]', '');
                            $tpl->set('[/add-comm]', '');
                        } else {
                            $tpl->set_block("'\\[add-comm\\](.*?)\\[/add-comm\\]'si", "");
                        }
                        //Выводим отмеченных людей на фото если они есть
                        /** fixme limit */
                        $sql_mark = $db->super_query("SELECT muser_id, mphoto_name, msettings_pos, mmark_user_id, mapprove FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' ORDER by `mdate` ASC", true);
                        $mark_peoples = $mark_peoples ?? '';//TODO update

                        if ($sql_mark) {
                            $cnt_mark = 0;
                            $mark_peoples .= '<div class="fl_l" id="peopleOnPhotoText' . $photo_id . '" style="margin-right:5px">На этой фотографии:</div>';
                            foreach ($sql_mark as $row_mark) {
                                $cnt_mark++;
                                if ($cnt_mark != 1) {
                                    $comma = ', ';
                                } else {
                                    $comma = '';
                                }
                                if ($row_mark['muser_id'] && $row_mark['mphoto_name'] == '') {
                                    if ($row['user_id'] == $user_info['user_id'] || $user_info['user_id'] == $row_mark['muser_id'] || $user_info['user_id'] == $row_mark['mmark_user_id']) {
                                        $del_mark_link = '<div class="fl_l"><img src="/images/hide_lef.gif" class="distin_del_user" title="Удалить отметку" onclick="Distinguish.DeletUser(' . $row_mark['muser_id'] . ', ' . $photo_id . ')"/></div>';
                                    } else {
                                        $del_mark_link = '';
                                    }
                                    $row_user = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $row_mark['muser_id'] . "'");
                                    if ($row_mark['mapprove'] || $row['user_id'] == $user_info['user_id'] || $user_info['user_id'] == $row_mark['mmark_user_id'] || $row_mark['muser_id'] == $user_info['user_id']) {
                                        $user_link = '<a href="/u' . $row_mark['muser_id'] . '" id="selected_us_' . $row_mark['muser_id'] . $photo_id . '" onclick="Page.Go(this.href); return false" onmouseover="Distinguish.ShowTag(' . $row_mark['msettings_pos'] . ', ' . $photo_id . ')" onmouseout="Distinguish.HideTag(' . $photo_id . ')" class="one_dis_user' . $photo_id . '">';
                                        $user_link_end = '</a>';
                                    } else {
                                        $user_link = '<span style="color:#000" id="selected_us_' . $row_mark['muser_id'] . $photo_id . '" onmouseover="Distinguish.ShowTag(' . $row_mark['msettings_pos'] . ', ' . $photo_id . ')" onmouseout="Distinguish.HideTag(' . $photo_id . ')" class="one_dis_user' . $photo_id . '">';
                                        $user_link_end = '</span>';
                                    }
                                    $mark_peoples .= '<span id="selectedDivIser' . $row_mark['muser_id'] . $photo_id . '"><div class="fl_l" style="margin-right:4px">' . $comma . '</div><div class="fl_l"> ' . $user_link . $row_user['user_search_pref'] . $user_link_end . '</div>' . $del_mark_link . '</span>';
                                } else {
                                    if ($row['user_id'] == $user_info['user_id'] || $user_info['user_id'] == $row_mark['mmark_user_id']) {
                                        $del_mark_link = '<div class="fl_l"><img src="/images/hide_lef.gif" class="distin_del_user" title="Удалить отметку" onclick="Distinguish.DeletUser(' . $row_mark['muser_id'] . ', ' . $photo_id . ', \'' . $row_mark['mphoto_name'] . '\')"/></div>';
                                    } else {
                                        $del_mark_link = '';
                                    }
                                    $mark_peoples .= '<span id="selectedDivIser' . $row_mark['muser_id'] . $photo_id . '"><div class="fl_l" style="margin-right:4px">' . $comma . '</div><div class="fl_l"><span style="color:#000" id="selected_us_' . $row_mark['muser_id'] . $photo_id . '" onmouseover="Distinguish.ShowTag(' . $row_mark['msettings_pos'] . ', ' . $photo_id . ')" onmouseout="Distinguish.HideTag(' . $photo_id . ')" class="one_dis_user' . $photo_id . '">' . $row_mark['mphoto_name'] . '</span></div>' . $del_mark_link . '</span>';
                                }
                                //Если человек отмечен но не потвердил
                                if (!$row_mark['mapprove'] AND $row_mark['muser_id'] == $user_info['user_id']) {
                                    $row_mmark_user_id = $db->super_query("SELECT user_search_pref, user_sex FROM `users` WHERE user_id = '" . $row_mark['mmark_user_id'] . "'");
                                    if ($row_mmark_user_id['user_sex'] == 1) {
                                        $approve_mark_gram_text = 'отметил';
                                    } else {
                                        $approve_mark_gram_text = 'отметила';
                                    }
                                    $approve_mark = $row_mmark_user_id['user_search_pref'];
                                    $approve_mark_user_id = $row_mark['mmark_user_id'];
                                    $approve_mark_del_link = 'Distinguish.DeletUser(' . $row_mark['muser_id'] . ', ' . $photo_id . ', \'' . $row_mark['mphoto_name'] . '\')';
                                } else {
                                    $approve_mark = '';
                                    $approve_mark_gram_text = '';
                                    $approve_mark_user_id = '';
                                    $approve_mark_del_link = '';
                                }
                            }
                        } else {
                            $approve_mark = '';
                            $approve_mark_gram_text = '';
                            $approve_mark_user_id = '';
                            $approve_mark_del_link = '';
                        }
                        $tpl->set('{mark-peoples}', $mark_peoples);
                        if ($approve_mark) {
                            $tpl->set('{mark-user-name}', $approve_mark);
                            $tpl->set('{mark-gram-text}', $approve_mark_gram_text);
                            $tpl->set('{mark-user-id}', $approve_mark_user_id);
                            $tpl->set('{mark-del-link}', $approve_mark_del_link);
                            $tpl->set('[mark-block]', '');
                            $tpl->set('[/mark-block]', '');
                        } else {
                            $tpl->set_block("'\\[mark-block\\](.*?)\\[/mark-block\\]'si", "");
                        }
                        //Проверка ставил человек на это фото уже оценку или нет
                        $check = $db->super_query("SELECT rating FROM `photos_rating` WHERE user_id = '{$user_info['user_id']}' AND photo_id = '{$photo_id}'");
                        if (!empty($check['rating'])) {
                            $tpl->set('{rate-check}', 'no_display');
                            $tpl->set('{rate-check-2}', '');
                            if ($check['rating'] == 1) {
                                $tpl->set('{ok-rate}', '<div class="rating rating3" style="background:url(\'/images/rating3.png\')">' . $check['rating'] . '</div>');
                            } else if ($check['rating'] == 6) {
                                $tpl->set('{ok-rate}', '<div class="rating rating3"  style="background:url(\'/images/rating2.png\')">5+</div>');
                            } else {
                                $tpl->set('{ok-rate}', '<div class="rating rating3">' . $check['rating'] . '</div>');
                            }
                        } else {
                            $tpl->set('{rate-check-2}', 'no_display');
                            $tpl->set('{rate-check}', '');
                        }
                        $tpl->set('{rate-all}', $row['rating_all']);
                        $tpl->set('{rate-max}', $row['rating_max']);
                        $rate = $row['rating_all'] ? $row['rating_num'] / $row['rating_all'] : 0;
                        $rate = round($rate, 2);
                        if ($rate > 5) {
                            $rate = '5+';
                        }
                        $tpl->set('{rate}', $rate);
                        $tpl->compile('content');
                        AjaxTpl($tpl);
                        if ($config['gzip'] == 'yes') {
                            (new Gzip(false))->GzipOut();
                        }
                    } else {
                        echo 'err_privacy';
                    }
                } else {
                    echo 'no_photo';
                }
            } else {
                echo 'err_privacy';
            }
    }
//        $tpl->clear();
//        $db->free();
} else {
    echo 'no_photo';
}