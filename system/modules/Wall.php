<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use FluffyDollop\Http\Request;
use Mozg\classes\Cache;
use Mozg\classes\Flood;

class Wall extends \Mozg\classes\Module
{
    public function sendRecord()
    {
        $lang = $this->lang;
        $logged = $this->logged;
        $db = $this->db;
        $user_info = $this->user_info;

//        $wall = new WallProfile($tpl);
//			NoAjaxQuery();
        $wall_text = (new Request)->filter('wall_text');
        if (Flood::check('identical', $wall_text)) {
            echo 'err_privacy';
        } else {
            $attach_files = (new Request)->filter('attach_files', 25000, true);
            $for_user_id = (new Request)->int('for_user_id');
            $fast_comm_id = (new Request)->int('rid');
            $answer_comm_id = (new Request)->int('answer_comm_id');
            $str_date = time();

            $spam_action = (!$fast_comm_id) ? 'wall' : 'comments';
            if (Flood::check($spam_action)) {
                echo 'err_privacy';
            } else {
                //Проверка на наличие юзера, которому отправляется запись
                $check = $db->super_query("SELECT user_privacy, user_last_visit FROM `users` WHERE user_id = '{$for_user_id}'");

                if ($check) {
                    if (!empty($wall_text) || !empty($attach_files)) {
                        //Приватность
                        $user_privacy = xfieldsdataload($check['user_privacy']);

                        //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                        if ($user_privacy['val_wall2'] == 2 || $user_privacy['val_wall1'] == 2 || ($user_privacy['val_wall3'] == 2 && $user_id !== $for_user_id))
                            $check_friend = CheckFriends($for_user_id);
                        else {
                            $check_friend = null;
                        }

                        if (!$fast_comm_id) {
                            if ($user_privacy['val_wall2'] == 1 || ($user_privacy['val_wall2'] == 2 && $check_friend) || $user_id == $for_user_id)
                                $xPrivasy = 1;
                            else
                                $xPrivasy = 0;
                        } else {
                            if ($user_privacy['val_wall3'] == 1 || ($user_privacy['val_wall3'] == 2 && $check_friend) || $user_id == $for_user_id)
                                $xPrivasy = 1;
                            else
                                $xPrivasy = 0;
                        }

                        if ($user_privacy['val_wall1'] == 1 || ($user_privacy['val_wall1'] == 2 && $check_friend) || $user_id == $for_user_id) {
                            $xPrivasyX = 1;
                        } else {
                            $xPrivasyX = 0;
                        }

                        //ЧС
                        $CheckBlackList = CheckBlackList($for_user_id);
                        if (!$CheckBlackList && $xPrivasy) {

                            //Определение изображения к ссылке
                            if (stripos($attach_files, 'link|') !== false) {
                                $attach_arr = explode('||', $attach_files);
                                $cnt_attach_link = 1;
                                foreach ($attach_arr as $attach_file) {
                                    $attach_type = explode('|', $attach_file);
                                    if ($attach_type[0] === 'link' && preg_match('/https:\/\/(.*?)+$/i', $attach_type[1]) && $cnt_attach_link == 1) {
                                        $domain_url_name = explode('/', $attach_type[1]);
                                        $rdomain_url_name = str_replace('https://', '', $domain_url_name[2]);
                                        $rImgUrl = $attach_type[4];
                                        $rImgUrl = str_replace("\\", "/", $rImgUrl);
                                        $img_name_arr = explode(".", $rImgUrl);
                                        $img_format = to_translit(end($img_name_arr));
                                        $image_name = substr(md5(time() . md5($rImgUrl)), 0, 15);

                                        //Разрешенные форматы
                                        $allowed_files = array('jpg', 'jpeg', 'jpe', 'png');

                                        //Загружаем картинку на сайт
                                        if (in_array(strtolower($img_format), $allowed_files) && preg_match("/https:\/\/(.*?)(.jpg|.png|.jpeg|.jpe)/i", $rImgUrl)) {

                                            //Директория загрузки фото
                                            $upload_dir = ROOT_DIR . '/uploads/attach/' . $user_id;

                                            //Если нет папки юзера, то создаём её
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

                                $attach_files = $attach_files . "vote|{$db->insert_id()}||";

                            }

                            //Если добавляется ответ на комментарий, то вносим в ленту новостей "ответы"
                            if ($answer_comm_id) {

                                //Выводим ид владельца комментария
                                $row_owner2 = $db->super_query("SELECT author_user_id FROM `wall` WHERE id = '{$answer_comm_id}' AND fast_comm_id != '0'");

                                //Проверка на то, что юзер не отвечает сам себе
                                if ($user_id !== $row_owner2['author_user_id'] && $row_owner2) {

                                    $check2 = $db->super_query("SELECT user_last_visit, user_name FROM `users` WHERE user_id = '{$row_owner2['author_user_id']}'");

                                    $wall_text = str_replace($check2['user_name'], "<a href=\"/u{$row_owner2['author_user_id']}\" onClick=\"Page.Go(this.href); return false\" class=\"newcolor000\">{$check2['user_name']}</a>", $wall_text);

                                    //Вставляем в ленту новостей
                                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 6, action_text = '{$wall_text}', obj_id = '{$answer_comm_id}', for_user_id = '{$row_owner2['author_user_id']}', action_time = '{$server_time}'");

                                    //Вставляем событие в моментальные оповещения
                                    $update_time = $server_time - 70;

                                    if ($check2['user_last_visit'] >= $update_time) {

                                        $db->query("INSERT INTO `updates` SET for_user_id = '{$row_owner2['author_user_id']}', from_user_id = '{$user_id}', type = '5', date = '{$server_time}', text = '{$wall_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/wall{$for_user_id}_{$fast_comm_id}'");

                                        Cache::mozgCreateCache("user_{$row_owner2['author_user_id']}/updates", 1);

                                        //ИНАЧЕ Добавляем +1 юзеру для оповещения
                                    } else {

                                        $cntCacheNews = Cache::mozgCache("user_{$row_owner2['author_user_id']}/new_news");
                                        Cache::mozgCreateCache("user_{$row_owner2['author_user_id']}/new_news", ($cntCacheNews + 1));

                                    }

                                }

                            }

                            //Вставляем саму запись в БД
                            $db->query("INSERT INTO `wall` SET author_user_id = '{$user_id}', for_user_id = '{$for_user_id}', text = '{$wall_text}', add_date = '{$str_date}', fast_comm_id = '{$fast_comm_id}', attach = '" . $attach_files . "'");
                            $db_id = $db->insert_id();

                            //Если пользователь пишет сам у себя на стене, то вносим это в "Мои Новости"
                            if ($user_id == $for_user_id && !$fast_comm_id) {
                                $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 1, action_text = '{$wall_text}', obj_id = '{$db_id}', action_time = '{$str_date}'");
                            }

                            //Если добавляется комментарий к записи, то вносим в ленту новостей "ответы"
                            if ($fast_comm_id && !$answer_comm_id) {
                                //Выводим ид владельца записи
                                $row_owner = $db->super_query("SELECT author_user_id FROM `wall` WHERE id = '{$fast_comm_id}'");

                                if ($user_id !== $row_owner['author_user_id'] && $row_owner) {
                                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 6, action_text = '{$wall_text}', obj_id = '{$fast_comm_id}', for_user_id = '{$row_owner['author_user_id']}', action_time = '{$str_date}'");

                                    //Вставляем событие в моментальные оповещения
                                    $update_time = time() - 70;

                                    if ($check['user_last_visit'] >= $update_time) {

                                        $db->query("INSERT INTO `updates` SET for_user_id = '{$row_owner['author_user_id']}', from_user_id = '{$user_id}', type = '1', date = '{$server_time}', text = '{$wall_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/wall{$for_user_id}_{$fast_comm_id}'");

                                        Cache::mozgCreateCache("user_{$row_owner['author_user_id']}/updates", 1);

                                        //ИНАЧЕ Добавляем +1 юзеру для оповещения
                                    } else {

                                        $cntCacheNews = Cache::mozgCache('user_' . $row_owner['author_user_id'] . '/new_news');
                                        Cache::mozgCreateCache('user_' . $row_owner['author_user_id'] . '/new_news', ($cntCacheNews + 1));

                                    }

                                    $config = settings_get();
                                    //Отправка уведомления на E-mail
                                    if ($config['news_mail_2'] === 'yes') {
                                        $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $row_owner['author_user_id'] . "'");
                                        if ($rowUserEmail['user_email']) {
//                                            $mail = new ViiMail($config);
//                                            $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
//                                            $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '2'");
//                                            $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
//                                            $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
//                                            $rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'] . 'wall' . $row_owner['author_user_id'] . '_' . $fast_comm_id, $rowEmailTpl['text']);
//                                            $mail->send($rowUserEmail['user_email'], 'Ответ на запись', $rowEmailTpl['text']);
                                        }
                                    }
                                }
                            }

                            if ($fast_comm_id) {
                                $db->query("UPDATE `wall` SET fasts_num = fasts_num+1 WHERE id = '{$fast_comm_id}'");
                            } else {
                                $db->query("UPDATE `users` SET user_wall_num = user_wall_num+1 WHERE user_id = '{$for_user_id}'");
                            }

                            //Если добавлена просто запись, то сразу обновляем все записи на стене
                            Flood::LogInsert('wall');
                            Flood::LogInsert('identical', $wall_text);

                            if (!$fast_comm_id) {
                                $config = settings_get();
                                if ($xPrivasyX) {
//                                    $wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, type, tell_uid, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `wall` tb1, `users` tb2 WHERE for_user_id = '{$for_user_id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '0' ORDER by `add_date` DESC LIMIT 0, {$limit_select}");
//                                    $wall->template('wall/record.tpl');
//                                    $wall->compile('content');
//                                    $id = $id ?? false;
//                                    $wall->select($config, $id, $for_user_id, $user_privacy, $check_friend, $user_info);
                                    echo 'error';
                                }

                                Cache::mozgClearCacheFile('user_' . $for_user_id . '/profile_' . $for_user_id);

                                //Отправка уведомления на E-mail
                                if ($config['news_mail_7'] === 'yes' && $user_id !== $for_user_id) {
                                    $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $for_user_id . "'");
                                    if ($rowUserEmail['user_email']) {
//                                            include_once ENGINE_DIR . '/classes/mail.php';
//                                            $mail = new \Mozg\classes\ViiMail($config);
//                                            $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
//                                            $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '7'");
//                                            $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
//                                            $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
//                                            $rowEmailTpl['text'] = str_replace('{%rec-link%}', $config['home_url'] . 'wall' . $for_user_id . '_' . $dbid, $rowEmailTpl['text']);
//                                            $mail->send($rowUserEmail['user_email'], 'Новая запись на стене', $rowEmailTpl['text']);
                                    }
                                }

                                //Если добавлен комментарий к записи, то просто обновляем нужную часть, то есть только часть комментариев, но не всю стену
                            } else {

                                Flood::LogInsert('comments');
                                Flood::LogInsert('identical', $wall_text);

                                //Выводим кол-во комментов к записи
                                $row = $db->super_query("SELECT fasts_num FROM `wall` WHERE id = '{$fast_comm_id}'");
                                $record_fasts_num = $row['fasts_num'];
                                if ($record_fasts_num > 3) {
                                    $limit_comm_num = $row['fasts_num'] - 3;
                                } else {
                                    $limit_comm_num = 0;
                                }

//                                $wall->comm_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, tb2.user_photo, user_search_pref, user_last_visit FROM `wall` tb1, `users` tb2 WHERE tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = '{$fast_comm_id}' ORDER by `add_date` ASC LIMIT {$limit_comm_num}, 3");

                                if ((new Request)->int('type') === 1) {
//                                    $wall->comm_template('news/news.tpl');
                                } else if ((new Request)->int('type') === 2) {
//                                    $wall->comm_template('wall/one_record.tpl');
                                } else {
//                                    $wall->comm_template('wall/record.tpl');
                                }

//                                $wall->comm_compile('content');
//                                $wall->comm_select();
                            }

//                                AjaxTpl($tpl);

                        } else {
                            echo 'err_privacy';
                        }
                    }
                }
            }
        }
    }
}