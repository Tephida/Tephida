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
use Mozg\classes\Dialog;

NoAjaxQuery();

$jsonResponse = array();
if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $db = Registry::get('db');
    $server_time = Registry::get('server_time');

    switch ($act) {
        case 'exclude':
            $id = (new Request)->int('id');
            $room_id = (new Request)->int('room_id');
            if ($room_id) {
                if ($id) {
                    $row = $db->super_query("SELECT id, photo FROM room WHERE id = '{$room_id}' and owner = '{$user_id}'");
                    if ($row) {
                        $row2 = $db->super_query("SELECT id FROM room_users WHERE room_id = '{$room_id}' and oid2 = '{$id}' and type = 0");
                        if ($row2) {
                            $attach_files = '';
                            /** fixme limit */
                            $sql = $db->super_query("SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type = 0", true);
                            if ($sql) {
                                $msg = json_encode(array('type' => 5, 'oid' => $user_id, 'oid2' => $id), JSON_THROW_ON_ERROR);
                                $id2 = md5(random_int(0, 1000000) . $server_time);
                                $user_ids = array();
                                foreach ($sql as $k => $v) {
                                    $user_ids[] = $v['oid2'];
                                }
                                $db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', information = 1, theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . $server_time . "', history_user_id = 0, attach = '" . $attach_files . "'");
                                $dbid2 = $db->insert_id();
                                foreach ($sql as $k => $v) {
                                    $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v['oid2'] . "'");
                                    $check_im_2 = $db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v['oid2'] . "' AND im_user_id = 0 AND room_id = '" . $room_id . "'");
                                    if (!$check_im_2) {
                                        $db->query("INSERT INTO im SET iuser_id = '" . $v['oid2'] . "', im_user_id = 0, room_id = '" . $room_id . "', msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                                    } else {
                                        $db->query("UPDATE im  SET idate = '" . $server_time . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                                    }
                                    $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v['oid2']}'");
                                    $update_time = $server_time - 70;
                                    if ($check2['user_last_visit'] >= $update_time) {
                                        $msg_lnk = '/messages#c' . $room_id;
                                        $msg = informationText($msg);
                                        $db->query("INSERT INTO `updates` SET for_user_id = '{$v['oid2']}', from_user_id = '{$user_id}', type = '8', date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                        Cache::mozgCreateCache("user_{$v['oid2']}/updates", 1);
                                    }
                                    Cache::mozgClearCacheFile('user_' . $v['oid2'] . '/im');
                                    Cache::mozgCreateCache('user_' . $v['oid2'] . '/im_update', '1');
                                    Cache::mozgCreateCache("user_{$v['oid2']}/typograf{$user_id}", "");
                                }
                            }
                            $db->query("UPDATE `room_users` SET type = 1 WHERE id = '" . $row2['id'] . "'");
                            $jsonResponse['response'] = $room_id;
                        } else {
                            $jsonResponse['error'] = 'Пользователь не найден';
                        }
                    } else {
                        $jsonResponse['error'] = 'Ошибка доступа';
                    }
                } else {
                    $jsonResponse['error'] = 'Пользователь не найден';
                }
            } else {
                $jsonResponse['error'] = 'Ошибка доступа';
            }
            echo json_encode($jsonResponse, JSON_THROW_ON_ERROR);
            break;

        case 'uploadRoomAvatar':
            $room_id = (new Request)->int('room_id');
            if ($room_id) {
                $row = $db->super_query("SELECT id, photo FROM room WHERE id = '{$room_id}' and owner = '{$user_id}'");
                if ($row) {
                    $uploaddir = ROOT_DIR . '/uploads/room/' . $room_id . '/';
                    try {
                        Filesystem::createDir($uploaddir);
                    } catch (Exception $e) {

                    }

                    $image_tmp = $_FILES['uploadfile']['tmp_name'];
                    $image_name = to_translit($_FILES['uploadfile']['name']);
                    $image_rename = substr(md5($server_time + rand(1, 100000)), 0, 20);
                    $image_size = $_FILES['uploadfile']['size'];
                    $type = end(explode(".", $image_name));
                    if ($type == 'jpg' || $type == 'png') {
                        $config = settings_get();
                        $config['max_photo_size'] *= 1000;
                        if ($image_size < $config['max_photo_size']) {
                            $res_type = strtolower('.' . $type);
                            if (move_uploaded_file($image_tmp, $uploaddir . $image_rename . $res_type)) {
                                $url = $config['home_url'] . 'uploads/room/' . $room_id . '/' . $image_rename . $res_type;
                                $db->query("UPDATE `room` SET photo = '{$url}' WHERE id = '" . $room_id . "'");
                                $attach_files = '';
                                /** fixme limit */
                                $sql = $db->super_query("SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type = 0", true);
                                if ($sql) {
                                    $msg = json_encode(array('type' => 4, 'oid' => $user_id), JSON_THROW_ON_ERROR);
                                    $id2 = md5(random_int(0, 1000000) . $server_time);
                                    $user_ids = array();
                                    foreach ($sql as $k => $v) {
                                        $user_ids[] = $v['oid2'];
                                    }
                                    $db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', information = 1, theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . $server_time . "',  history_user_id = 0, attach = '" . $attach_files . "'");
                                    $dbid2 = $db->insert_id();
                                    foreach ($sql as $k => $v) {
                                        $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v['oid2'] . "'");
                                        $check_im_2 = $db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v['oid2'] . "' AND im_user_id = 0 AND room_id = '" . $room_id . "'");
                                        if (!$check_im_2) {
                                            $db->query("INSERT INTO im SET iuser_id = '" . $v['oid2'] . "', im_user_id = 0, room_id = '" . $room_id . "', msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                                        } else {
                                            $db->query("UPDATE im  SET idate = '" . $server_time . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                                        }
                                        $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v['oid2']}'");
                                        $update_time = $server_time - 70;
                                        if ($check2['user_last_visit'] >= $update_time) {
                                            $msg_lnk = '/messages#c' . $room_id;
                                            $msg = informationText($msg);
                                            $db->query("INSERT INTO `updates` SET for_user_id = '{$v['oid2']}', from_user_id = '{$user_id}', type = '8', date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                            Cache::mozgCreateCache("user_{$v['oid2']}/updates", 1);
                                        }
                                        Cache::mozgClearCacheFile('user_' . $v['oid2'] . '/im');
                                        Cache::mozgCreateCache('user_' . $v['oid2'] . '/im_update', '1');
                                        Cache::mozgCreateCache("user_{$v['oid2']}/typograf{$user_id}", "");
                                    }
                                }
                                $jsonResponse['response'] = $room_id;
                            } else {
                                $jsonResponse['error'] = 'Нет места на диске';
                            }
                        } else {
                            $jsonResponse['error'] = 'Превышен максимальный объем фотографии';
                        }
                    } else {
                        $jsonResponse['error'] = 'Неподходящий формат';
                    }
                } else {
                    $jsonResponse['error'] = 'Ошибка доступа';
                }
            } else {
                $jsonResponse['error'] = 'Ошибка доступа';
            }
            echo json_encode($jsonResponse, JSON_THROW_ON_ERROR);
            break;

        case 'viewRoomBox':
            $room_id = (new Request)->int('room_id');
            $row = $db->super_query("SELECT id, title, owner, photo FROM room WHERE id = '{$room_id}'");
            if ($row) {
                $tpl->result['users'] = '';
                /** fixme limit */
                $sql = $db->super_query("SELECT tb1.id, tb2.user_id, tb2.user_search_pref, tb2.user_photo FROM room_users tb1, users tb2 WHERE tb1.room_id = '{$room_id}' and tb1.type = 0 and tb1.oid2 = tb2.user_id", true);
                if ($sql) {
                    $tpl->load_template('im/viewRoomItem.tpl');
                    foreach ($sql as $row2) {
                        $tpl->set('{id}', $row2['user_id']);
                        $tpl->set('{room}', $room_id);
                        $tpl->set('{name}', $row2['user_search_pref']);
                        if ($row['owner'] == $user_id && $row['owner'] != $row2['user_id']) {
                            $tpl->set('[admin]', '');
                            $tpl->set('[/admin]', '');
                        } else $tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si", "");
                        $tpl->set('{avatar}', $row2['user_photo'] ? '/uploads/users/' . $row2['user_id'] . '/50_' . $row2['user_photo'] : '/images/no_ava_50.png');
                        $tpl->compile('users');
                    }
                }
                $tpl->load_template('im/viewRoom.tpl');
                $tpl->set('{id}', $room_id);
                $tpl->set('{name}', $row['title']);
                $tpl->set('{users}', $tpl->result['users']);
                if ($row['owner'] == $user_id) {
                    $tpl->set('[owner]', '');
                    $tpl->set('[/owner]', '');
                } else {
                    $tpl->set_block("'\\[owner\\](.*?)\\[/owner\\]'si", "");
                }
                $tpl->set('{nameAttr}', $row['owner'] == $user_id ? '' : 'disabled');
                $tpl->set('{avatar}', $row['photo'] ? $row['photo'] : '/images/no_ava_50.png');
                $tpl->compile('content');
                AjaxTpl($tpl);
            }
            break;

        case 'saveRoomName':
            $room_id = (new Request)->int('room_id');
            $title = substr((new Request)->filter('title'), 0, 100);
            if ($room_id) {
                if ($title) {
                    $row = $db->super_query("SELECT id FROM room WHERE id = '{$room_id}' and owner = '{$user_id}'");
                    if ($room_id > 0) {
                        $db->query("UPDATE `room` SET title = '{$title}' WHERE id = '" . $room_id . "'");
                        $attach_files = '';
                        /** fixme limit */
                        $sql = $db->super_query("SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type = 0", true);
                        if ($sql) {
                            $msg = json_encode(array('type' => 3, 'oid' => $user_id), JSON_THROW_ON_ERROR);
                            $id2 = md5(random_int(0, 1000000) . $server_time);
                            $user_ids = array();
                            foreach ($sql as $k => $v) {
                                $user_ids[] = $v['oid2'];
                            }
                            $db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', information = 1, theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . $server_time . "',  history_user_id = 0, attach = '" . $attach_files . "'");
                            $dbid2 = $db->insert_id();
                            foreach ($sql as $k => $v) {
                                $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v['oid2'] . "'");
                                $check_im_2 = $db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v['oid2'] . "' AND im_user_id = 0 AND room_id = '" . $room_id . "'");
                                if (!$check_im_2) {
                                    $db->query("INSERT INTO im SET iuser_id = '" . $v['oid2'] . "', im_user_id = 0, room_id = '" . $room_id . "', msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                                } else {
                                    $db->query("UPDATE im  SET idate = '" . $server_time . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                                }
                                $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v['oid2']}'");
                                $update_time = $server_time - 70;
                                if ($check2['user_last_visit'] >= $update_time) {
                                    $msg_lnk = '/messages#c' . $room_id;
                                    $msg = informationText($msg);
                                    $db->query("INSERT INTO `updates` SET for_user_id = '{$v['oid2']}', from_user_id = '{$user_id}', type = '8', date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                    Cache::mozgCreateCache("user_{$v['oid2']}/updates", 1);
                                }
                                Cache::mozgClearCacheFile('user_' . $v['oid2'] . '/im');
                                Cache::mozgCreateCache('user_' . $v['oid2'] . '/im_update', '1');
                                Cache::mozgCreateCache("user_{$v['oid2']}/typograf{$user_id}", "");
                            }
                        }
                        $jsonResponse['response'] = $room_id;
                    } else {
                        $jsonResponse['error'] = 'Ошибка доступа';
                    }
                } else {
                    $jsonResponse['error'] = 'Введите название';
                }
            } else {
                $jsonResponse['error'] = 'Ошибка доступа';
            }
            echo json_encode($jsonResponse, JSON_THROW_ON_ERROR);
            break;

        case 'createRoomBox':
            $tpl->result['friends'] = '';
            /** fixme limit */
            $sql = $db->super_query("SELECT tb1.friend_id, tb2.user_photo, tb2.user_search_pref FROM friends tb1, users tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 ORDER by friends_date DESC", true);
            if ($sql) {
                $tpl->load_template('im/createRoomItem.tpl');
                $config = settings_get();
                foreach ($sql as $row) {
                    $tpl->set('{avatar}', $row['user_photo'] ? $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/50_' . $row['user_photo'] : "/images/100_no_ava.png");
                    $tpl->set('{id}', $row['friend_id']);
                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{function}', 'imRoom.toogleItem(this);');
                    $tpl->compile('friends');
                }
            }
            $tpl->load_template('im/createRoom.tpl');
            $tpl->set('{friends}', $tpl->result['friends']);
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        case 'inviteToRoomBox':
            $room_id = (new Request)->int('room_id');
            $tpl->result['friends'] = '';
            /** fixme limit */
            $sql = $db->super_query("SELECT tb1.friend_id, tb2.user_photo, tb2.user_search_pref FROM friends tb1, users tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 AND tb1.friend_id NOT IN (SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type != 2) ORDER by friends_date DESC", true);
            if ($sql) {
                $tpl->load_template('im/createRoomItem.tpl');
                $config = settings_get();
                foreach ($sql as $row) {
                    $tpl->set('{avatar}', $row['user_photo'] ? $config['home_url'] . 'uploads/users/' . $row['friend_id'] . '/50_' . $row['user_photo'] : "/images/100_no_ava.png");
                    $tpl->set('{id}', $row['friend_id']);
                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{function}', 'imRoom.toogleItem(this);');
                    $tpl->compile('friends');
                }
            }
            $tpl->load_template('im/inviteToRoom.tpl');
            $tpl->set('{id}', $room_id);
            $tpl->set('{friends}', $tpl->result['friends']);
            $tpl->compile('content');
            AjaxTpl($tpl);
            break;

        case 'createRoom':
            $title = (new Request)->filter('title');
            $user_ids = (new Request)->filter('user_ids');
            if ($title) {
                $user_ids = array_diff($user_ids, array(''));
                $user_ids = array_diff($user_ids, array($user_id));
                $user_ids = array_unique($user_ids);
                $count = count($user_ids);
                foreach ($user_ids as $k => $v) {
                    if ($v <= 0 || !is_numeric($v)) die();
                }
                if ($count >= 1 && $count <= 100) {
                    $row = $db->super_query("SELECT count(*) as cnt FROM friends WHERE user_id = '{$user_id}' AND subscriptions = 0 AND friend_id IN (" . implode(',', $user_ids) . ")");

                    if ($row['cnt'] >= 1 && $count == $row['cnt']) {
                        $attach_files = '';
                        $server_time = $server_time ?? time();
                        $db->query("INSERT INTO room SET title = '{$title}', owner = '{$user_id}', date = '{$server_time}'");
                        $room_id = $db->insert_id();
                        $msg = json_encode(array('type' => 0, 'oid' => $user_id));
                        $id2 = md5(random_int(0, 1000000) . $server_time);
                        $user_ids2 = $user_ids;
                        $user_ids2[] = $user_id;
                        $db->query("INSERT INTO `messages` 
                        SET user_ids = '" . implode(',', $user_ids2) . "', information = 1, theme = '...', text = '" . $msg . "', 
                        room_id = '{$room_id}', date = '" . $server_time . "',  history_user_id = 0, attach = '" . $attach_files . "'");
//                        $dbid2 = $db->insert_id();
                        foreach ($user_ids2 as $k => $v) {
                            $db->query("INSERT INTO room_users 
                            SET room_id = '{$room_id}', oid = '{$user_id}', oid2 = '{$v}', type = 0, date = '{$server_time}'");
                            $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v . "'");
                            $db->query("INSERT INTO im 
                            SET iuser_id = '" . $v . "', im_user_id = 0, room_id = '" . $room_id . "', 
                            msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                            $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v}'");
                            $update_time = $server_time - 70;

                            $check2['user_last_visit'] = ($check2['user_last_visit'] < 0) ? time() : $check2['user_last_visit'];

                            if ($check2['user_last_visit'] >= $update_time) {
                                $msg_lnk = '/messages#c' . $room_id;
                                $msg = informationText($msg);
                                $db->query("INSERT INTO `updates` 
                                SET for_user_id = '{$v}', from_user_id = '{$user_id}', type = '8', 
                                date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', 
                                user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                Cache::mozgCreateCache("user_{$v}/updates", 1);
                            }
                            Cache::mozgClearCacheFile('user_' . $v . '/im');
                            Cache::mozgCreateCache('user_' . $v . '/im_update', '1');
                            Cache::mozgCreateCache("user_{$v}/typograf{$user_id}", "");
                        }
                        $jsonResponse['response'] = $room_id;
                    } else {
                        $jsonResponse['error'] = 'Ошибка! Выберите участников';
                    }
                } else {
                    $jsonResponse['error'] = 'Выберите участников';
                }
            } else {
                $jsonResponse['error'] = 'Введите название';
            }
            echo json_encode($jsonResponse);
            break;

        case 'inviteToRoom':
            $room_id = (new Request)->int('room_id');
            $user_ids = (new Request)->filter('user_ids');
            if ($user_ids) {
                if ($room_id) {
                    $user_ids = array_diff($user_ids, array(''));
                    $user_ids = array_diff($user_ids, array($user_id));
                    $user_ids = array_unique($user_ids);
                    $count = count($user_ids);
                    foreach ($user_ids as $k => $v) {
                        if ($v <= 0 || !is_numeric($v)) die();
                    }
                    $row = $db->super_query("SELECT id FROM room WHERE id = '{$room_id}' AND owner = '{$user_id}'");
                    if ($row) {
                        $row2 = $db->super_query("SELECT count(*) as cnt FROM friends WHERE user_id = '{$user_id}' AND subscriptions = 0 AND friend_id IN (" . implode(',', $user_ids) . ") AND friend_id NOT IN (SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type != 1)");
                        if ($row2['cnt'] && $row2['cnt'] == $count) {
                            foreach ($user_ids as $k2 => $v2) {
                                $db->query("INSERT INTO room_users SET room_id = '{$room_id}', oid = '{$user_id}', oid2 = '{$v2}', type = 0, date = '{$server_time}'");
                                $attach_files = '';
                                /** fixme limit */
                                $sql = $db->super_query("SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type = 0", true);
                                if ($sql) {
                                    $msg = json_encode(array('type' => 1, 'oid' => $user_id, 'oid2' => $v2));
                                    $id2 = md5(random_int(0, 1000000) . $server_time);
                                    $user_ids = array();
                                    foreach ($sql as $k => $v) {
                                        $user_ids[] = $v['oid2'];
                                    }
                                    $db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', information = 1, theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . $server_time . "',  history_user_id = 0, attach = '" . $attach_files . "'");
                                    $dbid2 = $db->insert_id();
                                    foreach ($sql as $k => $v) {
                                        $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v['oid2'] . "'");
                                        $check_im_2 = $db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v['oid2'] . "' AND im_user_id = 0 AND room_id = '" . $room_id . "'");
                                        if (!$check_im_2) {
                                            $db->query("INSERT INTO im SET iuser_id = '" . $v['oid2'] . "', im_user_id = 0, room_id = '" . $room_id . "', msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                                        } else {
                                            $db->query("UPDATE im  SET idate = '" . $server_time . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                                        }
                                        $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v['oid2']}'");
                                        $update_time = $server_time - 70;
                                        if ($check2['user_last_visit'] >= $update_time) {
                                            $msg_lnk = '/messages#c' . $room_id;
                                            $msg = informationText($msg);
                                            $db->query("INSERT INTO `updates` SET for_user_id = '{$v['oid2']}', from_user_id = '{$user_id}', type = '8', date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                            Cache::mozgCreateCache("user_{$v['oid2']}/updates", 1);
                                        }
                                        Cache::mozgClearCacheFile('user_' . $v['oid2'] . '/im');
                                        Cache::mozgCreateCache('user_' . $v['oid2'] . '/im_update', '1');
                                        Cache::mozgCreateCache("user_{$v['oid2']}/typograf{$user_id}", "");
                                    }
                                }
                            }
                            $jsonResponse['response'] = $room_id;
                        } else {
                            $jsonResponse['error'] = 'Ошибка доступа';
                        }
                    } else {
                        $jsonResponse['error'] = 'Ошибка доступа';
                    }
                } else {
                    $jsonResponse['error'] = 'Ошибка доступа';
                }
            } else {
                $jsonResponse['error'] = 'Выберите друзей';
            }
            echo json_encode($jsonResponse);
            break;

        case 'exitFromRoom':
            $room_id = (new Request)->int('room_id');
            if ($room_id) {
                $row = $db->super_query("SELECT id FROM room WHERE id = '{$room_id}' AND owner != '{$user_id}'");
                if ($row) {
                    $row2 = $db->super_query("SELECT id FROM room_users WHERE oid2 = '{$user_id}' and room_id = '{$room_id}' and type = 0");
                    if ($row2) {
                        $attach_files = '';
                        /** fixme limit */
                        $sql = $db->super_query("SELECT oid2 FROM room_users WHERE room_id = '{$room_id}' and type = 0", true);
                        if ($sql) {
                            $msg = json_encode(array('type' => 2, 'oid' => $user_id));
                            $id2 = md5(random_int(0, 1000000) . $server_time);
                            $user_ids = array();
                            foreach ($sql as $k => $v) $user_ids[] = $v['oid2'];
                            $db->query("INSERT INTO `messages` SET user_ids = '" . implode(',', $user_ids) . "', information = 1, theme = '...', text = '" . $msg . "', room_id = '{$room_id}', date = '" . $server_time . "',  history_user_id = 0, attach = '" . $attach_files . "'");
                            $dbid2 = $db->insert_id();
                            foreach ($sql as $k => $v) {
                                $db->query("UPDATE `users` SET user_pm_num = user_pm_num+1 WHERE user_id = '" . $v['oid2'] . "'");
                                $check_im_2 = $db->super_query("SELECT id FROM im WHERE iuser_id = '" . $v['oid2'] . "' AND im_user_id = 0 AND room_id = '" . $room_id . "'");
                                if (!$check_im_2) $db->query("INSERT INTO im SET iuser_id = '" . $v['oid2'] . "', im_user_id = 0, room_id = '" . $room_id . "', msg_num = 1, idate = '" . $server_time . "', all_msg_num = 1");
                                else $db->query("UPDATE im  SET idate = '" . $server_time . "', msg_num = msg_num+1, all_msg_num = all_msg_num+1 WHERE id = '" . $check_im_2['id'] . "'");
                                $check2 = $db->super_query("SELECT user_last_visit FROM `users` WHERE user_id = '{$v['oid2']}'");
                                $update_time = $server_time - 70;
                                if ($check2['user_last_visit'] >= $update_time) {
                                    $msg_lnk = '/messages#c' . $room_id;
                                    $msg = informationText($msg);
                                    $db->query("INSERT INTO `updates` SET for_user_id = '{$v['oid2']}', from_user_id = '{$user_id}', type = '8', date = '{$server_time}', text = '{$msg}', user_photo = '{$user_info['user_photo']}', user_search_pref = '{$user_info['user_search_pref']}', lnk = '{$msg_lnk}'");
                                    Cache::mozgCreateCache("user_{$v['oid2']}/updates", 1);
                                }
                                Cache::mozgClearCacheFile('user_' . $v['oid2'] . '/im');
                                Cache::mozgCreateCache('user_' . $v['oid2'] . '/im_update', '1');
                                Cache::mozgCreateCache("user_{$v['oid2']}/typograf{$user_id}", "");
                            }
                        }
                        $db->query("UPDATE room_users  SET type = 2 WHERE id = '" . $row2['id'] . "'");
                        $jsonResponse['response'] = $room_id;
                    } else {
                        $jsonResponse['error'] = 'Ошибка доступа';
                    }
                } else {
                    $jsonResponse['error'] = 'Ошибка доступа';
                }
            } else {
                $jsonResponse['error'] = 'Ошибка доступа';
            }
            echo json_encode($jsonResponse);
            break;

        case "send":
            NoAjaxQuery();

            $room_id = (new Request)->int('room_id');
            $for_user_id = (new Request)->int('for_user_id');
            if ($room_id) {
                $for_user_id = 0;
            }
            $msg = (new Request)->filter('msg');
            $my_ava = (new Request)->filter('my_ava');
            $my_name = (new Request)->filter('my_name');
            $attach_files = (new Request)->filter('attach_files');
            $attach_files = str_replace('vote|', 'hack|', $attach_files);


            $dialog = new Dialog($user_id);
            $response = $dialog->send($for_user_id, $room_id, $msg, $attach_files);
            (new \FluffyDollop\Http\Response)->_e_json($response);

/*            $tpl->load_template('im/msg.tpl');
            $tpl->set('{ava}', $my_ava);
            $tpl->set('{name}', $my_name);
            $tpl->set('{user-id}', $user_id);
            $attach_result = '';
            if ($attach_files) {
                $attach_arr = explode('||', $attach_files);
                $cnt_attach = 1;
                $jid = 0;

                foreach ($attach_arr as $attach_file) {
                    $attach_type = explode('|', $attach_file);
                    if ($attach_type[0] == 'photo_u') {
                        $attauthor_user_id = $user_id;
                        if ($attach_type[1] == 'attach' and file_exists(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")) {
                            $size = getimagesize(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}");
                            $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                            $cnt_attach++;
                        } elseif (file_exists(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")) {
                            $size = getimagesize(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}");
                            $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                            $cnt_attach++;
                        }
                    } elseif ($attach_type[0] == 'video' and file_exists(ROOT_DIR . "/uploads/videos/{$attach_type[3]}/{$attach_type[1]}"))
                        $attach_result .= "<div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" align=\"left\" /></a></div>";
                    elseif ($attach_type[0] == 'audio') {
                        $audioId = (int)$attach_type[1];
                        $audioInfo = $db->super_query("SELECT artist, name, url FROM `audio` WHERE aid = '" . $audioId . "'");
                        if ($audioInfo) {
                            $jid++;
                            $attach_result .= '<div class="audioForSize' . $row['id'] . ' player_mini_mbar_wall_all2" id="audioForSize" style="width:440px"><div class="audio_onetrack audio_wall_onemus" style="width:440px"><div class="audio_playic cursor_pointer fl_l" onClick="music.newStartPlay(\'' . $jid . '\', ' . $row['id'] . ')" id="icPlay_' . $row['id'] . $jid . '"></div><div id="music_' . $row['id'] . $jid . '" data="' . $audioInfo['url'] . '" class="fl_l" style="margin-top:-1px"><a href="/?go=search&type=5&query=' . $audioInfo['artist'] . '" onClick="Page.Go(this.href); return false"><b>' . stripslashes($audioInfo['artist']) . '</b></a> &ndash; ' . stripslashes($audioInfo['name']) . '</div><div id="play_time' . $row['id'] . $jid . '" class="color777 fl_r no_display" style="margin-top:2px;margin-right:5px">00:00</div><div class="player_mini_mbar fl_l no_display player_mini_mbar_wall player_mini_mbar_wall_all2" id="ppbarPro' . $row['id'] . $jid . '" style="width:442px"></div></div></div>';
                        }
                    } elseif ($attach_type[0] == 'smile' and file_exists(ROOT_DIR . "/uploads/smiles/{$attach_type[1]}")) {
                        $attach_result .= '<img src=\"/uploads/smiles/' . $attach_type[1] . '\" style="margin-right:5px" />';
                    } elseif ($attach_type[0] == 'doc') {
                        $doc_id = (int)$attach_type[1];
                        $row_doc = $db->super_query("SELECT dname, dsize FROM `doc` WHERE did = '{$doc_id}'", false, "wall/doc{$doc_id}");
                        if ($row_doc) {
                            $attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did=' . $doc_id . '" target="_blank" onMouseOver="myhtml.title(\'' . $doc_id . $cnt_attach . $dbid . '\', \'<b>Размер файла: ' . $row_doc['dsize'] . '</b>\', \'doc_\')" id="doc_' . $doc_id . $cnt_attach . $dbid . '">' . $row_doc['dname'] . '</a></div></div></div><div class="clear"></div>';
                            $cnt_attach++;
                        }
                    } else {
                        $attach_result .= '';
                    }
                }
                if ($attach_result) {
                    $msg = '<div style="width:442px;overflow:hidden">' . preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $msg) . $attach_result . '</div><div class="clear"></div>';
                }
            } else {
                $msg = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $msg) . $attach_result;
            }
            $tpl->set('[noInformation]', '');
            $tpl->set('[/noInformation]', '');
            $tpl->set('{style}', '');
            $tpl->set('{text}', stripslashes($msg));
            $tpl->set('{msg-id}', $dbid);
            $tpl->set('{new}', 'im_class_new');
            $tpl->set('{date}', langdate('H:i:s', $server_time));
            $tpl->compile('content');
            AjaxTpl($tpl);*/

            die();

            break;

        case "read":
            NoAjaxQuery();
            $msg_id = (new Request)->int('msg_id');
            $check = $db->super_query("SELECT id, id2, date, room_id, history_user_id, room_id, read_ids, user_ids FROM `messages` WHERE id = '" . $msg_id . "' and find_in_set('{$user_id}', user_ids) AND not find_in_set('{$user_id}', del_ids) AND not find_in_set('{$user_id}', read_ids) and history_user_id != '{$user_id}'");
            if ($check) {
                $read_ids = explode(',', $check['read_ids']);
                $read_ids[] = $user_id;
                $db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$check['id']}'");
                $db->query("UPDATE `users` SET user_pm_num = user_pm_num-1 WHERE user_id = '" . $user_id . "'");
                if (!$check['room_id']) {
                    $user_ids = explode(',', $check['user_ids']);
                    $im_user_id = $user_ids[0] == $user_id ? $user_ids[1] : $user_ids[0];
                } else {
                    $im_user_id = 0;
                }
                $db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $check['room_id'] . "'");
                Cache::mozgClearCacheFile('user_' . $check['history_user_id'] . '/im');
            }
            break;

        case "typograf":
            NoAjaxQuery();
            $room_id = (new Request)->int('room_id');
            $for_user_id = (new Request)->int('for_user_id');
            if (!$room_id) {
                if ((new Request)->int('stop') == 1) {
                    Cache::mozgCreateCache("user_{$for_user_id}/typograf{$user_id}", "");
                } else {
                    Cache::mozgCreateCache("user_{$for_user_id}/typograf{$user_id}", 1);
                }
            }
            break;

        case "update":
            NoAjaxQuery();
            $room_id = (new Request)->int('room_id');
            $for_user_id = (new Request)->int('for_user_id');
            if ($room_id) {
                $for_user_id = 0;
            }
            $last_id = (new Request)->int('last_id');
            $sess_last_id = Cache::mozgCache('user_' . $user_id . '/im');
            if (!$room_id) {
                $typograf = Cache::mozgCache("user_{$user_id}/typograf{$for_user_id}");
                if ($typograf) {
                    {
                        echo "<script>$('#im_typograf').fadeIn()</script>";
                    }
                }
            }
            if ($last_id == $sess_last_id || (!$room_id && !$for_user_id)) {
                echo 'no_new';
                die();
            }
            $count = $db->super_query("SELECT msg_num, all_msg_num FROM `im` WHERE iuser_id = '" . $user_id . "' AND im_user_id = '" . ($room_id ? 0 : $for_user_id) . "' and room_id = '{$room_id}'");
            if ($count['all_msg_num'] > 20) {
                $limit = $count['all_msg_num'] - 20;
            } else {
                $limit = 0;
            }

            $sql_ = $db->super_query("SELECT tb1.id, text, information, date, read_ids, history_user_id, attach, tell_uid, tell_date, public, tell_comm, if(history_user_id > 0, tb2.user_name, '') as user_name, if(history_user_id > 0, tb2.user_photo, '') as user_photo FROM `messages` tb1 LEFT JOIN `users` tb2 ON history_user_id > 0 AND tb1.history_user_id = tb2.user_id WHERE " . ($room_id ? "tb1.room_id = '{$room_id}'" : "tb1.room_id = 0 and find_in_set('{$for_user_id}', tb1.user_ids)") . " and find_in_set('{$user_id}', tb1.user_ids) AND not find_in_set('{$user_id}', tb1.del_ids) ORDER by `date` ASC LIMIT " . $limit . ", 20", true);
            Cache::mozgCreateCache('user_' . $user_id . '/im', $last_id);
            if ($sql_) {
                $tpl->load_template('im/msg.tpl');
                foreach ($sql_ as $row) {
                    $tpl->set('{name}', $row['user_name']);
                    $tpl->set('{user-id}', $row['history_user_id']);
                    $tpl->set('{msg-id}', $row['id']);
                    if (date('Y-m-d', $row['date']) == date('Y-m-d', $server_time)) {
                        $tpl->set('{date}', langdate('H:i:s', $row['date']));
                    } else {
                        $tpl->set('{date}', langdate('d.m.y', $row['date']));
                    }
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $row['history_user_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $read_ids = $row['read_ids'] ? explode(',', $row['read_ids']) : array();
                    if (in_array($user_id, $read_ids, true) || ($read_ids && $user_id == $row['history_user_id'])) {
                        $tpl->set('{new}', '');
                        $tpl->set('{read-js-func}', '');
                    } else {
                        $tpl->set('{new}', 'im_class_new');
                        $tpl->set('{read-js-func}', 'onMouseOver="im.read(\'' . $row['id'] . '\', ' . ($row['information'] ? 0 : $row['history_user_id']) . ', ' . $user_id . ', ' . $room_id . ')"');
                    }
                    if ($row['attach']) {
                        $attach_arr = explode('||', $row['attach']);
                        $cnt_attach = 1;
                        $cnt_attach_link = 1;
                        $jid = 0;
                        $attach_result = '';
                        $config = settings_get();
                        foreach ($attach_arr as $attach_file) {
                            $attach_type = explode('|', $attach_file);
                            if ($attach_type[0] == 'photo' and file_exists(ROOT_DIR . "/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}")) {
                                $size = getimagesize(ROOT_DIR . "/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}");
                                $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '{$row['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                $cnt_attach++;
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'photo_u') {
                                if ($row['tell_uid']) {
                                    $attauthor_user_id = $row['tell_uid'];
                                } elseif ($row['history_user_id'] == $user_id) {
                                    $attauthor_user_id = $user_id;
                                } else {
                                    $attauthor_user_id = $row['history_user_id'];
                                }
                                if ($attach_type[1] == 'attach' and file_exists(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")) {
                                    $size = getimagesize(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}");
                                    $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                    $cnt_attach++;
                                } elseif (file_exists(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")) {
                                    $size = getimagesize(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}");
                                    $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                    $cnt_attach++;
                                }
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'video' and file_exists(ROOT_DIR . "/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")) {
                                $size = getimagesize(ROOT_DIR . "/uploads/videos/{$attach_type[3]}/{$attach_type[1]}");
                                $attach_result .= "<div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" {$size[3]} align=\"left\" /></a></div>";
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'audio') {
                                $data = explode('_', $attach_type[1]);
                                $audioId = intval($data[0]);

                                $row_audio = $db->super_query("SELECT id, oid, artist, title, url, duration FROM `audio` WHERE id = '{$audioId}'");
                                if($row_audio){
                                    $stime = gmdate("i:s", $row_audio['duration']);
                                    if(!$row_audio['artist']) $row_audio['artist'] = 'Неизвестный исполнитель';
                                    if(!$row_audio['title']) $row_audio['title'] = 'Без названия';
                                    $plname = 'wall';
                                    if($row_audio['oid'] != $user_info['user_id']) $q_s = <<<HTML
<div class="audioSettingsBut"><li class="icon-plus-6" onClick="gSearch.addAudio('{$row_audio['id']}_{$row_audio['oid']}_{$plname}')" onmouseover="showTooltip(this, {text: 'Добавить в мой список', shift: [6,5,0]});" id="no_play"></li><div class="clear"></div></div>
HTML;
                                    else $q_s = '';


                                    $qauido = "<div class=\"audioPage audioElem search search_item\" id=\"audio_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" onclick=\"playNewAudio('{$row_audio['id']}_{$row_audio['oid']}_{$plname}', event);\"><div class=\"area\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tbody><tr><td><div class=\"audioPlayBut new_play_btn\"><div class=\"bl\"><div class=\"figure\"></div></div></div><input type=\"hidden\" value=\"{$row_audio['url']},{$row_audio['duration']},page\" id=\"audio_url_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\"></td><td class=\"info\"><div class=\"audioNames\" style=\"width: 275px;\"><b class=\"author\" onclick=\"Page.Go('/?go=search&query=&type=5&q='+this.innerHTML);\" id=\"artist\">{$row_audio['artist']}</b>  –  <span class=\"name\" id=\"name\">{$row_audio['title']}</span> <div class=\"clear\"></div></div><div class=\"audioElTime\" id=\"audio_time_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\">{$stime}</div>{$q_s}</td></tr></tbody></table><div id=\"player{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" class=\"audioPlayer player{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" border=\"0\" cellpadding=\"0\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tbody><tr><td style=\"width: 100%;\"><div class=\"progressBar fl_l\" style=\"width: 100%;\" onclick=\"cancelEvent(event);\" onmousedown=\"audio_player.progressDown(event, this);\" id=\"no_play\" onmousemove=\"audio_player.playerPrMove(event, this)\" onmouseout=\"audio_player.playerPrOut()\"><div class=\"audioTimesAP\" id=\"main_timeView\"><div class=\"audioTAP_strlka\">100%</div></div><div class=\"audioBGProgress\"></div><div class=\"audioLoadProgress\"></div><div class=\"audioPlayProgress\" id=\"playerPlayLine\"><div class=\"audioSlider\"></div></div></div></td><td><div class=\"audioVolumeBar fl_l\" onclick=\"cancelEvent(event);\" onmousedown=\"audio_player.volumeDown(event, this);\" id=\"no_play\"><div class=\"audioTimesAP\"><div class=\"audioTAP_strlka\">100%</div></div><div class=\"audioBGProgress\"></div><div class=\"audioPlayProgress\" id=\"playerVolumeBar\"><div class=\"audioSlider\"></div></div></div>  </td></tr></tbody></table></div></div></div>";
                                    $attach_result .= $qauido;


                                }
                                $resLinkTitle = '';

                            } elseif ($attach_type[0] == 'smile' and file_exists(ROOT_DIR . "/uploads/smiles/{$attach_type[1]}")) {
                                $attach_result .= '<img src=\"/uploads/smiles/' . $attach_type[1] . '\" style="margin-right:5px" />';
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'link' and preg_match('/https:\/\/(.*?)+$/i', $attach_type[1]) and $cnt_attach_link == 1 and stripos(str_replace('https://www.', 'https://', $attach_type[1]), $config['home_url']) === false) {
                                $count_num = count($attach_type);
                                $domain_url_name = explode('/', $attach_type[1]);
                                $rdomain_url_name = str_replace('https://', '', $domain_url_name[2]);
                                $attach_type[3] = stripslashes($attach_type[3]);
                                $attach_type[3] = substr($attach_type[3], 0, 200);
                                $attach_type[2] = stripslashes($attach_type[2]);
                                $str_title = substr($attach_type[2], 0, 55);
                                if (stripos($attach_type[4], '/uploads/attach/') === false) {
                                    $attach_type[4] = '/images/no_ava_groups_100.gif';
                                    $no_img = false;
                                } else {
                                    $no_img = true;
                                }
                                if (!$attach_type[3])
                                    $attach_type[3] = '';
                                if ($no_img && $attach_type[2]) {
                                    $attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $rdomain_url_name . '</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="border:0px"><a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="' . $attach_type[4] . '" /></div></a><div class="attatch_link_title"><a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $str_title . '</a></div><div style="max-height:50px;overflow:hidden">' . $attach_type[3] . '</div></div></div>';
                                    $resLinkTitle = $attach_type[2];
                                    $resLinkUrl = $attach_type[1];
                                } else if ($attach_type[1] and $attach_type[2]) {
                                    $attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $rdomain_url_name . '</a></div></div></div><div class="clear"></div>';
                                    $resLinkTitle = $attach_type[2];
                                    $resLinkUrl = $attach_type[1];
                                }
                                $cnt_attach_link++;
                            } elseif ($attach_type[0] == 'doc') {
                                $doc_id = (int)$attach_type[1];
                                $row_doc = $db->super_query("SELECT dname, dsize FROM `doc` WHERE did = '{$doc_id}'", false);
                                if ($row_doc) {
                                    $attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did=' . $doc_id . '" target="_blank" onMouseOver="myhtml.title(\'' . $doc_id . $cnt_attach . $row['id'] . '\', \'<b>Размер файла: ' . $row_doc['dsize'] . '</b>\', \'doc_\')" id="doc_' . $doc_id . $cnt_attach . $row['id'] . '">' . $row_doc['dname'] . '</a></div></div></div><div class="clear"></div>';
                                    $cnt_attach++;
                                }
                            } elseif ($attach_type[0] == 'vote') {
                                $vote_id = (int)$attach_type[1];
                                $row_vote = $db->super_query("SELECT title, answers, answer_num FROM `votes` WHERE id = '{$vote_id}'", false);
                                if ($vote_id) {
                                    $checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false);
                                    $row_vote['title'] = stripslashes($row_vote['title']);
                                    if (!$row['text']) {
                                        {
                                            $row['text'] = $row_vote['title'];
                                        }
                                    }
                                    $arr_answe_list = explode('|', stripslashes($row_vote['answers']));
                                    $max = $row_vote['answer_num'];
                                    /** fixme limit */
                                    $sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", true);
                                    $answer = array();
                                    foreach ($sql_answer as $row_answer) {
                                        $answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
                                    }
                                    $attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
                                    for ($ai = 0, $aiMax = count($arr_answe_list); $ai < $aiMax; $ai++) {
                                        if (!$checkMyVote['cnt']) {
                                            $attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
                                        } else {
                                            $num = $answer[$ai]['cnt'];
                                            if (!$num) {
                                                $num = 0;
                                            }
                                            if ($max !== 0) {
                                                $proc = (100 * $num) / $max;
                                            } else {
                                                $proc = 0;
                                            }
                                            $proc = round($proc, 2);
                                            $attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
											{$arr_answe_list[$ai]}<br />
											<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:" . intval($proc) . "%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
											<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
											</div><div class=\"clear\"></div>";
                                        }
                                    }
                                    if ($row_vote['answer_num']) {
                                        $answer_num_text = declWord($row_vote['answer_num'], 'fave');
                                    } else {
                                        $answer_num_text = 'человек';
                                    }
                                    if ($row_vote['answer_num'] <= 1) {
                                        $answer_text2 = 'Проголосовал';
                                    } else {
                                        $answer_text2 = 'Проголосовало';
                                    }
                                    $attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
                                }
                            } else {
                                $attach_result .= '';
                            }
                        }

                        $resLinkTitle = $resLinkTitle ?? null;
                        $resLinkUrl = $resLinkUrl ?? null;

                        if (($resLinkTitle && $row['text'] == $resLinkUrl) || !$row['text'])
                            $row['text'] = $resLinkTitle . '<div class="clear"></div>' . $attach_result;
                        else if ($attach_result) {
                            $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']) . $attach_result;
                        } else {
                            $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']);
                        }
                    } else {
                        $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']);
                    }

                    $resLinkTitle = '';

                    if ($row['tell_uid']) {
                        if ($row['public']) {
                            $rowUserTell = $db->super_query("SELECT title, photo FROM `communities` WHERE id = '{$row['tell_uid']}'", false);
                        } else {
                            $rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$row['tell_uid']}'");
                        }

                        $dateTell = megaDate($row['tell_date']);

                        if ($row['public']) {
                            $rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
                            $tell_link = 'public';
                            if ($rowUserTell['photo']) {
                                $avaTell = '/uploads/groups/' . $row['tell_uid'] . '/50_' . $rowUserTell['photo'];
                            } else {
                                $avaTell = '/images/no_ava_50.png';
                            }
                        } else {
                            $tell_link = 'u';
                            if ($rowUserTell['user_photo']) {
                                $avaTell = '/uploads/users/' . $row['tell_uid'] . '/50_' . $rowUserTell['user_photo'];
                            } else {
                                $avaTell = '/images/no_ava_50.png';
                            }
                        }
                        $row['text'] = <<<HTML
{$row['tell_comm']}
<div class="wall_repost_border">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$row['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$row['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row['text']}
<div class="clear"></div>
</div>
HTML;

                    }
                    if ($row['information']) {
                        $row['text'] = informationText($row['text']);
                    }
                    if (!$row['information']) {
                        $tpl->set('[noInformation]', '');
                        $tpl->set('[/noInformation]', '');
                    } else {
                        $tpl->set_block("'\\[noInformation\\](.*?)\\[/noInformation\\]'si", "");
                    }
                    $tpl->set('{style}', $row['information'] ? 'min-height: auto;' : '');
                    $tpl->set('{text}', stripslashes($row['text']));
                    $tpl->compile('content');
                }
                AjaxTpl($tpl);
            }
            break;

        case "history":
            NoAjaxQuery();
            $need_read = (new Request)->int('need_read');
            $room_id = (new Request)->int('room_id');
            $for_user_id = (new Request)->int('for_user_id');
            $first_id = (new Request)->int('first_id');
            if ($room_id) {
                $for_user_id = 0;
            }
            if ($for_user_id) {
                Cache::mozgCreateCache("user_{$for_user_id}/typograf{$user_id}", "");
            }
            $limit_msg = 20;
            if ($need_read) {
                $sql = $db->super_query("SELECT id, history_user_id, read_ids, user_ids, room_id FROM `messages` WHERE " . ($room_id ? "room_id = '{$room_id}'" : "room_id = 0 and find_in_set('{$for_user_id}', user_ids)") . " and find_in_set('{$user_id}', user_ids) AND not find_in_set('{$user_id}', del_ids) AND not find_in_set('{$user_id}', read_ids) and history_user_id != '{$user_id}'", true);
                if ($sql) {
                    foreach ($sql as $row) {
                        $read_ids = explode(',', $row['read_ids']);
                        $read_ids[] = $user_id;
                        $db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$row['id']}'");
                        if (!$row['room_id']) {
                            $user_ids = explode(',', $row['user_ids']);
                            $im_user_id = $user_ids[0] == $user_id ? $user_ids[1] : $user_ids[0];
                        } else {
                            $im_user_id = 0;
                        }
                        $db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $row['room_id'] . "'");
                        Cache::mozgClearCacheFile('user_' . $row['history_user_id'] . '/im');
                    }
                    $db->query("UPDATE `users` SET user_pm_num = user_pm_num-" . count($sql) . " WHERE user_id = '" . $user_id . "'");
                }
            }
            if ($first_id > 0) {
                $count = $db->super_query("SELECT COUNT(*) AS all_msg_num FROM `messages` WHERE " . ($room_id ? "room_id = '{$room_id}'" : "room_id = 0 and find_in_set('{$for_user_id}', user_ids)") . " and find_in_set('{$user_id}', user_ids) AND not find_in_set('{$user_id}', del_ids) AND id < " . $first_id);
                $sql_sort = "AND id < " . $first_id;
            } else {
                $count = $db->super_query("SELECT all_msg_num FROM `im` WHERE iuser_id = '" . $user_id . "' AND im_user_id = '" . $for_user_id . "' AND room_id = '" . $room_id . "'");
            }
            if ($count['all_msg_num'] > $limit_msg) {
                $limit = $count['all_msg_num'] - $limit_msg;
            } else {
                $limit = 0;
            }

            $sql_sort = $sql_sort ?? null;

            $sql_ = $db->super_query("SELECT tb1.id, text, information, date, read_ids, history_user_id, attach, tell_uid, tell_date, public, tell_comm, if(history_user_id > 0, tb2.user_name, '') as user_name, if(history_user_id > 0, tb2.user_photo, '') as user_photo FROM `messages` tb1 LEFT JOIN `users` tb2 ON history_user_id > 0 AND tb1.history_user_id = tb2.user_id WHERE " . ($room_id ? "tb1.room_id = '{$room_id}'" : "tb1.room_id = 0 and find_in_set('{$for_user_id}', tb1.user_ids)") . " and find_in_set('{$user_id}', tb1.user_ids) AND not find_in_set('{$user_id}', tb1.del_ids) {$sql_sort} ORDER by `date` ASC LIMIT " . $limit . ", " . $limit_msg, true);
            $tpl->load_template('im/msg.tpl');
            if (!$first_id) {
                $tpl->result['content'] .= '<div class="im_scroll">';
                if ($count['all_msg_num'] > $limit_msg) {
                    $tpl->result['content'] .= '<div class="cursor_pointer" onClick="im.page(' . ($room_id ? 'c' . $room_id : $for_user_id) . '); return false" id="wall_all_records" style="width:520px"><div class="public_wall_all_comm" id="load_wall_all_records" style="margin-left:0px">Показать предыдущие сообщения</div></div><div id="prevMsg"></div>';
                }
                $tpl->result['content'] .= '<div id="im_scroll">';
                if (!$sql_) {
                    $tpl->result['content'] .= '<div class="info_center"><div style="padding-top:210px">Здесь будет выводиться история переписки.</div></div>';
                }
            }
            if ($sql_) {
                foreach ($sql_ as $row) {
                    $tpl->set('{name}', $row['user_name']);
                    $tpl->set('{user-id}', $row['history_user_id']);
                    $tpl->set('{msg-id}', $row['id']);
                    if (date('Y-m-d', $row['date']) == date('Y-m-d', $server_time)) {
                        $tpl->set('{date}', langdate('H:i:s', $row['date']));
                    } else {
                        $tpl->set('{date}', langdate('d.m.y', $row['date']));
                    }
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', '/uploads/users/' . $row['history_user_id'] . '/50_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/no_ava_50.png');
                    }
                    $read_ids = $row['read_ids'] ? explode(',', $row['read_ids']) : array();
                    if (in_array($user_id, $read_ids) || ($read_ids && $user_id == $row['history_user_id'])) {
                        $tpl->set('{new}', '');
                        $tpl->set('{read-js-func}', '');
                    } else {
                        $tpl->set('{new}', 'im_class_new');
                        $tpl->set('{read-js-func}', 'onMouseOver="im.read(\'' . $row['id'] . '\', ' . ($row['information'] ? 0 : $row['history_user_id']) . ', ' . $user_id . ', ' . $room_id . ')"');
                    }
                    if ($row['attach']) {
                        $attach_arr = explode('||', $row['attach']);
                        $cnt_attach = 1;
                        $cnt_attach_link = 1;
                        $jid = 0;
                        $attach_result = '';
                        $config = settings_get();
                        foreach ($attach_arr as $attach_file) {
                            $attach_type = explode('|', $attach_file);
                            if ($attach_type[0] == 'photo' and file_exists(ROOT_DIR . "/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}")) {
                                $size = getimagesize(ROOT_DIR . "/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}");
                                $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/groups/{$row['tell_uid']}/photos/c_{$attach_type[1]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '{$row['tell_uid']}', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                $cnt_attach++;
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'photo_u') {
                                if ($row['tell_uid']) {
                                    $attauthor_user_id = $row['tell_uid'];
                                } elseif ($row['history_user_id'] == $user_id) {
                                    $attauthor_user_id = $user_id;
                                } else {
                                    $attauthor_user_id = $row['history_user_id'];
                                }
                                if ($attach_type[1] == 'attach' and file_exists(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}")) {
                                    $size = getimagesize(ROOT_DIR . "/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}");
                                    $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/attach/{$attauthor_user_id}/c_{$attach_type[2]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                    $cnt_attach++;
                                } elseif (file_exists(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}")) {
                                    $size = getimagesize(ROOT_DIR . "/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}");
                                    $attach_result .= "<img id=\"photo_wall_{$row['id']}_{$cnt_attach}\" src=\"/uploads/users/{$attauthor_user_id}/albums/{$attach_type[2]}/c_{$attach_type[1]}\" {$size[3]} style=\"margin-top:3px;margin-right:3px\" align=\"left\" onClick=\"groups.wall_photo_view('{$row['id']}', '', '{$attach_type[1]}', '{$cnt_attach}')\" class=\"cursor_pointer page_num{$row['id']}\" />";
                                    $cnt_attach++;
                                }
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'video' and file_exists(ROOT_DIR . "/uploads/videos/{$attach_type[3]}/{$attach_type[1]}")) {
                                $size = getimagesize(ROOT_DIR . "/uploads/videos/{$attach_type[3]}/{$attach_type[1]}");
                                $attach_result .= "<div><a href=\"/video{$attach_type[3]}_{$attach_type[2]}\" onClick=\"videos.show({$attach_type[2]}, this.href, location.href); return false\"><img src=\"/uploads/videos/{$attach_type[3]}/{$attach_type[1]}\" style=\"margin-top:3px;margin-right:3px\" {$size[3]} align=\"left\" /></a></div>";
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'audio') {
                                $data = explode('_', $attach_type[1]);
                                $audioId = intval($data[0]);

                                $row_audio = $db->super_query("SELECT id, oid, artist, title, url, duration FROM `audio` WHERE id = '{$audioId}'");
                                if($row_audio){
                                    $stime = gmdate("i:s", $row_audio['duration']);
                                    if(!$row_audio['artist']) $row_audio['artist'] = 'Неизвестный исполнитель';
                                    if(!$row_audio['title']) $row_audio['title'] = 'Без названия';
                                    $plname = 'wall';
                                    if($row_audio['oid'] != $user_info['user_id']) $q_s = <<<HTML
<div class="audioSettingsBut"><li class="icon-plus-6" onClick="gSearch.addAudio('{$row_audio['id']}_{$row_audio['oid']}_{$plname}')" onmouseover="showTooltip(this, {text: 'Добавить в мой список', shift: [6,5,0]});" id="no_play"></li><div class="clear"></div></div>
HTML;
                                    else $q_s = '';


                                    $qauido = "<div class=\"audioPage audioElem search search_item\" id=\"audio_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" onclick=\"playNewAudio('{$row_audio['id']}_{$row_audio['oid']}_{$plname}', event);\"><div class=\"area\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tbody><tr><td><div class=\"audioPlayBut new_play_btn\"><div class=\"bl\"><div class=\"figure\"></div></div></div><input type=\"hidden\" value=\"{$row_audio['url']},{$row_audio['duration']},page\" id=\"audio_url_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\"></td><td class=\"info\"><div class=\"audioNames\" style=\"width: 275px;\"><b class=\"author\" onclick=\"Page.Go('/?go=search&query=&type=5&q='+this.innerHTML);\" id=\"artist\">{$row_audio['artist']}</b>  –  <span class=\"name\" id=\"name\">{$row_audio['title']}</span> <div class=\"clear\"></div></div><div class=\"audioElTime\" id=\"audio_time_{$row_audio['id']}_{$row_audio['oid']}_{$plname}\">{$stime}</div>{$q_s}</td></tr></tbody></table><div id=\"player{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" class=\"audioPlayer player{$row_audio['id']}_{$row_audio['oid']}_{$plname}\" border=\"0\" cellpadding=\"0\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tbody><tr><td style=\"width: 100%;\"><div class=\"progressBar fl_l\" style=\"width: 100%;\" onclick=\"cancelEvent(event);\" onmousedown=\"audio_player.progressDown(event, this);\" id=\"no_play\" onmousemove=\"audio_player.playerPrMove(event, this)\" onmouseout=\"audio_player.playerPrOut()\"><div class=\"audioTimesAP\" id=\"main_timeView\"><div class=\"audioTAP_strlka\">100%</div></div><div class=\"audioBGProgress\"></div><div class=\"audioLoadProgress\"></div><div class=\"audioPlayProgress\" id=\"playerPlayLine\"><div class=\"audioSlider\"></div></div></div></td><td><div class=\"audioVolumeBar fl_l\" onclick=\"cancelEvent(event);\" onmousedown=\"audio_player.volumeDown(event, this);\" id=\"no_play\"><div class=\"audioTimesAP\"><div class=\"audioTAP_strlka\">100%</div></div><div class=\"audioBGProgress\"></div><div class=\"audioPlayProgress\" id=\"playerVolumeBar\"><div class=\"audioSlider\"></div></div></div>  </td></tr></tbody></table></div></div></div>";
                                    $attach_result .= $qauido;


                                }
                                $resLinkTitle = '';

                            } elseif ($attach_type[0] == 'smile' and file_exists(ROOT_DIR . "/uploads/smiles/{$attach_type[1]}")) {
                                $attach_result .= '<img src=\"/uploads/smiles/' . $attach_type[1] . '\" style="margin-right:5px" />';
                                $resLinkTitle = '';
                            } elseif ($attach_type[0] == 'link' and preg_match('/https:\/\/(.*?)+$/i', $attach_type[1]) and $cnt_attach_link == 1 and stripos(str_replace('https://www.', 'https://', $attach_type[1]), $config['home_url']) === false) {
                                $count_num = count($attach_type);
                                $domain_url_name = explode('/', $attach_type[1]);
                                $rdomain_url_name = str_replace('https://', '', $domain_url_name[2]);
                                $attach_type[3] = stripslashes($attach_type[3]);
                                $attach_type[3] = substr($attach_type[3], 0, 200);
                                $attach_type[2] = stripslashes($attach_type[2]);
                                $str_title = substr($attach_type[2], 0, 55);
                                if (stripos($attach_type[4], '/uploads/attach/') === false) {
                                    $attach_type[4] = '/images/no_ava_groups_100.gif';
                                    $no_img = false;
                                } else {
                                    $no_img = true;
                                }
                                if (!$attach_type[3]) {
                                    $attach_type[3] = '';
                                }
                                if ($no_img && $attach_type[2]) {
                                    $attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $rdomain_url_name . '</a></div></div><div class="clear"></div><div class="wall_show_block_link" style="border:0px"><a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank"><div style="width:108px;height:80px;float:left;text-align:center"><img src="' . $attach_type[4] . '" /></div></a><div class="attatch_link_title"><a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $str_title . '</a></div><div style="max-height:50px;overflow:hidden">' . $attach_type[3] . '</div></div></div>';
                                    $resLinkTitle = $attach_type[2];
                                    $resLinkUrl = $attach_type[1];
                                } else if ($attach_type[1] and $attach_type[2]) {
                                    $attach_result .= '<div style="margin-top:2px" class="clear"><div class="attach_link_block_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Ссылка: <a href="/index.php?go=away&url=' . $attach_type[1] . '" target="_blank">' . $rdomain_url_name . '</a></div></div></div><div class="clear"></div>';
                                    $resLinkTitle = $attach_type[2];
                                    $resLinkUrl = $attach_type[1];
                                }
                                $cnt_attach_link++;
                            } elseif ($attach_type[0] == 'doc') {
                                $doc_id = (int)$attach_type[1];
                                $row_doc = $db->super_query("SELECT dname, dsize FROM `doc` WHERE did = '{$doc_id}'", false);
                                if ($row_doc) {
                                    $attach_result .= '<div style="margin-top:5px;margin-bottom:5px" class="clear"><div class="doc_attach_ic fl_l" style="margin-top:4px;margin-left:0px"></div><div class="attach_link_block_te"><div class="fl_l">Файл <a href="/index.php?go=doc&act=download&did=' . $doc_id . '" target="_blank" onMouseOver="myhtml.title(\'' . $doc_id . $cnt_attach . $row['id'] . '\', \'<b>Размер файла: ' . $row_doc['dsize'] . '</b>\', \'doc_\')" id="doc_' . $doc_id . $cnt_attach . $row['id'] . '">' . $row_doc['dname'] . '</a></div></div></div><div class="clear"></div>';
                                    $cnt_attach++;
                                }
                            } elseif ($attach_type[0] == 'vote') {
                                $vote_id = (int)$attach_type[1];
                                $row_vote = $db->super_query("SELECT title, answers, answer_num FROM `votes` WHERE id = '{$vote_id}'", false);
                                if ($vote_id) {
                                    $checkMyVote = $db->super_query("SELECT COUNT(*) AS cnt FROM `votes_result` WHERE user_id = '{$user_id}' AND vote_id = '{$vote_id}'", false);
                                    $row_vote['title'] = stripslashes($row_vote['title']);
                                    if (!$row['text']) {
                                        $row['text'] = $row_vote['title'];
                                    }
                                    $arr_answe_list = explode('|', stripslashes($row_vote['answers']));
                                    $max = $row_vote['answer_num'];
                                    /** fixme limit */
                                    $sql_answer = $db->super_query("SELECT answer, COUNT(*) AS cnt FROM `votes_result` WHERE vote_id = '{$vote_id}' GROUP BY answer", true);
                                    $answer = array();
                                    foreach ($sql_answer as $row_answer) {
                                        $answer[$row_answer['answer']]['cnt'] = $row_answer['cnt'];
                                    }
                                    $attach_result .= "<div class=\"clear\" style=\"height:10px\"></div><div id=\"result_vote_block{$vote_id}\"><div class=\"wall_vote_title\">{$row_vote['title']}</div>";
                                    for ($ai = 0; $ai < sizeof($arr_answe_list); $ai++) {
                                        if (!$checkMyVote['cnt']) {
                                            $attach_result .= "<div class=\"wall_vote_oneanswe\" onClick=\"Votes.Send({$ai}, {$vote_id})\" id=\"wall_vote_oneanswe{$ai}\"><input type=\"radio\" name=\"answer\" /><span id=\"answer_load{$ai}\">{$arr_answe_list[$ai]}</span></div>";
                                        } else {
                                            $num = $answer[$ai]['cnt'];
                                            if (!$num) {
                                                $num = 0;
                                            }
                                            if ($max !== 0) {
                                                $proc = (100 * $num) / $max;
                                            } else {
                                                $proc = 0;
                                            }
                                            $proc = round($proc, 2);
                                            $attach_result .= "<div class=\"wall_vote_oneanswe cursor_default\">
											{$arr_answe_list[$ai]}<br />
											<div class=\"wall_vote_proc fl_l\"><div class=\"wall_vote_proc_bg\" style=\"width:" . intval($proc) . "%\"></div><div style=\"margin-top:-16px\">{$num}</div></div>
											<div class=\"fl_l\" style=\"margin-top:-1px\"><b>{$proc}%</b></div>
											</div><div class=\"clear\"></div>";
                                        }
                                    }
                                    if ($row_vote['answer_num']) {
                                        $answer_num_text = declWord($row_vote['answer_num'], 'fave');
                                    } else {
                                        $answer_num_text = 'человек';
                                    }
                                    if ($row_vote['answer_num'] <= 1) {
                                        $answer_text2 = 'Проголосовал';
                                    } else {
                                        $answer_text2 = 'Проголосовало';
                                    }
                                    $attach_result .= "{$answer_text2} <b>{$row_vote['answer_num']}</b> {$answer_num_text}.<div class=\"clear\" style=\"margin-top:10px\"></div></div>";
                                }
                            } else {
                                $attach_result .= '';
                            }
                        }
                        if (($resLinkTitle && $row['text'] == $resLinkUrl) || !$row['text']) {
                            $row['text'] = $resLinkTitle . '<div class="clear"></div>' . $attach_result;
                        } else if ($attach_result) {
                            $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']) . $attach_result;
                        } else {
                            $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']);
                        }
                    } else {
                        $row['text'] = preg_replace('`(http(?:s)?://\w+[^\s\[\]\<]+)`i', '<a href="/index.php?go=away&url=$1" target="_blank">$1</a>', $row['text']);
                    }

                    $resLinkTitle = '';

                    if ($row['tell_uid']) {
                        if ($row['public']) {
                            $rowUserTell = $db->super_query("SELECT title, photo FROM `communities` WHERE id = '{$row['tell_uid']}'", false);
                        } else {
                            $rowUserTell = $db->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$row['tell_uid']}'");
                        }

                        $dateTell = megaDate($row['tell_date']);

                        if ($row['public']) {
                            $rowUserTell['user_search_pref'] = stripslashes($rowUserTell['title']);
                            $tell_link = 'public';
                            if ($rowUserTell['photo']) {
                                $avaTell = '/uploads/groups/' . $row['tell_uid'] . '/50_' . $rowUserTell['photo'];
                            } else {
                                $avaTell = '/images/no_ava_50.png';
                            }
                        } else {
                            $tell_link = 'u';
                            if ($rowUserTell['user_photo']) {
                                $avaTell = '/uploads/users/' . $row['tell_uid'] . '/50_' . $rowUserTell['user_photo'];
                            } else {
                                $avaTell = '/images/no_ava_50.png';
                            }
                        }
                        $row['text'] = <<<HTML
{$row['tell_comm']}
<div class="wall_repost_border">
<div class="wall_tell_info"><div class="wall_tell_ava"><a href="/{$tell_link}{$row['tell_uid']}" onClick="Page.Go(this.href); return false"><img src="{$avaTell}" width="30" /></a></div><div class="wall_tell_name"><a href="/{$tell_link}{$row['tell_uid']}" onClick="Page.Go(this.href); return false"><b>{$rowUserTell['user_search_pref']}</b></a></div><div class="wall_tell_date">{$dateTell}</div></div>{$row['text']}
<div class="clear"></div>
</div>
HTML;

                    }
                    if ($row['information']) {
                        $row['text'] = informationText($row['text']);
                    }
                    if (!$row['information']) {
                        $tpl->set('[noInformation]', '');
                        $tpl->set('[/noInformation]', '');
                    } else {
                        $tpl->set_block("'\\[noInformation\\](.*?)\\[/noInformation\\]'si", "");
                    }
                    $tpl->set('{style}', $row['information'] ? 'min-height: auto;' : '');
                    $tpl->set('{text}', stripslashes($row['text']));
                    $tpl->compile('content');
                }
            }
            if (!$first_id) {
                $tpl->result['content'] .= '</div></div>';
            }
            if (!$first_id) {
                $tpl->load_template('im/form.tpl');
                $tpl->set('{for_user_id}', $room_id ? 'c' . $room_id : $for_user_id);
                $myInfo = $db->super_query("SELECT user_name FROM `users` WHERE user_id = '" . $user_id . "'");
                $tpl->set('{myuser-id}', $user_id);
                $tpl->set('{my-name}', $myInfo['user_name']);
                if ($user_info['user_photo']) {
                    $tpl->set('{my-ava}', '/uploads/users/' . $user_id . '/50_' . $user_info['user_photo']);
                } else {
                    $tpl->set('{my-ava}', '/images/no_ava_50.png');
                }
                if ($room_id) {
                    $checkRoom = $db->super_query("SELECT owner FROM `room` WHERE id = '" . $room_id . "'");
                } else {
                    $checkRoom = null;
                }
                if ($room_id && $checkRoom['owner'] == $user_id) {
                    $tpl->set('[canInvite]', '');
                    $tpl->set('[/canInvite]', '');
                } else {
                    $tpl->set_block("'\\[canInvite\\](.*?)\\[/canInvite\\]'si", "");
                }
                if ($room_id && $checkRoom['owner'] != $user_id) {
                    $checkInRoom = $db->super_query("SELECT id FROM `room_users` WHERE room_id = '" . $room_id . "' and oid2 = '" . $user_id . "' and type = 0");
                    if ($checkInRoom) {
                        $tpl->set('[canExit]', '');
                        $tpl->set('[/canExit]', '');
                    } else {
                        $tpl->set_block("'\\[canExit\\](.*?)\\[/canExit\\]'si", "");
                    }
                } else {
                    $tpl->set_block("'\\[canExit\\](.*?)\\[/canExit\\]'si", "");
                }
                if ($room_id) {
                    $tpl->set('[room]', '');
                    $tpl->set('[/room]', '');
                } else $tpl->set_block("'\\[room\\](.*?)\\[/room\\]'si", "");
                $tpl->compile('content');
            }
            AjaxTpl($tpl);
            break;

        case "upDialogs":
            NoAjaxQuery();
            $update = Cache::mozgCache('user_' . $user_id . '/im_update');
            if ($update) {
                $sql_ = $db->super_query("SELECT tb1.msg_num, im_user_id, room_id FROM `im` tb1 LEFT JOIN `users` tb2 ON tb1.im_user_id > 0 and tb2.user_id = tb1.im_user_id LEFT JOIN `room` tb3 ON tb1.room_id > 0 and tb3.id = tb1.room_id WHERE tb1.iuser_id = '" . $user_id . "' AND msg_num > 0 ORDER by `idate` DESC LIMIT 0, 50", true);
                $res = '';
                foreach ($sql_ as $row) {
                    $res .= '$("#upNewMsg' . ($row['room_id'] ? 'c' . $row['room_id'] : $row['im_user_id']) . '").html(\'<div class="im_new fl_l" id="msg_num' . ($row['room_id'] ? 'c' . $row['room_id'] : $row['im_user_id']) . '">' . $row['msg_num'] . '</div>\').show();';
                }
                if ($user_info['user_pm_num']) {
                    $user_pm_num_2 = "<div class=\"ic_newAct\" style=\"margin-left:37px\">+{$user_info['user_pm_num']}</div>";
                    $doc_title = 'document.title = \'(' . $user_info['user_pm_num'] . ') Новые сообщения\';';
                } else {
                    $doc_title = 'document.title = \'Диалоги\';';
                    Cache::mozgCreateCache('user_' . $user_id . '/im_update', '0');
                    $user_pm_num_2 = '';
                }
                echo '<script type="text/javascript">
				' . $doc_title . '
				$(\'#new_msg\').html(\'' . $user_pm_num_2 . '\');
				' . $res . '
				</script>';
            }
            break;

        case 'del':
            $room_id = (new Request)->int('room_id');
            $im_user_id = (new Request)->int('im_user_id');
            if ($room_id) {
                $im_user_id = 0;
            }
            $row = $db->super_query("SELECT id, msg_num, all_msg_num FROM `im` WHERE iuser_id = '{$user_id}' AND im_user_id = '{$im_user_id}' AND room_id = '{$room_id}'");
            if ($row) {
                $sql = $db->super_query("SELECT id, read_ids, room_id, history_user_id, del_ids FROM `messages` WHERE " . ($room_id ? "room_id = '{$room_id}'" : "room_id = 0 and find_in_set('{$im_user_id}', tb1.user_ids)") . " and find_in_set('{$user_id}', user_ids) AND not find_in_set('{$user_id}', del_ids)");
                if ($sql) {
                    foreach ($sql as $row2) {
                        $del_ids = $row2['del_ids'] ? explode(',', $row2['del_ids']) : array();
                        $del_ids[] = $user_id;
                        $del_ids = implode(',', $del_ids);
                        $db->query("UPDATE `messages` SET del_ids = '{$del_ids}' WHERE id = '{$row2['id']}'");
                        $read_ids = explode(',', $row2['read_ids']);
                        if ($row['history_user_id'] !== $user_id && !in_array($user_id, $read_ids, true)) {
                            $read_ids[] = $user_id;
                            $db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$row2['id']}'");
                            $db->query("UPDATE `users` SET user_pm_num = user_pm_num-1 WHERE user_id = '" . $user_id . "'");
                            if (!$row2['room_id']) {
                                $user_ids = explode(',', $row2['user_ids']);
                                $im_user_id = $user_ids[0] == $user_id ? $user_ids[1] : $user_ids[0];
                            } else {
                                $im_user_id = 0;
                            }
                            $db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $row2['room_id'] . "'");
                            Cache::mozgClearCacheFile('user_' . $row2['history_user_id'] . '/im');
                        }
                    }
                }
                if ($row['msg_num']) {
                    $db->query("UPDATE `users` SET user_pm_num = user_pm_num-{$row['msg_num']} WHERE user_id = '{$user_id}'");
                }
                $db->query("DELETE FROM `im` WHERE id = '{$row['id']}'");
            }
            break;

        case 'delet':
            NoAjaxQuery();
            $mid = (new Request)->int('mid');
            $row = $db->super_query("SELECT read_ids, room_id, history_user_id, del_ids FROM `messages` WHERE id = '{$mid}' AND find_in_set('{$user_id}', user_ids) and not find_in_set('{$user_id}', del_ids)");
            if ($row) {
                $del_ids = $row['del_ids'] ? explode(',', $row['del_ids']) : array();
                $del_ids[] = $user_id;
                $del_ids = implode(',', $del_ids);
                $db->query("UPDATE `messages` SET del_ids = '{$del_ids}' WHERE id = '{$mid}'");
                $read_ids = explode(',', $row['read_ids']);
                if ($row['history_user_id'] != $user_id && !in_array($user_id, $read_ids)) {
                    $read_ids[] = $user_id;
                    $db->query("UPDATE `messages` SET read_ids = '" . implode(',', $read_ids) . "' WHERE id = '{$mid}'");
                    $db->query("UPDATE `users` SET user_pm_num = user_pm_num-1 WHERE user_id = '" . $user_id . "'");
                    if (!$row['room_id']) {
                        $user_ids = explode(',', $row['user_ids']);
                        $im_user_id = $user_ids[0] == $user_id ? $user_ids[1] : $user_ids[0];
                    } else {
                        $im_user_id = 0;
                    }
                    $db->query("UPDATE `im` SET msg_num = msg_num-1 WHERE iuser_id = '" . $user_id . "' and im_user_id = '" . $im_user_id . "' AND room_id = '" . $row['room_id'] . "'");
                    Cache::mozgClearCacheFile('user_' . $row['history_user_id'] . '/im');
                }
            }
            break;

        default:
            $metatags['title'] = 'Диалоги';
            $mobile_speedbar = '<a href="/messages">Диалоги</a>';
            $sql_ = $db->super_query("SELECT tb1.msg_num, im_user_id, room_id, if(tb1.im_user_id > 0, tb2.user_search_pref, tb3.title) as user_search_pref, if(tb1.im_user_id > 0, tb2.user_photo, tb3.photo) as user_photo FROM `im` tb1 LEFT JOIN `users` tb2 ON tb1.im_user_id > 0 and tb2.user_id = tb1.im_user_id LEFT JOIN `room` tb3 ON tb1.room_id > 0 and tb3.id = tb1.room_id WHERE tb1.iuser_id = '" . $user_id . "' ORDER by `idate` DESC LIMIT 0, 50", true);
            $tpl->load_template('im/dialog.tpl');
            foreach ($sql_ as $row) {
                $tpl->set('{name}', $row['user_search_pref']);
                $tpl->set('{uid}', $row['room_id'] ? 'c' . $row['room_id'] : $row['im_user_id']);
                if ($row['user_photo']) {
                    $tpl->set('{ava}', $row['room_id'] ? $row['user_photo'] : '/uploads/users/' . $row['im_user_id'] . '/50_' . $row['user_photo']);
                } else {
                    $tpl->set('{ava}', '/images/no_ava_50.png');
                }
                if ($row['msg_num']) {
                    $tpl->set('{msg_num}', '<div class="im_new fl_l" id="msg_num' . ($row['room_id'] ? 'c' . $row['room_id'] : $row['im_user_id']) . '">' . $row['msg_num'] . '</div>');
                } else {
                    $tpl->set('{msg_num}', '');
                }
                $tpl->compile('dialog');
            }
            $tpl->load_template('im/head.tpl');
            if ($sql_) {
                $tpl->set('{dialogs}', $tpl->result['dialog']);
            } else {
                $tpl->set('{dialogs}', '');
            }
            $tpl->set('[inbox]', '');
            $tpl->set('[/inbox]', '');
            $tpl->set_block("'\\[outbox\\](.*?)\\[/outbox\\]'si", "");
            $tpl->set_block("'\\[review\\](.*?)\\[/review\\]'si", "");
            $tpl->compile('info');
            compile($tpl);
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}