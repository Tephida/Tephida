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
use Mozg\classes\Cache;
use Mozg\classes\Flood;

//Если страница вызвана через AJAX, то включаем защиту, чтоб не могли обращаться напрямую к странице
NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $metatags['title'] = $lang['friends'] ?? '';
    $user_info = $user_info ?? Registry::get('user_info');
    $server_time = Registry::get('server_time');
    $page = (new Request)->int('page', 1);
    $db = Registry::get('db');
    $gcount = 20;
    $limit_page = ($page - 1) * $gcount;

    $params = array();
    switch ($act) {

        //################### Отправка заявки в друзья ###################//
        case "send_demand":
            NoAjaxQuery();

            if (Flood::check('friends')) {
                echo 'yes_demand';//fixme
            } else {
                $for_user_id = (new Request)->int('for_user_id');
                $from_user_id = $user_info['user_id'];

                //Проверяем на факт сушествования заявки для пользователя, если она уже есть, то даёт ответ "yes_demand"
                $check = $db->super_query("SELECT for_user_id FROM `friends_demands` WHERE for_user_id = '{$for_user_id}' AND from_user_id = '{$from_user_id}'");

                if ($for_user_id && !$check && $for_user_id !== $from_user_id) {

                    //Проверяем существования заявки у себя в заявках
                    $check_demands = $db->super_query("SELECT for_user_id FROM `friends_demands` WHERE for_user_id = '{$from_user_id}' AND from_user_id = '{$for_user_id}'");
                    if (!$check_demands) {

                        //Проверяем нет ли этого юзера уже в списке друзей
                        $check_friendlist = $db->super_query("SELECT user_id FROM `friends` WHERE friend_id = '{$for_user_id}' AND user_id = '{$from_user_id}' AND subscriptions = 0");
                        if (!$check_friendlist) {
                            $db->query("INSERT INTO `friends_demands` (for_user_id, from_user_id, demand_date) VALUES ('{$for_user_id}', '{$from_user_id}', NOW())");
                            Flood::LogInsert('friends');
                            $db->query("UPDATE `users` SET user_friends_demands = user_friends_demands+1 WHERE user_id = '{$for_user_id}'");
                            echo 'ok';

                            //Вставляем событие в моментальные оповещания
                            $row_owner = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$for_user_id}'");
                            $update_time = $server_time - 70;

                            if ($row_owner['user_last_visit'] >= $update_time) {

                                $action_update_text = 'хочет добавить Вас в друзья.';

                                $db->query("INSERT INTO `updates` SET for_user_id = '{$for_user_id}', from_user_id = '{$user_info['user_id']}', type = '11', date = '{$server_time}', text = '{$action_update_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/friends/requests'");

                                Cache::mozgCreateCache("user_{$for_user_id}/updates", 1);

                            }
                            $config = settings_get();
                            //Отправка уведомления на E-mail
                            if ($config['news_mail_1'] == 'yes') {
                                $rowUserEmail = $db->super_query("SELECT user_name, user_email FROM `users` WHERE user_id = '" . $for_user_id . "'");
                                if ($rowUserEmail['user_email']) {
                                    $mail = new \FluffyDollop\Support\ViiMail($config);
                                    $rowMyInfo = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");
                                    $rowEmailTpl = $db->super_query("SELECT text FROM `mail_tpl` WHERE id = '1'");
                                    $rowEmailTpl['text'] = str_replace('{%user%}', $rowUserEmail['user_name'], $rowEmailTpl['text']);
                                    $rowEmailTpl['text'] = str_replace('{%user-friend%}', $rowMyInfo['user_search_pref'], $rowEmailTpl['text']);
                                    $mail->send($rowUserEmail['user_email'], 'Новая заявка в друзья', $rowEmailTpl['text']);
                                }
                            }
                        } else {
                            echo 'yes_friend';
                        }
                    } else {
                        echo 'yes_demand2';
                    }
                } else {
                    echo 'yes_demand';
                }
            }
            break;

        //################### Принятие заявки на дружбу ###################//
        case "take":
            NoAjaxQuery();
            $take_user_id = (new Request)->int('take_user_id');
            $user_id = $user_info['user_id'];

            //Проверяем на существования юзера в таблице заявок в друзья
            $check = $db->super_query("SELECT for_user_id FROM `friends_demands` WHERE for_user_id = '{$user_id}' AND from_user_id = '{$take_user_id}'");

            if ($check) {

                //Добавляем юзера который кинул заявку в список друзей
                $db->query("INSERT INTO `friends` SET user_id = '{$user_id}', friend_id = '{$take_user_id}', friends_date = NOW()");

                //Тому кто предлогал дружбу, добавляем ему в друзья себя
                $db->query("INSERT INTO `friends` SET user_id = '{$take_user_id}', friend_id = '{$user_id}', friends_date = NOW()");

                //Обновляем кол-во заявок и кол-друзей у юзера
                $db->query("UPDATE `users` SET user_friends_demands = user_friends_demands-1, user_friends_num = user_friends_num+1 WHERE user_id = '{$user_id}'");

                //Тому кто предлогал дружбу, обновляем кол-друзей
                $db->query("UPDATE `users` SET user_friends_num = user_friends_num+1 WHERE user_id = '{$take_user_id}'");

                //Удаляем заявку из таблицы заявок
                $db->query("DELETE FROM `friends_demands` WHERE for_user_id = '{$user_id}' AND from_user_id = '{$take_user_id}'");

                $generateLastTime = $server_time - 10800;

                //Добавляем действия в ленту новостей кто подавал заявку
                $rowX = $db->super_query("SELECT ac_id, action_text FROM `news` WHERE action_time > '{$generateLastTime}' AND action_type = 4 AND ac_user_id = '{$take_user_id}'");
                if ($rowX['ac_id']) {
                    if (!preg_match("/{$rowX['action_text']}/i", $user_id)) {
                        $db->query("UPDATE `news` SET action_text = '{$rowX['action_text']}||{$user_id}', action_time = '{$server_time}' WHERE ac_id = '{$rowX['ac_id']}'");
                    } else {
                        echo '';
                    }
                } else {
                    $db->query("INSERT INTO `news` SET ac_user_id = '{$take_user_id}', action_type = 4, action_text = '{$user_id}', action_time = '{$server_time}'");
                }

                //Вставляем событие в моментальные оповещения
                $row_owner = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$take_user_id}'");
                $update_time = $server_time - 70;

                if ($row_owner['user_last_visit'] >= $update_time) {

                    $myRow = $db->super_query("SELECT user_sex FROM `users` WHERE user_id = '{$user_info['user_id']}'");
                    if ($myRow['user_sex'] == 2) {
                        $action_update_text = 'подтвердила Вашу заявку на дружбу.';
                    } else {
                        $action_update_text = 'подтвердил Вашу заявку на дружбу.';
                    }

                    $db->query("INSERT INTO `updates` SET for_user_id = '{$take_user_id}', from_user_id = '{$user_info['user_id']}', type = '12', date = '{$server_time}', text = '{$action_update_text}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '/u{$take_user_id}'");

                    Cache::mozgCreateCache("user_{$take_user_id}/updates", 1);

                }

                //Добавляем действия в ленту новостей себе
                $row = $db->super_query("SELECT ac_id, action_text FROM `news` WHERE action_time > '{$generateLastTime}' AND action_type = 4 AND ac_user_id = '{$user_id}'");
                if ($row) {
                    if (!preg_match("/{$row['action_text']}/i", $take_user_id)) {
                        $db->query("UPDATE `news` SET action_text = '{$row['action_text']}||{$take_user_id}', action_time = '{$server_time}' WHERE ac_id = '{$row['ac_id']}'");
                    } else {
                        echo '';
                    }
                } else {
                    $db->query("INSERT INTO `news` SET ac_user_id = '{$user_id}', action_type = 4, action_text = '{$take_user_id}', action_time = '{$server_time}'");
                }

                //Чистим кеш владельцу стр и тому кого добавляем в др.
                Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                Cache::mozgClearCacheFile('user_' . $take_user_id . '/profile_' . $take_user_id);

                //Записываем пользователя в кеш файл друзей
                $openMyList = Cache::mozgCache("user_{$user_id}/friends");
                Cache::mozgCreateCache("user_{$user_id}/friends", $openMyList . "u{$take_user_id}|");

                $openTakeList = Cache::mozgCache("user_{$take_user_id}/friends");
                Cache::mozgCreateCache("user_{$take_user_id}/friends", $openTakeList . "u{$user_id}|");
            } else {
                echo 'no_request';
            }

            break;

        //################### Отклонение заявки на дружбу ###################//
        case "reject":
            NoAjaxQuery();
            $reject_user_id = (new Request)->int('reject_user_id');
            $user_id = $user_info['user_id'];

            //Проверяем на существования юзера в таблице заявок в друзья
            $check = $db->super_query("SELECT for_user_id FROM `friends_demands` WHERE for_user_id = '{$user_id}' AND from_user_id = '{$reject_user_id}'");
            if ($check) {
                //Обновляем кол-во заявок у юзера
                $db->query("UPDATE `users` SET user_friends_demands = user_friends_demands-1 WHERE user_id = '{$user_id}'");

                //Удаляем заявку из таблицы заявок
                $db->query("DELETE FROM `friends_demands` WHERE for_user_id = '{$user_id}' AND from_user_id = '{$reject_user_id}'");

            } else {
                echo 'no_request';
            }

            break;

        //################### Удаления друга из списка друзей ###################//
        case "delete":
            NoAjaxQuery();
            $delet_user_id = (new Request)->int('delet_user_id');
            $user_id = $user_info['user_id'];

            //Проверяем на существования юзера в списке друзей
            $check = $db->super_query("SELECT user_id FROM `friends` WHERE user_id = '{$user_id}' AND friend_id = '{$delet_user_id}' AND subscriptions = 0");
            if ($check) {
                //Удаляем друга из таблицы друзей
                $db->query("DELETE FROM `friends` WHERE user_id = '{$user_id}' AND friend_id = '{$delet_user_id}' AND subscriptions = 0");

                //Удаляем у друга из таблицы
                $db->query("DELETE FROM `friends` WHERE user_id = '{$delet_user_id}' AND friend_id = '{$user_id}' AND subscriptions = 0");

                //Обновляем кол-друзей у юзера
                $db->query("UPDATE `users` SET user_friends_num = user_friends_num-1 WHERE user_id = '{$user_id}'");

                //Обновляем у друга которого удаляем кол-во друзей
                $db->query("UPDATE `users` SET user_friends_num = user_friends_num-1 WHERE user_id = '{$delet_user_id}'");

                //Чистим кеш владельцу стр и тому кого удаляем из др.
                Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
                Cache::mozgClearCacheFile('user_' . $delet_user_id . '/profile_' . $delet_user_id);

                //Удаляем пользователя из кеша файл друзей
                $openMyList = Cache::mozgCache("user_{$user_id}/friends");
                Cache::mozgCreateCache("user_{$user_id}/friends", str_replace("u{$delet_user_id}|", "", $openMyList));

                $openTakeList = Cache::mozgCache("user_{$delet_user_id}/friends");
                Cache::mozgCreateCache("user_{$delet_user_id}/friends", str_replace("u{$user_id}|", "", $openTakeList));
            } else {
                echo 'no_friend';
            }

            break;

        //################### Страница заявок в друзья ###################//
        case "requests":
            $mobile_speedbar = 'Заявки в друзья';

            $user_id = $user_info['user_id'];

            if ($user_info['user_friends_demands']) {
                $user_speedbar = $user_info['user_friends_demands'] . ' ' . declWord($user_info['user_friends_demands'], 'friends_demands');
            } else {
                $user_speedbar = $lang['no_requests'];
            }

            //Верх
            $tpl->load_template('friends/head.tpl');
            $tpl->set('{user-id}', $user_id);
            if ($user_info['user_friends_demands']) {
                $tpl->set('{demands}', '(' . $user_info['user_friends_demands'] . ')');
            } else {
                $tpl->set('{demands}', '');
            }
            $tpl->set('[request-friends]', '');
            $tpl->set('[/request-friends]', '');
            $tpl->set_block("'\\[all-friends\\](.*?)\\[/all-friends\\]'si", "");
            $tpl->set_block("'\\[online-friends\\](.*?)\\[/online-friends\\]'si", "");
            $tpl->compile('info');

            //Выводим заявки в друзья если они есть
            if ($user_info['user_friends_demands']) {
                $sql_ = $db->super_query("SELECT tb1.from_user_id, demand_date, tb2.user_photo, user_search_pref, user_country_city_name, user_birthday FROM `friends_demands` tb1, `users` tb2 WHERE tb1.for_user_id = '{$user_id}' AND tb1.from_user_id = tb2.user_id ORDER by `demand_date` DESC LIMIT {$limit_page}, {$gcount}", true);
                $tpl->load_template('friends/request.tpl');
                foreach ($sql_ as $row) {
                    $user_country_city_name = explode('|', $row['user_country_city_name']);
                    $tpl->set('{country}', $user_country_city_name[0]);
                    $tpl->set('{city}', ', ' . $user_country_city_name[1]);
                    $tpl->set('{user-id}', $row['from_user_id']);
                    $tpl->set('{name}', $row['user_search_pref']);

                    $config = settings_get();
                    // FOR MOBILE VERSION 1.0
                    if ($config['temp'] == 'mobile') {
                        $avaPREFver = '50_';
                        $noAvaPrf = 'no_ava_50.png';
                    } else {
                        $avaPREFver = '100_';
                        $noAvaPrf = '100_no_ava.png';
                    }
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['from_user_id'] . '/' . $avaPREFver . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', "/images/{$noAvaPrf}");
                    }

                    //Возраст юзера
                    $user_birthday = explode('-', $row['user_birthday']);
                    $tpl->set('{age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]) ?? '');

                    $tpl->compile('content');
                }
                navigation($gcount, $user_info['user_friends_demands'], $config['home_url'] . 'friends/requests/page/');

            } else
                msgbox('', $lang['no_requests'], 'info_2');

            compile($tpl);
            break;

        //################### Просмотр всех онлайн друзей ###################//
        case "online":
            $mobile_speedbar = 'Друзья на сайте';

            $get_user_id = (new Request)->int('user_id');

            if (!$get_user_id) {
                $get_user_id = $user_info['user_id'];
            }

            //ЧС
            $CheckBlackList = CheckBlackList($get_user_id);
            if (!$CheckBlackList) {

                if ($get_user_id == $user_info['user_id']) {
                    $sql_order = "ORDER by `views`";
                } else {
                    $sql_order = "ORDER by `friends_date`";
                }
                $online_time = time() - $config['online_time'];
                $sql_ = $db->super_query("SELECT tb1.user_id, user_country_city_name, user_search_pref, user_birthday, user_photo, user_logged_mobile FROM `users` tb1, `friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$get_user_id}' AND tb1.user_last_visit >= '{$online_time}' AND tb2.subscriptions = 0 {$sql_order} DESC LIMIT {$limit_page}, {$gcount}", true);

                //Выводим имя юзера
                $friends_sql = $db->super_query("SELECT user_name, user_friends_num FROM `users` WHERE user_id = '{$get_user_id}'");
                if ($user_info['user_id'] != $get_user_id) {
                    $gram_name = grammaticalName($friends_sql['user_name']);
                } else {
                    $gram_name = 'Вас';
                }

                if ($sql_) //Кол-во друзей в онлайн
                {
                    $online_friends = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` tb1, `friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$get_user_id}' AND tb1.user_last_visit >= '{$online_time}' AND tb2.subscriptions = 0");
                } else {
                    $online_friends = null;
                }


                if (!empty($online_friends['cnt'])) {
                    $user_speedbar = 'У ' . $gram_name . ' ' . $online_friends['cnt'] . ' ' . declWord($online_friends['cnt'], 'friends_online');
                } else {
                    $user_speedbar = $lang['no_requests_online'];
                }

                //Верх
                $tpl->load_template('friends/head.tpl');
                if ($user_info['user_id'] != $get_user_id) {
                    $tpl->set('{name}', $gram_name);
                } else {
                    $tpl->set('{name}', '');
                }

                $tpl->set('{user-id}', $get_user_id);
                if ($get_user_id == $user_info['user_id']) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                    $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    if ($user_info['user_friends_demands']) {
                        $tpl->set('{demands}', '(' . $user_info['user_friends_demands'] . ')');
                    } else {
                        $tpl->set('{demands}', '');
                    }
                } else {
                    $tpl->set('[not-owner]', '');
                    $tpl->set('[/not-owner]', '');
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }
                $tpl->set('[online-friends]', '');
                $tpl->set('[/online-friends]', '');
                $tpl->set_block("'\\[request-friends\\](.*?)\\[/request-friends\\]'si", "");
                $tpl->set_block("'\\[all-friends\\](.*?)\\[/all-friends\\]'si", "");
                $tpl->compile('info');

                if ($sql_) {
                    $tpl->load_template('friends/friend.tpl');
                    foreach ($sql_ as $row) {
                        $user_country_city_name = explode('|', $row['user_country_city_name']);
                        $tpl->set('{country}', $user_country_city_name[0]);
                        if ($user_country_city_name[1]) {
                            $tpl->set('{city}', ', ' . $user_country_city_name[1]);
                        } else {
                            $tpl->set('{city}', '');
                        }
                        $tpl->set('{user-id}', $row['user_id']);
                        $tpl->set('{name}', $row['user_search_pref']);

                        $config = settings_get();
                        if ($row['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/100_' . $row['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/100_no_ava.png');
                        }

                        OnlineTpl($server_time, $row['user_logged_mobile']);

                        //Возраст юзера
                        $user_birthday = explode('-', $row['user_birthday']);
                        $tpl->set('{age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]));

                        if ($get_user_id == $user_info['user_id']) {
                            $tpl->set('[owner]', '');
                            $tpl->set('[/owner]', '');
                        } else {
                            $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                        }

                        if ($row['user_id'] == $user_info['user_id']) {
                            $tpl->set_block("'\\[viewer\\](.*?)\\[/viewer\\]'si", "");
                        } else {
                            $tpl->set('[viewer]', '');
                            $tpl->set('[/viewer]', '');
                        }

                        $tpl->compile('content');
                    }
                    navigation($gcount, $online_friends['cnt'], $config['home_url'] . 'friends/online/' . $get_user_id . '/page/');
                } else
                    msgbox('', $lang['no_requests_online'], 'info_2');
            } else {
                $user_speedbar = $lang['error'];
                msgbox('', $lang['no_notes'], 'info');
            }

            compile($tpl);
            break;

        //################### Загрузка друзей в окне для выбора СП ###################//
        case "box":
            NoAjaxQuery();

            $user_id = $user_info['user_id'];

            $page = (new Request)->int('page', 1);

            $gcount = 18;
            $limit_page = ($page - 1) * $gcount;

            if ((new Request)->int('user_sex') == 1) {
                $sql_usSex = 2;
            } elseif ((new Request)->int('user_sex') == 2) {
                $sql_usSex = 1;
            } else {
                $sql_usSex = false;
            }

            //Все друзья
            if ($sql_usSex) {
                $count = $db->super_query("SELECT COUNT(*) AS cnt FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 AND tb2.user_sex = '{$sql_usSex}'");

                if ($count['cnt']) {
                    $sql_ = $db->super_query("SELECT tb1.friend_id, tb2.user_photo, user_search_pref FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 AND tb2.user_sex = '{$sql_usSex}' ORDER by `views` DESC LIMIT {$limit_page}, {$gcount}", true);
                    $tpl->load_template('friends/box_friend.tpl');
                    foreach ($sql_ as $row) {
                        $tpl->set('{user-id}', $row['friend_id']);
                        $tpl->set('{name}', $row['user_search_pref']);

                        $config = settings_get();
                        if ($row['user_photo']) {
                            $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/50_' . $row['user_photo']);
                        } else {
                            $tpl->set('{ava}', '/images/100_no_ava.png');
                        }

                        $tpl->compile('content');
                    }
                    box_navigation($gcount, $count['cnt'], "''", 'sp.openfriends', '');
                } else {
                    msgbox('', '<div class="clear" style="margin-top:140px"></div>' . $lang['no_requests'], 'info_2');
                }
            } else {
                msgbox('', '<div class="clear" style="margin-top:140px"></div>' . $lang['no_requests'], 'info_2');
            }

            AjaxTpl($tpl);

            break;

        //################### Общие друзья ###################//
        case "common":

            $metatags['title'] = 'Общие друзья';
            $user_speedbar = 'Общие друзья';

            $uid = (new Request)->int('uid');

            //Выводим информацию о человеке, у которого смотрим общих друзей
            $owner = $db->super_query("SELECT user_friends_num, user_name FROM `users` WHERE user_id = '{$uid}'");

            //Если есть такой юзер и есть вообще друзья
            if ($owner and $owner['user_friends_num'] and $uid != $user_info['user_id']) {

                //Считаем кол-во общих дузей
                $count_common = $db->super_query("SELECT COUNT(*) AS cnt FROM `friends` tb1 INNER JOIN `friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$uid}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0");

                //Верх
                $tpl->load_template('friends/head_common.tpl');

                $tpl->set('{name}', grammaticalName($owner['user_name']));
                $tpl->set('{user-id}', $uid);

                if ($count_common['cnt']) {
                    $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
                } else {
                    $tpl->set('[no]', '');
                    $tpl->set('[/no]', '');
                }

                $tpl->compile('info');

                //Если есть на вывод
                if ($count_common['cnt']) {
                    $user_speedbar = $count_common['cnt'] . ' ' . declWord($count_common['cnt'], 'friends_common');
                    //SQL запрос на вывод друзей, по дате новых 20
                    $sql_mutual = $db->super_query("SELECT tb1.friend_id, tb3.user_birthday, user_photo, user_search_pref, user_country_city_name, user_last_visit, user_logged_mobile FROM `users` tb3, `friends` tb1 INNER JOIN `friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$uid}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0 AND tb1.friend_id = tb3.user_id ORDER by `friends_date` LIMIT {$limit_page}, {$gcount}", true);
                    if ($sql_mutual) {
                        $tpl->load_template('friends/friend.tpl');
                        foreach ($sql_mutual as $row) {
                            $user_country_city_name = explode('|', $row['user_country_city_name']);
                            $tpl->set('{country}', $user_country_city_name[0]);
                            if ($user_country_city_name[1]) {
                                $tpl->set('{city}', ', ' . $user_country_city_name[1]);
                            } else {
                                $tpl->set('{city}', '');
                            }

                            $tpl->set('{user-id}', $row['friend_id']);
                            $tpl->set('{name}', $row['user_search_pref']);

                            $config = settings_get();
                            $avaPREFver = $avaPREFver ?? '';
                            $noAvaPrf = $noAvaPrf ?? '';
                            if ($row['user_photo']) {
                                $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/' . $avaPREFver . $row['user_photo']);
                            } else {
                                $tpl->set('{ava}', "/images/{$noAvaPrf}");
                            }

                            OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);

                            //Возраст юзера
                            $user_birthday = explode('-', $row['user_birthday']);
                            $tpl->set('{age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]));

                            if ($get_user_id == $user_info['user_id']) {

                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');

                            } else {
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }

                            $tpl->set('[viewer]', '');
                            $tpl->set('[/viewer]', '');

                            $tpl->compile('content');

                        }

                        navigation($gcount, $count_common['cnt'], $config['home_url'] . 'friends/common/' . $uid . '/page/');

                    }

                }

            } else {
                msgbox('', 'У Вас с этим пользователем нет общих друзей.', 'info_2');
            }

            break;

        default:

            //################### Просмотр всех друзей ###################//
            $mobile_speedbar = 'Друзья';

            $get_user_id = (new Request)->int('user_id');
            if (!$get_user_id)
                $get_user_id = $user_info['user_id'];

            //ЧС
            $CheckBlackList = CheckBlackList($get_user_id);
            if (!$CheckBlackList) {
                //Выводим кол-во друзей из таблицы юзеров
                $friends_sql = $db->super_query("SELECT user_name, user_friends_num FROM `users` WHERE user_id = '{$get_user_id}'");

                if ($user_info['user_id'] != $get_user_id) {
                    $gram_name = grammaticalName($friends_sql['user_name']);
                } else {
                    $gram_name = 'Вас';
                }

                if ($friends_sql['user_friends_num']) {
                    $user_speedbar = 'У ' . $gram_name . ' <span id="friend_num">' . $friends_sql['user_friends_num'] . '</span> ' . declWord($friends_sql['user_friends_num'], 'friends');
                } else {
                    $user_speedbar = $lang['no_requests'];
                }

                //Верх
                $tpl->load_template('friends/head.tpl');
                if ($user_info['user_id'] !== $get_user_id) {
                    $tpl->set('{name}', $gram_name);
                } else {
                    $tpl->set('{name}', '');
                }

                $tpl->set('{user-id}', $get_user_id);
                if ($get_user_id == $user_info['user_id']) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                    $tpl->set_block("'\\[not-owner\\](.*?)\\[/not-owner\\]'si", "");
                    if ($user_info['user_friends_demands']) {
                        $tpl->set('{demands}', '(' . $user_info['user_friends_demands'] . ')');
                    } else {
                        $tpl->set('{demands}', '');
                    }
                } else {
                    $tpl->set('[not-owner]', '');
                    $tpl->set('[/not-owner]', '');
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }

                $tpl->set('[all-friends]', '');
                $tpl->set('[/all-friends]', '');
                $tpl->set_block("'\\[request-friends\\](.*?)\\[/request-friends\\]'si", "");
                $tpl->set_block("'\\[online-friends\\](.*?)\\[/online-friends\\]'si", "");
                $tpl->compile('info');

                //Все друзья
                if ($friends_sql['user_friends_num']) {

                    if ($get_user_id == $user_info['user_id']) {
                        $sql_order = "ORDER by `views`";
                    } else {
                        $sql_order = "ORDER by `friends_date`";
                    }

                    $sql_ = $db->super_query("SELECT tb1.friend_id, tb2.user_birthday, user_photo, user_search_pref, user_country_city_name, user_last_visit, user_logged_mobile FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$get_user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 {$sql_order} DESC LIMIT {$limit_page}, {$gcount}", true);
                    if ($sql_) {
                        $tpl->load_template('friends/friend.tpl');
                        foreach ($sql_ as $row) {
                            if ($row['user_country_city_name'] == '' || $row['user_country_city_name'] == '|') {
                                $tpl->set('{country}', '');
                                $tpl->set('{city}', '');
                            } else {
                                $user_country_city_name = explode('|', $row['user_country_city_name']);
                                $tpl->set('{country}', $user_country_city_name[0]);

                                if ($user_country_city_name[1]) {
                                    $tpl->set('{city}', ', ' . $user_country_city_name[1]);
                                } else {
                                    $tpl->set('{city}', '');
                                }
                            }


                            $tpl->set('{user-id}', $row['friend_id']);
                            $tpl->set('{name}', $row['user_search_pref']);

                            $config = settings_get();
                            // FOR MOBILE VERSION 1.0
                            if ($config['temp'] == 'mobile') {
                                $avaPREFver = '50_';
                                $noAvaPrf = 'no_ava_50.png';
                            } else {
                                $avaPREFver = '100_';
                                $noAvaPrf = '100_no_ava.png';
                            }

                            if ($row['user_photo']) {
                                $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/' . $avaPREFver . $row['user_photo']);
                            } else {
                                $tpl->set('{ava}', "/images/{$noAvaPrf}");
                            }

                            OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);

                            //Возраст юзера
                            if ($row['user_birthday'] == '0-0-0') {
                                $tpl->set('{age}', '');
                            } else {
                                if (!empty($row['user_birthday'])){//fixme null
                                    $user_birthday = explode('-', $row['user_birthday']);
                                    $tpl->set('{age}', user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]));
                                }else{
                                    $tpl->set('{age}', '');
                                }
                            }

                            if ($get_user_id == $user_info['user_id']) {
                                $tpl->set('[owner]', '');
                                $tpl->set('[/owner]', '');
                            } else {
                                $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                            }

                            if ($row['friend_id'] == $user_info['user_id']) {
                                $tpl->set_block("'\\[viewer\\](.*?)\\[/viewer\\]'si", "");
                            } else {
                                $tpl->set('[viewer]', '');
                                $tpl->set('[/viewer]', '');
                            }

                            $tpl->compile('content');
                        }
                        navigation($gcount, $friends_sql['user_friends_num'], $config['home_url'] . 'friends/' . $get_user_id . '/page/');
                    } else {
                        msgbox('', $lang['no_requests'], 'info_2');
                    }
                } else {
                    msgbox('', $lang['no_requests'], 'info_2');
                }
            } else {
                $user_speedbar = $lang['error'];
                msgbox('', $lang['no_notes'], 'info');
            }

            compile($tpl, $params);
    }
//    $db->free();
//    $tpl->clear();
} else {
    $user_speedbar = 'Информация';
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}