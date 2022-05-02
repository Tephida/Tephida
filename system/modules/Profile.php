<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use ErrorException;
use FluffyDollop\Http\Request;
use Mozg\classes\Cache;
use Mozg\classes\DB;
use Mozg\classes\Module;
use FluffyDollop\Support\Registry;
use Mozg\classes\Wall;
use Mozg\classes\WallProfile;

class Profile extends Module
{
    /**
     * @return void
     * @throws ErrorException|\JsonException|\Exception
     */
    final public function main(): void
    {
        $db = Registry::get('db');
        $config = settings_get();
        $online_time = time() - $config['online_time'];

        $id = (new Request)->int('id');
        $user_info = Registry::get('user_info');
        $user_id = (int)$user_info['user_id'];
        $cache_folder = 'user_' . $id;
        $server_time = Registry::get('server_time');
        $params = [];

        //Читаем кеш
        $row = unserialize(Cache::mozgCache($cache_folder . '/profile_' . $id));

        //Проверяем на наличие кеша, если нет, то выводим из БД и создаём его
        if ($row) {
//            $row_online = $db->super_query("SELECT user_last_visit, user_logged_mobile FROM `users` WHERE user_id = '{$id}'");

            $row_online = \Mozg\classes\DB::getDB()->row('SELECT user_last_visit, user_logged_mobile FROM `users` WHERE user_id = ?', $id);
        } else {
            /** @var array $row */
            $row = \Mozg\classes\User::getUser($id);
            //todo update
            if ($row) {
                Cache::mozgCreateFolderCache($cache_folder);
                Cache::mozgCreateCache($cache_folder . '/profile_' . $id, serialize($row));
                $row_online['user_last_visit'] = $row['user_last_visit'];
                $row_online['user_logged_mobile'] = $row['user_logged_mobile'];
            } else {
                $row_online = \Mozg\classes\DB::getDB()->row('SELECT user_last_visit, user_logged_mobile FROM `users` WHERE user_id = ?', $id);
            }
        }

        $config = settings_get();
        $meta_tags['title'] = $row['user_search_pref'] ?? $config['home'];

//        $tpl = new TpLSite($this->tpl_dir_name, $meta_tags);

        //Если есть такой юзер, то продолжаем выполнение скрипта
        if ($row) {
            $mobile_speedbar = $row['user_search_pref'];
            $user_speedbar = $row['user_search_pref'];
            $metatags['title'] = $row['user_search_pref'];


            if ($row['user_delet'] > 0) {
//                $tpl->load_template("profile_delete_all.tpl");
                $user_name_lastname_exp = explode(' ', $row['user_search_pref']);
//                $tpl->set('{name}', $user_name_lastname_exp[0]);
//                $tpl->set('{lastname}', $user_name_lastname_exp[1]);
//                $tpl->compile('content');
                view('profile.deleted', $params);
            } elseif ($row['user_ban_date'] >= $server_time || $row['user_ban_date'] == '0') {
//                $tpl->load_template("profile_baned_all.tpl");
                $user_name_lastname_exp = explode(' ', $row['user_search_pref']);
//                $tpl->set('{name}', $user_name_lastname_exp[0]);
//                $tpl->set('{lastname}', $user_name_lastname_exp[1]);
//                $tpl->compile('content');
                view('profile.baned', $params);
            } else {
                //todo update
                if (Registry::get('logged')) {
                    $CheckBlackList = CheckBlackList($id);
                } else {
                    $CheckBlackList = false;
                }

                $params['blacklist'] = \Mozg\classes\Friends::checkBlackList($id);

                $user_privacy = unserialize($row['user_privacy']);

                Registry::set('user_privacy', $user_privacy);

                $user_name_lastname_exp = explode(' ', $row['user_search_pref']);

                if ($row['user_country_city_name'] == '') {
                    $row['user_country_city_name'] = ' | ';
                }
                $user_country_city_name_exp = explode('|', $row['user_country_city_name']);

                //################### Друзья ###################//
                if ($row['user_friends_num'] && $user_info['user_group'] === 0) {
                    /** @var array $sql_friends */
                    $sql_friends = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.user_id  AND subscriptions = 0 ORDER by rand() DESC LIMIT 0, 6", true);
                    foreach ($sql_friends as $key => $row_friends) {
                        $friend_info = explode(' ', $row_friends['user_search_pref']);
                        $all_friends[$key]['user_id'] = $row_friends['friend_id'];
                        $all_friends[$key]['name'] = $friend_info['0'];
                        if (isset($friend_info['1'])) {
                            $all_friends[$key]['last_name'] = $friend_info['1'];

                        } else {
                            $all_friends[$key]['last_name'] = 'Неизвестный пользователь';
                        }

                        if ($row_friends['user_photo']) {
                            $all_friends[$key]['ava'] = $config['home_url'] . 'uploads/users/' . $row_friends['friend_id'] . '/50_' . $row_friends['user_photo'];
                        } else {
                            $all_friends[$key]['ava'] = '/images/no_ava_50.png';
                        }
                    }
                    $params['all_friends'] = $all_friends;
                    $params['all_friends_num'] = $row['user_friends_num'];
                }

                //Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
                if (Registry::get('logged') && $user_id != $id) {
                    $check_friend = CheckFriends($row['user_id']);
                } else {
                    $check_friend = null;
                }

                //Кол-во друзей в онлайн
                $params['online_friends'] = false;
                if ($row['user_friends_num']) {
                    /** @var array $online_friends */
                    $online_friends = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` tb1, `friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$id}' AND tb1.user_last_visit >= '{$online_time}' AND subscriptions = 0");

                    //Если друзья на сайте есть то идем дальше
                    if ($online_friends['cnt']) {
                        /** @var array $sql_friends_online */
                        $sql_friends_online = $db->super_query("SELECT tb1.user_id, user_country_city_name, user_search_pref, user_birthday, user_photo FROM `users` tb1, `friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$id}' AND tb1.user_last_visit >= '{$online_time}'  AND subscriptions = 0 ORDER by rand() DESC LIMIT 0, 6", true);
                        foreach ($sql_friends_online as $key => $row_friends_online) {
                            $friend_info_online = explode(' ', $row_friends_online['user_search_pref']);
                            $sql_friends_online[$key]['user_id'] = $row_friends_online['user_id'];
                            $sql_friends_online[$key]['name'] = $friend_info_online['0'];
//                                $sql_friends_online[$key]['lastname'] = $friend_info_online[1]
                            if (isset($friend_info_online['1'])) {
                                $sql_friends_online[$key]['last_name'] = $friend_info_online['1'];
                            } else {
                                $sql_friends_online[$key]['last_name'] = 'Неизвестный пользователь';
                            }
                            if ($row_friends_online['user_photo']) {
                                $sql_friends_online[$key]['ava'] = $config['home_url'] . 'uploads/users/' . $row_friends_online['user_id'] . '/50_' . $row_friends_online['user_photo'];
                            } else {
                                $sql_friends_online[$key]['ava'] = '/images/no_ava_50.png';
                            }
                        }
                        $params['online_friends'] = $sql_friends_online;
                        $params['online_friends_num'] = $online_friends['cnt'];
                    }
                }

                //################### Заметки ###################//
                if ($row['user_notes_num']) {
//                    $tpl->result['notes'] = mozg_cache($cache_folder . '/notes_user_' . $id);
//                    if (!$tpl->result['notes']) {
//                        $sql_notes = $db->super_query("SELECT id, title, date, comm_num FROM `notes` WHERE owner_user_id = '{$id}' ORDER by `date` DESC LIMIT 0,5", true);
//                        $tpl->load_template('profile_note.tpl');
//                        foreach ($sql_notes as $row_notes) {
//                            $tpl->set('{id}', $row_notes['id']);
//                            $tpl->set('{title}', stripslashes($row_notes['title']));
//                            $tpl->set('{comm-num}', $row_notes['comm_num'] . ' ' . declWord($row_notes['comm_num'], 'comments'));
//                            $date_str = megaDate(strtotime($row_notes['date']), 'no_year');
//                            $tpl->set('{date}', $date_str);
//                            $tpl->compile('notes');
//                        }
//                        mozg_create_cache($cache_folder . '/notes_user_' . $id, $tpl->result['notes'] ?? '');
//                    }
                }

                //################### Видеозаписи ###################//
                if ($row['user_videos_num']) {
                    //Настройки приватности
                    if ($user_id == $id) {
                        $sql_privacy = "";
                    } elseif ($check_friend) {
                        $sql_privacy = "AND privacy regexp '[[:<:]](1|2)[[:>:]]'";
                        $cache_pref_videos = "_friends";
                    } else {
                        $sql_privacy = "AND privacy = 1";
                        $cache_pref_videos = "_all";
                    }

                    //Если страницу смотрит другой юзер, то считаем кол-во видео
                    if ($user_id !== $id) {
                        /** @var array $video_cnt */
                        $video_cnt = $db->super_query("SELECT COUNT(*) AS cnt FROM `videos` WHERE owner_user_id = '{$id}' {$sql_privacy} AND public_id = '0'", false);
                        $row['user_videos_num'] = $video_cnt['cnt'];
                    } else {
                        $video_cnt['cnt'] = 0;//fixme
                    }

                    /** @var array $sql_videos */
                    $sql_videos = $db->super_query("SELECT id, title, add_date, comm_num, photo FROM `videos` WHERE owner_user_id = '{$id}' {$sql_privacy} AND public_id = '0' ORDER by `add_date` DESC LIMIT 0,2", 1);

                    foreach ($sql_videos as $key => $row_videos) {
                        $sql_videos[$key]['photo'] = $row_videos['photo'];
                        $sql_videos[$key]['user_id'] = $id;
                        $sql_videos[$key]['title'] = stripslashes($row_videos['title']);
                        $titles = array('комментарий', 'комментария', 'комментариев');//comments
                        $sql_videos[$key]['comm_num'] = $row_videos['comm_num'] . ' ' . declWord($row_videos['comm_num'], 'comments');
                        $sql_videos[$key]['date'] = megaDate(strtotime($row_videos['add_date']), '');
                    }
                    $params['videos_num'] = $video_cnt['cnt'];
                    $params['videos'] = $sql_videos;
                }

                //################### Подписки ###################//
                if ($row['user_subscriptions_num'] > 0) {
//                    $tpl->result['subscriptions'] = mozg_cache('/subscr_user_' . $id);
//                    if (!$tpl->result['subscriptions']) {
                    /** @var array $sql_subscriptions */
                    $sql_subscriptions = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo, user_country_city_name, user_status FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 1 ORDER by `friends_date` DESC LIMIT 0,5", true);
                    $user_subscriptions = [];
                    foreach ($sql_subscriptions as $key => $row_subscr) {
                        $user_subscriptions[$key]['user_id'] = $row_subscr['friend_id'];
                        $user_subscriptions[$key]['name'] = $row_subscr['user_search_pref'];
                        $user_subscriptions[$key]['info'] = '';
                        if ($row_subscr['user_status']) {
                            $user_subscriptions[$key]['info'] = stripslashes(iconv_substr($row_subscr['user_status'], 0, 24, 'utf-8'));
                        } else {
                            $country_city = explode('|', $row_subscr['user_country_city_name']);
                            $user_subscriptions[$key]['info'] = $country_city[1];
                        }
                        if ($row_subscr['user_photo']) {
                            $user_subscriptions[$key]['ava'] = $config['home_url'] . 'uploads/users/' . $row_subscr['friend_id'] . '/50_' . $row_subscr['user_photo'];
                        } else {
                            $user_subscriptions[$key]['ava'] = '/images/no_ava_50.png';
                        }
                    }
                    $params['subscriptions'] = $user_subscriptions;
                    $params['subscriptions_num'] = $row['user_subscriptions_num'];
//                        mozg_create_cache('/subscr_user_' . $id, $tpl->result['subscriptions']);
//                    }
                } else {
                    $params['subscriptions'] = false;
                }

                //################### Музыка ###################//
                if ($row['user_audio']) {
                    /** @var array $sql_audio */
                    $sql_audio = $db->super_query("SELECT id, url, artist, title, duration FROM `audio` WHERE oid = '{$id}' and public = '0' ORDER by `id` DESC LIMIT 0, 3", 1);
                    /*foreach($sql_audio as $row_audio){
                        $stime = gmdate('i:s', $row_audio['duration']);
                        if(!$row_audio['artist']) {
                            $row_audio['artist'] = 'Неизвестный исполнитель';
                        }
                        if(!$row_audio['title']) {
                            $row_audio['title'] = 'Без названия';
                        }
                        $search_artist = urlencode($row_audio['artist']);
                        $plname = 'audios'.$id;
                        $tpl->result['audios'] .= <<<HTML
<div class="audioPage audioElem" id="audio_{$row_audio['id']}_{$id}_{$plname}" onclick="playNewAudio('{$row_audio['id']}_{$id}_{$plname}', event);">
<div class="area">
<table cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
<td>
<div class="audioPlayBut new_play_btn"><div class="bl"><div class="figure"></div></div></div>
<input type="hidden" value="{$row_audio['url']},{$row_audio['duration']},page" id="audio_url_{$row_audio['id']}_{$id}_{$plname}">
</td>
<td class="info">
<div class="audioNames"><b class="author" onclick="Page.Go('/?go=search&query={$search_artist}&type=5&n=1'); return false;" id="artist">{$row_audio['artist']}</b>  –  <span class="name" id="name">{$row_audio['title']}</span> <div class="clear"></div></div>
<div class="audioElTime" id="audio_time_{$row_audio['id']}_{$id}_{$plname}">{$stime}</div>
</td>
</tr>
</tbody>
</table>
<div id="player{$row_audio['id']}_{$id}_{$plname}" class="audioPlayer" border="0" cellpadding="0">
<table cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
<td style="width: 100%;">
<div class="progressBar fl_l" style="width: 100%;" onclick="cancelEvent(event);" onmousedown="audio_player.progressDown(event, this);" id="no_play" onmousemove="audio_player.playerPrMove(event, this)" onmouseout="audio_player.playerPrOut()">
<div class="audioTimesAP" id="main_timeView"><div class="audioTAP_strlka">100%</div></div>
<div class="audioBGProgress"></div>
<div class="audioLoadProgress"></div>
<div class="audioPlayProgress" id="playerPlayLine"><div class="audioSlider"></div></div>
</div>
</td>
<td>
<div class="audioVolumeBar fl_l" onclick="cancelEvent(event);" onmousedown="audio_player.volumeDown(event, this);" id="no_play">
<div class="audioTimesAP"><div class="audioTAP_strlka">100%</div></div>
<div class="audioBGProgress"></div>
<div class="audioPlayProgress" id="playerVolumeBar"><div class="audioSlider"></div></div>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
HTML;
                    }*/

                    foreach ($sql_audio as $key => $row_audio) {
                        $sql_audio[$key]['stime'] = gmdate('i:s', (int)$row_audio['duration']);
                        if (!$row_audio['artist']) {
                            $row_audio['artist'] = 'Неизвестный исполнитель';
                        }
                        if (!$row_audio['title']) {
                            $row_audio['title'] = 'Без названия';
                        }
                        $sql_audio[$key]['search_artist'] = urlencode($row_audio['artist']);
                        $sql_audio[$key]['plname'] = 'audios' . $id;


                    }
                    $params['audios_num'] = $row['user_audio'] . ' ' . declWord($row['user_audio'], 'audio');
                    $params['audios'] = $sql_audio;
                }

                $cnt_happfr = 0;
                //################### Праздники друзей ###################//
                if ($user_id == $id && !isset($_SESSION['happy_friends_block_hide'])) {
                    /** @var array $sql_happy_friends */
//                    $sql_happy_friends = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref, user_photo,
//       user_birthday FROM `friends` tb1, `users` tb2
//WHERE tb1.user_id = '" . $id . "' AND tb1.friend_id = tb2.user_id  AND subscriptions = 0
//AND user_day = '" . date('j', $server_time) . "' AND user_month = '" . date('n', $server_time) . "'
//ORDER by `user_last_visit` DESC LIMIT 0, 50", true);

                    $sql_happy_friends = DB::getDB()->run('SELECT tb1.friend_id, tb2.user_search_pref, 
                        user_photo,  user_birthday FROM `friends` tb1, `users` tb2 
                        WHERE tb1.user_id = ? AND tb1.friend_id = tb2.user_id  AND subscriptions = 0 
                        AND user_day = ? AND user_month = ? ORDER by `user_last_visit`
                        DESC LIMIT 0, 50', $id, date('j', $server_time), date('n', $server_time));


                    foreach ($sql_happy_friends as $key => $happy_row_friends) {
                        $cnt_happfr++;
                        $sql_happy_friends[$key]['user_id'] = $happy_row_friends['friend_id'];
                        $sql_happy_friends[$key]['name'] = $happy_row_friends['user_search_pref'];
                        $user_birthday = explode('-', $happy_row_friends['user_birthday']);
                        $sql_happy_friends[$key]['age'] = user_age($user_birthday[0], $user_birthday[1], $user_birthday[2]);
                        if ($happy_row_friends['user_photo']) {
                            $sql_happy_friends[$key]['ava'] = '/uploads/users/' . $happy_row_friends['friend_id'] . '/100_' . $happy_row_friends['user_photo'];
                        } else {
                            $sql_happy_friends[$key]['ava'] = '/images/100_no_ava.png';
                        }
                    }
                    $params['happy_friends'] = $sql_happy_friends;
                    $params['happy_friends_num'] = $cnt_happfr;
                }

                //################### Загрузка стены ###################//
                $params['wall_num'] = $row['user_wall_num'];
                if ($row['user_wall_num']) {
                    if ((new Request)->filter('uid')) {
//                        $meta_tags['title'] = 'walls';

//                        $tpl = new TpLSite(ROOT_DIR . '/templates/' . $config['temp'], $meta_tags);
                    }

//                        $wall = new WallProfile($tpl);


                    /** Показ последних 10 записей */

                    //Если вызвана страница стены, не со страницы юзера
                    if (!isset($id) && !(new Request)->filter('uid')) {
                        $rid = (new Request)->int('rid');

                        $id = (new Request)->int('uid');
                        if (!$id) {
                            $id = $user_id;
                        }

                        $walluid = $id;
                        $page = (new Request)->int('page', 1);
                        $gcount = 10;
                        $limit_page = ($page - 1) * $gcount;

                        //Выводим имя юзера и настройки приватности
                        $row_user = $db->super_query("SELECT user_name, user_wall_num, user_privacy FROM `users` WHERE user_id = '{$id}'");
                        $user_privacy = unserialize($row_user['user_privacy']);

                        if ($row_user) {
                            //ЧС
                            $CheckBlackList = CheckBlackList($id);
                            if (!$CheckBlackList) {
                                //Проверка естьли запрашиваемый юзер в друзьях у юзера который смотрит стр
                                if ($user_id != $id) {
                                    $check_friend = CheckFriends($id);
                                } else {
                                    $check_friend = false;
                                }

                                if ($user_privacy['val_wall1'] == 1 || ($user_privacy['val_wall1'] == 2 && $check_friend) || $user_id == $id) {
                                    $cnt_rec['cnt'] = $row_user['user_wall_num'];
                                } else {
                                    $cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
                                }

                                $type = (new Request)->filter('type');

                                if ($type === 'own') {
                                    $cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
                                    $where_sql = "AND tb1.author_user_id = '{$id}'";
//                                    $tpl->set_block("'\\[record-tab\\](.*?)\\[/record-tab\\]'si", "");
                                    $page_type = '/wall' . $id . '_sec=own&page=';
                                } else if ($type === 'record') {
                                    $where_sql = "AND tb1.id = '{$rid}'";
//                                    $tpl->set('[record-tab]', '');
//                                    $tpl->set('[/record-tab]', '');
                                    $wallAuthorId = $db->super_query("SELECT author_user_id FROM `wall` WHERE id = '{$rid}'");
                                } else {
                                    $type = '';
                                    $where_sql = '';
//                                    $tpl->set_block("'\\[record-tab\\](.*?)\\[/record-tab\\]'si", "");
                                    $page_type = '/wall' . $id . '/page/';
                                }

                                if ($cnt_rec['cnt'] > 0) {
                                    $user_speedbar = 'На стене ' . $cnt_rec['cnt'] . ' ' . declWord($cnt_rec['cnt'], 'rec');
                                }

//                                $tpl->load_template('wall/head.tpl');
//                                $tpl->set('{name}', gramatikName($row_user['user_name']));
//                                $tpl->set('{uid}', $id);
//                                $tpl->set('{rec-id}', $rid);
//                                $tpl->set("{activetab-{$type}}", 'activetab');
//                                $tpl->compile('info');

                                if ($cnt_rec['cnt'] < 1) {
                                    msgbox('', $lang['wall_no_rec'], 'info_2');
                                }

                            } else {
                                $user_speedbar = $lang['error'];
                                msgbox('', $lang['no_notes'], 'info');
                            }
                        } else {
                            msgbox('', $lang['wall_no_rec'], 'info_2');
                        }
                    }

                    $CheckBlackList = $CheckBlackList ?? false;
                    $check_friend = $check_friend ?? false;
                    $user_privacy = $user_privacy ?? null;
                    $user_privacy['val_wall1'] = $user_privacy['val_wall1'] ?? 3;
                    $wallAuthorId = $wallAuthorId ?? null;
                    $wallAuthorId['author_user_id'] = $wallAuthorId['author_user_id'] ?? null;
                    $id = $id ?? null;

                    if (!$CheckBlackList) {
                        $where_sql = $where_sql ?? null;

                        $page = (new Request)->int('page', 1);
                        $gcount = 10;
                        $limit_page = ($page - 1) * $gcount;
                        $limit_select = 10;

                        if ($user_privacy['val_wall1'] == 1 || ($user_privacy['val_wall1'] == 2 && $check_friend) || $user_id == $id) {
//                            $wall_row = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `wall` tb1, `users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 {$where_sql} ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}", true);

                            $wall_row = DB::getDB()->run('SELECT tb1.id, author_user_id, text, add_date, 
                                fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, 
                                tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile 
                                FROM `wall` tb1, `users` tb2 
                                WHERE for_user_id = ? AND tb1.author_user_id = tb2.user_id 
                                  AND tb1.fast_comm_id = 0 ' . $where_sql . '  
                                ORDER by `add_date` DESC LIMIT ' . $limit_page . ' , ' . $limit_select, $id);

                            $Hacking = false;
                        } elseif ($wallAuthorId['author_user_id'] == $id) {
                            $wall_row = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `wall` tb1, `users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 {$where_sql} ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}", true);
                            $Hacking = false;
                        } else {
//                            $wall->query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `wall` tb1, `users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 AND tb1.author_user_id = '{$id}' ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}");
                            $wall_row = $db->super_query("SELECT tb1.id, author_user_id, text, add_date, fasts_num, likes_num, likes_users, tell_uid, type, tell_date, public, attach, tell_comm, tb2.user_photo, user_search_pref, user_last_visit, user_logged_mobile FROM `wall` tb1, `users` tb2 WHERE for_user_id = '{$id}' AND tb1.author_user_id = tb2.user_id AND tb1.fast_comm_id = 0 AND tb1.author_user_id = '{$id}' ORDER by `add_date` DESC LIMIT {$limit_page}, {$limit_select}", true);
                            if ($wallAuthorId['author_user_id']) {
                                $Hacking = true;
                            }
                        }


                        $Hacking = $Hacking ?? false;

                        //Если вызвана страница стены, не со страницы юзера
                        if (!$Hacking) {
                            $rid = $rid ?? null;
                            $walluid = $walluid ?? null;

                            $for_user_id = null;//fixme

                            if ($rid || $walluid || (new Request)->filter('uid')) {
//                                $wall->template('wall/one_record.tpl');
//                                $wall->compile('content');
                                $config = settings_get();
//                                $wall->select($config, $id, $for_user_id, $user_privacy, $check_friend, $user_info);

                                $wall_data = (new Wall())->profile($config, $id, $for_user_id, $user_privacy, $check_friend, $user_info, $wall_row);

                                //FIXME
                                $cnt_rec = $cnt_rec ?? null;
                                $gcount = $gcount ?? null;
                                $page_type = $page_type ?? null;

                                $type = (new Request)->filter('type');

                                if (($cnt_rec['cnt'] > $gcount && $type == '') || $type === 'own') {
                                    navigation($gcount, $cnt_rec['cnt'], $page_type);
                                }

                            } else {
//                                $wall->template('wall/record.tpl');
//                                $wall->compile('wall');
                                $config = settings_get();
//                                $wall->select($config, $id, $for_user_id, $user_privacy, $check_friend, $user_info);
                                $wall_data = (new Wall())->profile($config, $id, $for_user_id, $user_privacy, $check_friend, $user_info, $wall_row);
                            }
                        } else {
                            echo 'Error 500';
                        }
                    }
                }

                $params['wall_records'] = $wall_data ?? [];

                if ($user_id != $id) {
                    if ($user_privacy['val_wall1'] == 3 or $user_privacy['val_wall1'] == 2 and !$check_friend) {
                        $cnt_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE for_user_id = '{$id}' AND author_user_id = '{$id}' AND fast_comm_id = 0");
                        $row['user_wall_num'] = $cnt_rec['cnt'];
                    }
                }

                $row['user_wall_num'] = $row['user_wall_num'] ?? '';
                if ($row['user_wall_num'] > 10) {
                    $params['wall_link'] = true;
                } else {
                    $params['wall_link'] = false;
                }

                //Общие друзья
                if (Registry::get('logged') && $row['user_friends_num'] && $id !== $user_info['user_id']) {
                    /** @var array $count_common */
                    $count_common = $db->super_query("SELECT COUNT(*) AS cnt FROM `friends` tb1 INNER JOIN `friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$id}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0");
                    if ($count_common['cnt']) {
                        /** @var array $sql_mutual */
                        $sql_mutual = $db->super_query("SELECT tb1.friend_id, tb3.user_photo, user_search_pref FROM `users` tb3, `friends` tb1 INNER JOIN `friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$id}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0 AND tb1.friend_id = tb3.user_id ORDER by rand() LIMIT 0, 3", true);
                        foreach ($sql_mutual as $key => $row_mutual) {
                            $friend_info_mutual = explode(' ', $row_mutual['user_search_pref']);
                            $sql_mutual[$key]['user_id'] = $row_mutual['friend_id'];
                            $sql_mutual[$key]['name'] = $friend_info_mutual[0];
                            $sql_mutual[$key]['last_name'] = $friend_info_mutual[1];
                            if ($row_mutual['user_photo']) {
                                $sql_mutual[$key]['ava'] = $config['home_url'] . 'uploads/users/' . $row_mutual['friend_id'] . '/50_' . $row_mutual['user_photo'];
                            } else {
                                $sql_mutual[$key]['ava'] = '/images/no_ava_50.png';
                            }
                        }
                        $params['mutual_friends'] = $sql_mutual;
                    } else {
                        $params['mutual_friends'] = false;
                    }
                } else {
                    $params['mutual_friends'] = false;
                }

                //################### Загрузка самого профиля ###################//
//                $tpl->load_template('profile.tpl');

                $params['user_id'] = $row['user_id'];

                //Страна и город
                $params['country'] = $user_country_city_name_exp['0'];
                $params['country_id'] = $row['user_country'];
                $params['city'] = $user_country_city_name_exp['1'];
                $params['city_id'] = $row['user_city'];

                //Если человек сидит с мобильной версии
                if ($row_online['user_logged_mobile']) {
                    $mobile_icon = '<img src="/images/spacer.gif" class="mobile_online" />';
                } else {
                    $mobile_icon = '';
                }

                if ($row_online['user_last_visit'] >= $online_time) {
                    $lang['online'] = $lang['online'] ?? 'online';
                    $params['online'] = $lang['online'] . $mobile_icon;
                } elseif ((int)$row_online['user_last_visit'] > 0) {
                    $dateTell = megaDate((int)$row_online['user_last_visit']);
                    if ($row['user_sex'] == 2) {
                        $params['online'] = 'последний раз была ' . $dateTell . $mobile_icon;
                    } else {
                        $params['online'] = 'последний раз был ' . $dateTell . $mobile_icon;
                    }
                } else {
                    $params['online'] = '';
                }//FIXME


                /*                if ($row['user_city'] && $row['user_country']) {
                                    $tpl->set('[not-all-city]', '');
                                    $tpl->set('[/not-all-city]', '');
                                } else {
                                    $tpl->set_block("'\\[not-all-city\\](.*?)\\[/not-all-city\\]'si", "");
                                }
                                if ($row['user_country']) {
                                    $tpl->set('[not-all-country]', '');
                                    $tpl->set('[/not-all-country]', '');
                                } else {
                                    $tpl->set_block("'\\[not-all-country\\](.*?)\\[/not-all-country\\]'si", "");
                                }*/

                //Контакты
                $xfields = unserialize($row['user_xfields']);
                $preg_safq_name_exp = explode(', ', 'phone, vk, od, skype, fb, icq, site');
                foreach ($preg_safq_name_exp as $preg_safq_name) {
                    if (isset($xfields[$preg_safq_name]) and $xfields[$preg_safq_name]) {
//                        $tpl->set("[not-contact-{$preg_safq_name}]", '');
//                        $tpl->set("[/not-contact-{$preg_safq_name}]", '');
                    } else {
//                        $tpl->set_block("'\\[not-contact-{$preg_safq_name}\\](.*?)\\[/not-contact-{$preg_safq_name}\\]'si", "");
                    }
                }

                //todo wtf?
                if (!isset($xfields['phone'])) {
                    $xfields['phone'] = '';
                }
                if (!isset($xfields['site'])) {
                    $xfields['site'] = '';
                }
                $params['phone'] = stripslashes($xfields['phone']);
                if (!empty($xfields['site'])) {
                    if (preg_match('/https:\/\//i', $xfields['site'])) {
                        if (preg_match('/\.ru|\.com|\.net|\.su|\.in\.ua|\.ua/i', $xfields['site'])) {
                            $params['site'] = '<a href="' . stripslashes($xfields['site']) . '" target="_blank">' . stripslashes($xfields['site']) . '</a>';
                        } else {
                            $params['site'] = stripslashes($xfields['site']);
                        }
                    } else {
                        $params['site'] = 'https://' . stripslashes($xfields['site']);
                    }
                } else {
                    $params['site'] = '';
                }


                /*                if (!isset($xfields['vk'])) $xfields['vk'] = '';
                                if (!isset($xfields['od'])) $xfields['od'] = '';
                                if (!isset($xfields['fb'])) $xfields['fb'] = '';
                                if (!isset($xfields['skype'])) $xfields['skype'] = '';
                                if (!isset($xfields['icq'])) $xfields['icq'] = '';
                                if (!isset($xfields['phone'])) $xfields['phone'] = '';
                                if (!isset($xfields['site'])) $xfields['site'] = '';

                               $tpl->set('{vk}', '<a href="' . stripslashes($xfields['vk']) . '" target="_blank">' . stripslashes($xfields['vk']) . '</a>');
                                $tpl->set('{od}', '<a href="' . stripslashes($xfields['od']) . '" target="_blank">' . stripslashes($xfields['od']) . '</a>');
                                $tpl->set('{fb}', '<a href="' . stripslashes($xfields['fb']) . '" target="_blank">' . stripslashes($xfields['fb']) . '</a>');
                                $tpl->set('{skype}', stripslashes($xfields['skype']));
                                $tpl->set('{icq}', stripslashes($xfields['icq']));
                                $tpl->set('{phone}', stripslashes($xfields['phone']));*/

//                if (preg_match('/https:\/\//i', $xfields['site'])) {
//                    if (preg_match('/\.ru|\.com|\.net|\.su|\.in\.ua|\.ua/i', $xfields['site'])) {
//                        $params['site'] = '<a href="' . stripslashes($xfields['site']) . '" target="_blank">' . stripslashes($xfields['site']) . '</a>';
//                    } else {
//                        $params['site'] = stripslashes($xfields['site']);
//                    }
//                } else {
//                    $params['site'] = 'https://' . stripslashes($xfields['site']);
//                }

                /*                if (!$xfields['vk'] && !$xfields['od'] && !$xfields['fb'] && !$xfields['skype'] && !$xfields['icq'] && !$xfields['phone'] && !$xfields['site']) {
                                    $tpl->set_block("'\\[not-block-contact\\](.*?)\\[/not-block-contact\\]'si", "");
                                } else {
                                    $tpl->set('[not-block-contact]', '');
                                    $tpl->set('[/not-block-contact]', '');
                                }*/

                //Интересы
                $xfields_all = unserialize($row['user_xfields_all']);

                /*                if (!isset($xfields_all['activity'])) $xfields_all['activity'] = '';
                                if (!isset($xfields_all['interests'])) $xfields_all['interests'] = '';
                                if (!isset($xfields_all['myinfo'])) $xfields_all['myinfo'] = '';
                                if (!isset($xfields_all['music'])) $xfields_all['music'] = '';
                                if (!isset($xfields_all['kino'])) $xfields_all['kino'] = '';
                                if (!isset($xfields_all['books'])) $xfields_all['books'] = '';
                                if (!isset($xfields_all['games'])) $xfields_all['games'] = '';
                                if (!isset($xfields_all['quote'])) $xfields_all['quote'] = '';*/

//                $preg_safq_name_exp = explode(', ', 'activity, interests, myinfo, music, kino, books, games, quote');

//                if ($xfields_all['myinfo']) {
//                    $params['not_block_info'] = '';
//                } else {
                $params['not_block_info'] = '<div align="center" style="color:#999;">Информация отсутствует.</div>';
//                }

//                $params['myinfo'] = nl2br(stripslashes($xfields_all['myinfo']));
                $params['myinfo'] = '';

                $params['name'] = $user_name_lastname_exp[0];
                $params['lastname'] = $user_name_lastname_exp[1];

                //День рождение
                if (!$row['user_birthday'] == '') {
                    $user_birthday = explode('-', $row['user_birthday']);
                    $row['user_day'] = $user_birthday[2];
                    $row['user_month'] = $user_birthday[1];
                    $row['user_year'] = $user_birthday[0];
                } else {
                    $row['user_day'] = '';
                    $row['user_month'] = '';
                    $row['user_year'] = '';
                }


                if ($row['user_day'] > 0 && $row['user_day'] <= 31 && $row['user_month'] > 0 && $row['user_month'] < 13) {
//                    $tpl->set('[not-all-birthday]', '');
//                    $tpl->set('[/not-all-birthday]', '');

                    if ($row['user_day'] && $row['user_month'] && $row['user_year'] > 1929 && $row['user_year'] < 2012) {
                        //                            $tpl->set('{birth-day}', '<a href="/?go=search&day='.$row['user_day'].'&month='.$row['user_month'].'&year='.$row['user_year'].'" onClick="Page.Go(this.href); return false">'.langdate('j F Y', strtotime($row['user_year'].'-'.$row['user_month'].'-'.$row['user_day'])).' г.</a>');
                        $params['birth_day'] = '<a href="/?go=search&day=' . $row['user_day'] . '&month=' . $row['user_month'] . '&year=' . $row['user_year'] . '" onClick="Page.Go(this.href); return false">' . Langs::lang_date('j F Y', strtotime($row['user_year'] . '-' . $row['user_month'] . '-' . $row['user_day'])) . ' г.</a>';
                    } else {
                        $params['birth_day'] = '<a href="/?go=search&day=' . $row['user_day'] . '&month=' . $row['user_month'] . '" onClick="Page.Go(this.href); return false">' . Langs::lang_date('j F', strtotime($row['user_year'] . '-' . $row['user_month'] . '-' . $row['user_day'])) . '</a>';
                    }
                } else {
//                    $tpl->set_block("'\\[not-all-birthday\\](.*?)\\[/not-all-birthday\\]'si", "");
                }

                //Показ скрытых текста только для владельца страницы
                if ($user_info['user_id'] == $row['user_id']) {
                    $params['owner'] = true;
                } else {
                    $params['owner'] = false;
                }

                //Аватарка
//                $row_view_photos = $db->super_query("SELECT * FROM `photos` WHERE user_id = '{$id}'");

                $row_view_photos = DB::getDB()->row('SELECT * FROM `photos` WHERE user_id = ?', $id);

                $params['photoid'] = $row_view_photos['id'] ?? 0;
                $params['albumid'] = $row_view_photos['album_id'] ?? 0;
                if ($row['user_photo']) {
                    //todo optimize
                    $album = $db->super_query("SELECT aid FROM `albums` WHERE user_id = '{$id}' AND system = '1'");
                    $albuml = $db->super_query("SELECT * FROM `photos` WHERE album_id = '{$album['aid']}' ORDER BY id DESC");
                    $params['ava'] = $config['home_url'] . 'uploads/users/' . $row['user_id'] . '/' . $row['user_photo'];
                    $params['display_ava'] = 'style="display:block;"';
                    $params['link'] = '/photo' . $row['user_id'] . '_' . $albuml['id'] . '_' . $albuml['album_id'];
                } else {
                    $params['ava'] = '/images/no_ava.gif';
                    $params['display_ava'] = 'style="display:none;"';
                }

                //Проверка пользователя
                if ($row['user_real'] == 1) {
                    $params['user_real'] = '<img style="margin-left:5px" src="/images/icons/verifi.png" title="Подтверждённый пользователь">';
                } else {
                    $params['user_real'] = '';
                }

                //################### Альбомы ###################//
                if ($user_id == $id) {
                    $albums_privacy = false;
                    $albums_count['cnt'] = $row['user_albums_num'];
                    $cache_pref = '';
                } elseif (isset($check_friend) and $check_friend) {
                    $albums_privacy = "AND SUBSTRING(privacy, 1, 1) regexp '[[:<:]](1|2)[[:>:]]'";
                    $albums_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `albums` WHERE user_id = '{$id}' {$albums_privacy}", false);
                    $cache_pref = "_friends";
                } else {
                    $albums_privacy = "AND SUBSTRING(privacy, 1, 1) = 1";
                    $albums_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `albums` WHERE user_id = '{$id}' {$albums_privacy}", false);
                    $cache_pref = "_all";
                }

//                $sql_albums = $db->super_query("SELECT aid, name, adate, photo_num, cover FROM `albums` WHERE user_id = '{$id}' {$albums_privacy} ORDER by `position` ASC LIMIT 0, 4", true);
                $sql_albums = DB::getDB()->run('SELECT aid, name, adate, photo_num, cover FROM `albums` WHERE user_id = ? ' . $albums_privacy . ' ORDER by `position` ASC LIMIT 0, 4', $id);

                if ($sql_albums && $albums_count['cnt'] && $config['album_mod'] == 'yes') {
                    foreach ($sql_albums as $key2 => $row_albums) {
                        $sql_albums[$key2]['name'] = stripslashes($row_albums['name']);
                        $sql_albums[$key2]['date'] = megaDate((int)$row_albums['adate']);
                        $sql_albums[$key2]['albums_photonums'] = declWord($row_albums['photo_num'], 'photos');
                        if ($row_albums['cover']) {
                            $sql_albums[$key2]['album_cover'] = "/uploads/users/{$id}/albums/{$row_albums['aid']}/c_{$row_albums['cover']}";
                        } else {
                            $sql_albums[$key2]['album_cover'] = '/images/no_cover.png';
                        }
                    }
                    $params['albums_num'] = $albums_count['cnt'];
                    $params['albums'] = $sql_albums;
                }

                //Делаем проверки на существования запрашиваемого юзера у себя в друзьях, закладках, в подписках, делаем всё это если страницу смотрит другой человек
                if ($user_id !== $id) {
                    //Проверка есть ли запрашиваемый юзер в друзьях у юзера который смотрит стр
                    if ($check_friend) {
                        $params['yes_friends'] = true;
                    } else {
                        $params['yes_friends'] = false;
                    }

                    //Проверка есть ли запрашиваемый юзер в закладках у юзера который смотрит стр
                    if (Registry::get('logged')) {
                        $check_fave = $db->super_query("SELECT user_id FROM `fave` WHERE user_id = '{$user_info['user_id']}' AND fave_id = '{$id}'");
                        if ($check_fave) {
                            $params['yes_fave'] = true;
                        } else {
                            $params['yes_fave'] = false;
                        }
                    } else {
                        $params['yes_fave'] = false;
                    }

                    //Проверка есть ли запрашиваемый юзер в подписках у юзера который смотрит стр
                    if (Registry::get('logged')) {
                        $check_subscr = $db->super_query("SELECT user_id FROM `friends` WHERE user_id = '{$user_info['user_id']}' AND friend_id = '{$id}' AND subscriptions = 1");
                        if ($check_subscr) {
                            $params['yes_subscription'] = true;
                        } else {
                            $params['yes_subscription'] = false;
                        }
                    } else {
                        $params['yes_subscription'] = false;
                    }


                    //Проверка есть ли запрашиваемый юзер в черном списке
                    if (Registry::get('logged')) {
                        $MyCheckBlackList = MyCheckBlackList($id);
                    } else {
                        $MyCheckBlackList = false;
                    }
                    if ($MyCheckBlackList) {
                        $params['yes_blacklist'] = true;
                    } else {
                        $params['yes_blacklist'] = false;
                    }
                }

                $author_info = explode(' ', $row['user_search_pref']);
                $params['gram_name'] = grammaticalName($author_info[0]);

//                $tpl->set('{friends-num}', $row['user_friends_num']);
                if (!isset($online_friends['cnt'])) {
                    $online_friends['cnt'] = '';
                }
//                $tpl->set('{online-friends-num}', $online_friends['cnt']);
//                $tpl->set('{notes-num}', $row['user_notes_num']);
//                $tpl->set('{subscriptions-num}', $row['user_subscriptions_num']);
//                $tpl->set('{videos-num}', $row['user_videos_num']);


                //Если человек пришел после реги, то открываем ему окно загрузи фотографии
//                if (intFilter('after')) {
//                    $tpl->set('[after_reg]', '');
//                    $tpl->set('[/after_reg]', '');
//                } else
//                    $tpl->set_block("'\\[after_reg\\](.*?)\\[/after_reg\\]'si", "");

                //Стена
//                $tpl->set('{records}', $tpl->result['wall'] ?? '');



//                $tpl->set('{wall-rec-num}', $row['user_wall_num']);

//                if ($row['user_wall_num']) {
//                    $tpl->set_block("'\\[no-records\\](.*?)\\[/no-records\\]'si", "");
//                } else {
//                    $tpl->set('[no-records]', '');
//                    $tpl->set('[/no-records]', '');
//                }

                //Статус
                $params['status_text'] = stripslashes($row['user_status']);

                //Приватность сообщений
                if ($user_privacy['val_msg'] == 1 || ($user_privacy['val_msg'] == 2 && $check_friend)) {
                    $params['privacy_msg'] = true;
                } else {
                    $params['privacy_msg'] = false;
                }

                //Приватность стены
                if ($user_id === $id) {
                    $params['privacy_wall'] = true;
                } else {
                    if ($user_privacy['val_wall1'] == 1 || ($user_privacy['val_wall1'] == 2 && $check_friend)) {
                        $params['privacy_wall'] = true;
                    } else {
                        $params['privacy_wall'] = false;
                    }

                    if ($user_privacy['val_wall2'] == 1 || ($user_privacy['val_wall2'] == 2 && $check_friend)) {
                        $params['privacy_wall'] = true;
                    } else {
                        $params['privacy_wall'] = false;
                    }
                }

                //Приватность информации
                if ($user_privacy['val_info'] == 1 || ($user_privacy['val_info'] == 2 && $check_friend) || $user_id == $id) {
                    $params['privacy_info'] = true;
                } else {
                    $params['privacy_info'] = false;
                }

                //Семейное положение
                $user_sp = explode('|', $row['user_sp']);
                if (isset($user_sp[1]) && $user_sp[1]) {
                    $rowSpUserName = $db->super_query("SELECT user_search_pref, user_sp, user_sex FROM `users` WHERE user_id = '{$user_sp[1]}'");
                    if ($row['user_sex'] == 1) {
                        $check_sex = 2;
                    } elseif ($row['user_sex'] == 2) {
                        $check_sex = 1;
                    } else {
                        $check_sex = null;
                    }

                    if ($rowSpUserName['user_sp'] == $user_sp[0] . '|' . $id or $user_sp[0] == 5 and $rowSpUserName['user_sex'] == $check_sex) {
                        $spExpName = explode(' ', $rowSpUserName['user_search_pref']);
                        $spUserName = $spExpName[0] . ' ' . $spExpName[1];
                    } else {
                        $spUserName = '';
                    }
                } else {
                    $spUserName = '';
                }
                if ($row['user_sex'] == 1) {
                    $sp1 = '<a href="/?go=search&sp=1" onClick="Page.Go(this.href); return false">не женат</a>';
                    $sp2 = "подруга <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp2_2 = '<a href="/?go=search&sp=2" onClick="Page.Go(this.href); return false">есть подруга</a>';
                    $sp3 = "невеста <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp3_3 = '<a href="/?go=search&sp=3" onClick="Page.Go(this.href); return false">помовлен</a>';
                    $sp4 = "жена <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp4_4 = '<a href="/?go=search&sp=4" onClick="Page.Go(this.href); return false">женат</a>';
                    $sp5 = "любимая <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp5_5 = '<a href="/?go=search&sp=5" onClick="Page.Go(this.href); return false">влюблён</a>';
                } elseif ($row['user_sex'] == 2) {
                    $sp1 = '<a href="/?go=search&sp=1" onClick="Page.Go(this.href); return false">не замужем</a>';
                    $sp2 = "друг <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp2_2 = '<a href="/?go=search&sp=2" onClick="Page.Go(this.href); return false">есть друг</a>';
                    $sp3 = "жених <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp3_3 = '<a href="/?go=search&sp=3" onClick="Page.Go(this.href); return false">помовлена</a>';
                    $sp4 = "муж <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp4_4 = '<a href="/?go=search&sp=4" onClick="Page.Go(this.href); return false">замужем</a>';
                    $sp5 = "любимый <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                    $sp5_5 = '<a href="/?go=search&sp=5" onClick="Page.Go(this.href); return false">влюблена</a>';
                } else {
                    $sp5_5 = '';
                    $sp5 = '';
                    $sp4_4 = '';
                    $sp4 = '';
                    $sp3_3 = '';
                    $sp3 = '';
                    $sp2_2 = '';
                    $sp2 = '';
                    $sp1 = '';
                }

                $user_sp[1] = $user_sp[1] ?? '';
                $sp6 = "партнёр <a href=\"/u{$user_sp[1]}\" onClick=\"Page.Go(this.href); return false\">{$spUserName}</a>";
                $sp6_6 = '<a href="/?go=search&sp=6" onClick="Page.Go(this.href); return false">всё сложно</a>';
                if ($user_sp[0] == 1) {
                    $params['sp'] = $sp1;
                } elseif ($user_sp[0] == 2) {
                    $params['sp'] = $spUserName ? $sp2 : $sp2_2;
                } elseif ($user_sp[0] == 3) {
                    $params['sp'] = $spUserName ? $sp3 : $sp3_3;
                } elseif ($user_sp[0] == 4) {
                    $params['sp'] = $spUserName ? $sp4 : $sp4_4;
                } elseif ($user_sp[0] == 5) {
                    $params['sp'] = $spUserName ? $sp5 : $sp5_5;
                } else if ($user_sp[0] == 6) {
                    $params['sp'] = $spUserName ? $sp6 : $sp6_6;
                } else if ($user_sp[0] == 7) {
                    $params['sp'] = '<a href="/?go=search&sp=7" onClick="Page.Go(this.href); return false">в активном поиске</a>';
                } else {
                    $params['sp'] = false;
                }

                //ЧС
//                if (!$CheckBlackList) {
//                    $params['blacklist'] = true;
//                } else {
//                    $params['blacklist'] = false;
//                }

                //################### Подарки ###################//
                if ($row['user_gifts']) {
                    $sql_gifts = $db->super_query("SELECT gift FROM `gifts` WHERE uid = '{$id}' ORDER by `gdate` DESC LIMIT 0, 5", true);
                    $params['gifts_num'] = $row['user_gifts'] . ' ' . declWord($row['user_gifts'], 'gifts');
                    $params['gifts'] = $sql_gifts;
                }

                //################### Интересные Группы ###################//
                if ($row['user_public_num']) {
                    $sql_groups = $db->super_query("SELECT tb1.friend_id, tb2.id, title, photo, adres, status_text FROM `friends` tb1, `communities` tb2 WHERE tb1.user_id = '{$id}' AND tb1.friend_id = tb2.id AND tb1.subscriptions = 2 ORDER by `traf` DESC LIMIT 0, 5", true);
                    $all_groups = [];
                    foreach ($sql_groups as $key => $row_groups) {
                        if (!empty($row_groups['adres'])) {
                            $all_groups[$key]['adres'] = $row_groups['adres'];
                        } else {
                            $all_groups[$key]['adres'] = 'public' . $row_groups['id'];
                        }
                        if (!empty($row_groups['photo'])) {
                            $all_groups[$key]['ava'] = "/uploads/groups/{$row_groups['id']}/50_{$row_groups['photo']}";
                        } else {
                            $all_groups[$key]['ava'] = '/images/no_ava_50.png';
                        }
                        $all_groups[$key]['info'] = iconv_substr($row_groups['status_text'], 0, 24, 'utf-8');
                        $all_groups[$key]['user_id'] = $row_groups['id'];
                        $all_groups[$key]['name'] = $row_groups['title'];
                        $all_groups[$key]['title'] = $row_groups['title'];
                    }
                    $params['groups'] = $all_groups;
                    $params['groups_num'] = $row['user_public_num'];
                }

                //################### Музыка ###################//
                /*                if ($row['user_audio'] && $config['audio_mod'] == 'yes') {
                                    $tpl->set('{audios-num}', $row['user_audio'] . ' ' . declWord($row['user_audio'], 'audio'));
                                } else {
                                }*/


                //################### Обработка дополнительных полей ###################//
                /*                $xfieldsdata = xfieldsdataload($row['xfields']);
                                $xfields = profileload();
                                foreach ($xfields as $value) {
                                    $preg_safe_name = preg_quote($value[0], "'");
                                    if (empty($xfieldsdata[$value[0]])) {
                                        $tpl->copy_template = preg_replace("'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template);
                                    } else {
                                        $tpl->copy_template = str_replace("[xfgiven_{$preg_safe_name}]", "", $tpl->copy_template);
                                        $tpl->copy_template = str_replace("[/xfgiven_{$preg_safe_name}]", "", $tpl->copy_template);
                                    }
                                    $tpl->copy_template = preg_replace("'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes($xfieldsdata[$value[0]]), $tpl->copy_template);
                                }*/

                //Фотография профиля
                /*                if ($row['user_photo']) {
                                    $avaImgIsinfo = getimagesize(ROOT_DIR . "/uploads/users/{$row['user_id']}/{$row['user_photo']}");
                                    if ($avaImgIsinfo[1] < 200) {
                                        $rForme = $avaImgIsinfo[1] * 100 / 230 * 2;
                                        $ava_marg_top = 'style="margin-top:-' . $rForme . 'px"';
                                    } else {
                                        $ava_marg_top = '';
                                    }
                                    $tpl->set('{cover-param-7}', $ava_marg_top);
                                } else {
                                    $tpl->set('{cover-param-7}', '');
                                }*/

                //Rating
                if ($row['user_rating'] > 1000) {
                    $params['rating_class_left'] = 'profile_rate_1000_left';
                    $params['rating_class_right'] = 'profile_rate_1000_right';
                    $params['rating_class_head'] = 'profile_rate_1000_head';
                } elseif ($row['user_rating'] > 500) {
                    $params['rating_class_left'] = 'profile_rate_500_left';
                    $params['rating_class_right'] = 'profile_rate_500_right';
                    $params['rating_class_head'] = 'profile_rate_500_head';
                } else {
                    $params['rating_class_left'] = '';
                    $params['rating_class_right'] = '';
                    $params['rating_class_head'] = '';
                }

                if (!$row['user_rating']) {
                    $row['user_rating'] = 0;
                }
                $params['rating'] = $row['user_rating'];

//                $tpl->compile('content');

                //Обновляем кол-во посещений на страницу, если юзер есть у меня в друзьях
                if (isset($check_friend) && $check_friend) {
                    $db->query("UPDATE LOW_PRIORITY `friends` SET views = views+1 WHERE user_id = '{$user_info['user_id']}' AND friend_id = '{$id}' AND subscriptions = 0");
                }

                //Вставляем в статистику
                if (Registry::get('logged') && $user_info['user_id'] != $id) {
                    $stat_date = date('Ymd', $server_time);
                    $stat_x_date = date('Ym', $server_time);
                    $check_user_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `users_stats_log` WHERE user_id = '{$user_info['user_id']}' AND for_user_id = '{$id}' AND date = '{$stat_date}'");
                    if (!$check_user_stat['cnt']) {
                        $check_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `users_stats` WHERE user_id = '{$id}' AND date = '{$stat_date}'");
                        if ($check_stat['cnt']) {
                            $db->query("UPDATE `users_stats` SET users = users + 1, views = views + 1 WHERE user_id = '{$id}' AND date = '{$stat_date}'");
                        } else {
                            $db->query("INSERT INTO `users_stats` SET user_id = '{$id}', date = '{$stat_date}', users = '1', views = '1', date_x = '{$stat_x_date}'");
                        }
                        $db->query("INSERT INTO `users_stats_log` SET user_id = '{$user_info['user_id']}', date = '{$stat_date}', for_user_id = '{$id}'");
                    } else {
                        $db->query("UPDATE `users_stats` SET views = views + 1 WHERE user_id = '{$id}' AND date = '{$stat_date}'");
                    }
                }

                view('profile.profile', $params);
            }

//            $tpl->render();
        } else {

//            msgBoxNew($tpl, 'Информация', 'no_upage', 'info.tpl');
        }

//    $tpl->clear();
//	$db->free();

    }
}