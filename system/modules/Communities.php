<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\modules;

use FluffyDollop\Support\Registry;
use Mozg\classes\{Module, TpLSite, WallPublic};

class Communities extends Module
{
    function main(): void
    {
        NoAjaxQuery();

        if (Registry::get('logged')) {
            $db = Registry::get('db');
            $register = new \FluffyDollop\Registry\Registry();
            $lang = $register->get('lang');
            $user_info = Registry::get('user_info');
            $server_time = Registry::get('server_time');
            $config = settings_get();

            $user_id = $user_info['user_id'];
            $pid = (new \FluffyDollop\Http\Request)->int('pid');
            $mobile_speedbar = 'Сообщество';
            $get_adres = (new \FluffyDollop\Http\Request)->filter('get_adres', 100);

            //Если страница вызвана через "к предыдущим записям"
            $limit_select = 10;
            $page_cnt = (new \FluffyDollop\Http\Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt *= $limit_select;
            }

            if ($page_cnt > 0) {
                $row = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");
                $row['id'] = $pid;
            } else {

                if ($pid !== 0) {
                    $row = $db->super_query("SELECT id, title, descr, traf, ulist, photo, date, admin, feedback, comments, real_admin, rec_num, del, ban, adres, audio_num, forum_num, discussion, status_text, web, videos_num FROM `communities` WHERE id = '" . $pid . "'");
                } elseif ($get_adres && str_contains($get_adres, 'public')) {
                    $pid = str_replace('public', '', $get_adres);
                    $row = $db->super_query("SELECT id, title, descr, traf, ulist, photo, date, admin, feedback, comments, real_admin, rec_num, del, ban, adres, audio_num, forum_num, discussion, status_text, web, videos_num FROM `communities` WHERE id = '" . $pid . "'");
                } else {
                    //FIXME
                    echo $get_adres;
                    $row = $db->super_query("SELECT id, title, descr, traf, ulist, photo, date, admin, feedback, comments, real_admin, rec_num, del, ban, adres, audio_num, forum_num, discussion, status_text, web, videos_num FROM `communities` WHERE id = '" . $pid . "'");
                }
            }

            $meta_tags['title'] = $row['title'] ?? 'ttt';
            $tpl = new TpLSite($this->tpl_dir_name, $meta_tags);

            if (isset($row['del']) && $row['del'] == 1) {
                $user_speedbar = 'Страница удалена';
                msgbox('', '<br /><br />Сообщество удалено администрацией.<br /><br /><br />', 'info_2');
            } elseif (isset($row['ban']) && $row['ban'] == 1) {
                $user_speedbar = 'Страница заблокирована';
                msgbox('', '<br /><br />Сообщество заблокировано администрацией.<br /><br /><br />', 'info_2');
            } elseif ($row) {
                $metatags['title'] = stripslashes($row['title']);
                $user_speedbar = $lang['public_spbar'];

                if (stripos($row['admin'], "u{$user_id}|") !== false)
                    $public_admin = true;
                else
                    $public_admin = false;

                //Стена
                //Если страница вывзана через "к предыдущим записям"
                if ($page_cnt)
                    NoAjaxQuery();

                $wall = new WallPublic($tpl);
                $wall->query("SELECT tb1.id, text, public_id, add_date, fasts_num, attach, likes_num, likes_users, tell_uid, public, tell_date, tell_comm, fixed, tb2.title, photo, comments, adres FROM `communities_wall` tb1, `communities` tb2 WHERE tb1.public_id = '{$row['id']}' AND tb1.public_id = tb2.id AND fast_comm_id = 0 ORDER by `fixed` DESC, `add_date` DESC LIMIT {$page_cnt}, {$limit_select}");
                $wall->template('groups/record.tpl');
                //Если страница вызвана через "к предыдущим записям"
                if ($page_cnt)
                    $wall->compile('content');
                else
                    $wall->compile('wall');
                $wall->select($public_admin, $server_time);

                //Если страница вызвана через "к предыдущим записям"
                if ($page_cnt) {
                    AjaxTpl($tpl);
                    exit;//fixme
                }

                $tpl->load_template('public/main.tpl');

                $tpl->set('{title}', stripslashes($row['title']));

                if ($row['photo']) {

                    //FOR MOBILE VERSION 1.0
                    if ($config['temp'] == 'mobile')

                        $row['photo'] = '50_' . $row['photo'];

                    $tpl->set('{photo}', "/uploads/groups/{$row['id']}/{$row['photo']}");
                    $tpl->set('{display-ava}', '');
                } else {

                    //FOR MOBILE VERSION 1.0
                    if ($config['temp'] == 'mobile')

                        $tpl->set('{photo}', "/images/no_ava_50.png");

                    else

                        $tpl->set('{photo}', "/images/no_ava.gif");

                    $tpl->set('{display-ava}', 'no_display');
                }

                if ($row['descr'])
                    $tpl->set('{descr-css}', '');
                else
                    $tpl->set('{descr-css}', 'no_display');

                $tpl->set('{edit-descr}', myBrRn(stripslashes($row['descr'])));

                //КНопка Показать полностью..
                $expBR = explode('<br />', $row['descr']);
                $textLength = count($expBR);
                $strTXT = strlen($row['descr']);
                if ($textLength > 9 or $strTXT > 600)
                    $row['descr'] = '<div class="wall_strlen" id="hide_wall_rec' . $row['id'] . '">' . $row['descr'] . '</div><div class="wall_strlen_full" onMouseDown="wall.FullText(' . $row['id'] . ', this.id)" id="hide_wall_rec_lnk' . $row['id'] . '">Показать полностью..</div>';

                $tpl->set('{descr}', stripslashes($row['descr']));

                $tpl->set('{num}', '<span id="traf">' . $row['traf'] . '</span> ' . declWord($row['traf'], 'subscribers'));
                if ($row['traf']) {
                    $tpl->set('{num-2}', '<a href="/public' . $row['id'] . '" onClick="groups.all_people(\'' . $row['id'] . '\'); return false">' . declWord($row['traf'], 'subscribers2') . ' ' . $row['traf'] . ' ' . declWord($row['traf'], 'subscribers3') . '</a>');
                    $tpl->set('{no-users}', '');
                } else {
                    $tpl->set('{num-2}', '<span class="color777">Вы будете первым.</span>');
                    $tpl->set('{no-users}', 'no_display');
                }

                //Права админа
                if ($public_admin) {
                    $tpl->set('[admin]', '');
                    $tpl->set('[/admin]', '');
                    $tpl->set_block("'\\[not-admin\\](.*?)\\[/not-admin\\]'si", "");
                } else {
                    $tpl->set('[not-admin]', '');
                    $tpl->set('[/not-admin]', '');
                    $tpl->set_block("'\\[admin\\](.*?)\\[/admin\\]'si", "");
                }

                //Проверка подписан юзер или нет
                if (stripos($row['ulist'], "|{$user_id}|") !== false)
                    $tpl->set('{yes}', 'no_display');
                else
                    $tpl->set('{no}', 'no_display');

                //Контакты
                if ($row['feedback']) {
                    $tpl->set('[yes]', '');
                    $tpl->set('[/yes]', '');
                    $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");
                    $tpl->set('{num-feedback}', '<span id="fnumu">' . $row['feedback'] . '</span> ' . declWord($row['feedback'], 'feedback'));
                    $sql_feedbackusers = $db->super_query("SELECT tb1.fuser_id, office, tb2.user_search_pref, user_photo FROM `communities_feedback` tb1, `users` tb2 WHERE tb1.cid = '{$row['id']}' AND tb1.fuser_id = tb2.user_id ORDER by `fdate` ASC LIMIT 0, 5", true);
                    $feedback_users = '';
                    foreach ($sql_feedbackusers as $row_feedbackusers) {
                        if ($row_feedbackusers['user_photo']) $ava = "/uploads/users/{$row_feedbackusers['fuser_id']}/50_{$row_feedbackusers['user_photo']}";
                        else $ava = "/images/no_ava_50.png";
                        $row_feedbackusers['office'] = stripslashes($row_feedbackusers['office']);
                        $feedback_users .= "<div class=\"onesubscription onesubscriptio2n\" id=\"fb{$row_feedbackusers['fuser_id']}\"><a href=\"/u{$row_feedbackusers['fuser_id']}\" onClick=\"Page.Go(this.href); return false\"><img src=\"{$ava}\" alt=\"\" /><div class=\"onesubscriptiontitle\">{$row_feedbackusers['user_search_pref']}</div></a><div class=\"nesubscriptstatus\">{$row_feedbackusers['office']}</div></div>";
                    }
                    $tpl->set('{feedback-users}', $feedback_users);
                    $tpl->set('[feedback]', '');
                    $tpl->set('[/feedback]', '');
                } else {
                    $tpl->set('[no]', '');
                    $tpl->set('[/no]', '');
                    $tpl->set_block("'\\[yes\\](.*?)\\[/yes\\]'si", "");
                    $tpl->set('{feedback-users}', '');
                    if ($public_admin) {
                        $tpl->set('[feedback]', '');
                        $tpl->set('[/feedback]', '');
                    } else
                        $tpl->set_block("'\\[feedback\\](.*?)\\[/feedback\\]'si", "");
                }

                //Выводим подписчиков
                $sql_users = $db->super_query("SELECT tb1.user_id, tb2.user_name, user_lastname, user_photo FROM `friends` tb1, `users` tb2 WHERE tb1.friend_id = '{$row['id']}' AND tb1.user_id = tb2.user_id AND tb1.subscriptions = 2 ORDER by rand() LIMIT 0, 6", true);
                $users = '';
                foreach ($sql_users as $row_users) {
                    if ($row_users['user_photo']) $ava = "/uploads/users/{$row_users['user_id']}/50_{$row_users['user_photo']}";
                    else $ava = "/images/no_ava_50.png";
                    $users .= "<div class=\"onefriend oneusers\" id=\"subUser{$row_users['user_id']}\"><a href=\"/u{$row_users['user_id']}\" onClick=\"Page.Go(this.href); return false\"><img src=\"{$ava}\"  style=\"margin-bottom:3px\" /></a><a href=\"/u{$row_users['user_id']}\" onClick=\"Page.Go(this.href); return false\">{$row_users['user_name']}<br /><span>{$row_users['user_lastname']}</span></a></div>";
                }
                $tpl->set('{users}', $users);

                $tpl->set('{id}', $row['id']);
                $date_str = megaDate(strtotime($row['date']), 1, 1);
                $tpl->set('{date}', $date_str);
                //Комментарии включены
                if ($row['comments'])
                    $tpl->set('{settings-comments}', 'comments');
                else
                    $tpl->set('{settings-comments}', 'none');

                //Выводим админов при ред. страницы
                if ($public_admin) {
                    $admins_arr = str_replace('|', '', explode('u', $row['admin']));
                    $adminO = '';
                    foreach ($admins_arr as $admin_id) {
                        if ($admin_id) {
                            $row_admin = $db->super_query("SELECT user_search_pref, user_photo FROM `users` WHERE user_id = '{$admin_id}'");
                            if ($row_admin['user_photo']) $ava_admin = "/uploads/users/{$admin_id}/50_{$row_admin['user_photo']}";
                            else $ava_admin = "/images/no_ava_50.png";
                            if ($admin_id != $row['real_admin'])
                                $admin_del_href = "<a href=\"/\" onClick=\"groups.deladmin('{$row['id']}', '{$admin_id}'); return false\"><small>Удалить</small></a>";
                            else
                                $admin_del_href = '';
                            $adminO .= "<div class=\"public_oneadmin\" id=\"admin{$admin_id}\"><a href=\"/u{$admin_id}\" onClick=\"Page.Go(this.href); return false\"><img src=\"{$ava_admin}\" align=\"left\" width=\"32\" /></a><a href=\"/u{$admin_id}\" onClick=\"Page.Go(this.href); return false\">{$row_admin['user_search_pref']}</a><br />{$admin_del_href}</div>";
                        }
                    }

                    $tpl->set('{admins}', $adminO);
                }

                $wall_response = $tpl->result['wall'] ?? '';
                $tpl->set('{records}', $wall_response);

                //Стена
                if ($row['rec_num'] > 10)
                    $tpl->set('{wall-page-display}', '');
                else
                    $tpl->set('{wall-page-display}', 'no_display');

                if ($row['rec_num'])
                    $tpl->set('{rec-num}', '<b id="rec_num">' . $row['rec_num'] . '</b> ' . declWord($row['rec_num'], 'rec'));
                else {
                    $tpl->set('{rec-num}', '<b id="rec_num">Нет записей</b>');
                    if ($public_admin)
                        $tpl->set('{records}', '<div class="wall_none" style="border-top:0px">Новостей пока нет.</div>');
                    else
                        $tpl->set('{records}', '<div class="wall_none">Новостей пока нет.</div>');
                }

                //Выводим информацию о том кто смотрит страницу для себя
                $tpl->set('{viewer-id}', $user_id);

                if (!$row['adres']) $row['adres'] = 'public' . $row['id'];
                $tpl->set('{adres}', $row['adres']);

                //Аудиозаписи
                if($row['audio_num']){
                    $sql_audios = $db->super_query("SELECT id, url, artist, title, duration FROM `audio` WHERE oid = '{$row['id']}' and public = '1' ORDER by `id` DESC LIMIT 0, 3", 1);
                    foreach($sql_audios as $row_audio){
                        $stime = gmdate('i:s', $row_audio['duration']);
                        if(!$row_audio['artist']) {
                            $row_audio['artist'] = 'Неизвестный исполнитель';
                        }
                        if(!$row_audio['title']) {
                            $row_audio['title'] = 'Без названия';
                        }
                        $search_artist = urlencode($row_audio['artist']);
                        $plname = 'publicaudios'.$row['id'];
                        $audios .= <<<HTML
<div class="audioPage audioElem" id="audio_{$row_audio['id']}_{$row['id']}_{$plname}" onclick="playNewAudio('{$row_audio['id']}_{$row['id']}_{$plname}', event);">
<div class="area">
<table cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
<td>
<div class="audioPlayBut new_play_btn"><div class="bl"><div class="figure"></div></div></div>
<input type="hidden" value="{$row_audio['url']},{$row_audio['duration']},page" id="audio_url_{$row_audio['id']}_{$row['id']}_{$plname}">
</td>
<td class="info">
<div class="audioNames"><b class="author" onclick="Page.Go('/?go=search&query={$search_artist}&type=5&n=1'); return false;" id="artist">{$row_audio['artist']}</b>  –  <span class="name" id="name">{$row_audio['title']}</span> <div class="clear"></div></div>
<div class="audioElTime" id="audio_time_{$row_audio['id']}_{$row['id']}_{$plname}">{$stime}</div>
</td>
</tr>
</tbody>
</table>
<div id="player{$row_audio['id']}_{$row['id']}_{$plname}" class="audioPlayer" border="0" cellpadding="0">
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
                    }
                    $tpl->set(false, ['{audios}' => $audios,'{audio-num}' => $row['audio_num'],'[audios]' => '','[/audios]' => '','[yesaudio]' => '','[/yesaudio]' => '']);
                    $tpl->set_block("'\\[noaudio\\](.*?)\\[/noaudio\\]'si", '');
                } else {
                    $tpl->set_block("'\\[yesaudio\\](.*?)\\[/yesaudio\\]'si", '');
                    $tpl->set(false, ['{audios}' => '','[noaudio]' => '','[/noaudio]' => '']);
                    $tpl->set_block("'\\[audios\\](.*?)\\[/audios\\]'si", '');
                }


                //Обсуждения
                if ($row['discussion']) {

                    $tpl->set('{settings-discussion}', 'discussion');
                    $tpl->set('[discussion]', '');
                    $tpl->set('[/discussion]', '');

                } else {

                    $tpl->set('{settings-discussion}', 'none');
                    $tpl->set_block("'\\[discussion\\](.*?)\\[/discussion\\]'si", "");

                }

                if (!$row['forum_num']) $row['forum_num'] = '';
                $tpl->set('{forum-num}', $row['forum_num']);

                if ($row['forum_num'] and $row['discussion']) {

                    $sql_forum = $db->super_query("SELECT fid, title, lastuser_id, lastdate, msg_num FROM `communities_forum` WHERE public_id = '{$row['id']}' ORDER by `fixed` DESC, `lastdate` DESC, `fdate` DESC LIMIT 0, 5", true);
                    $thems = '';
                    foreach ($sql_forum as $row_forum) {

                        $row_last_user = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '{$row_forum['lastuser_id']}'");
                        $last_userX = explode(' ', $row_last_user['user_search_pref']);
                        $row_last_user['user_search_pref'] = grammaticalName($last_userX[0]) . ' ' . grammaticalName($last_userX[1]);

                        $row_forum['title'] = stripslashes($row_forum['title']);

                        $msg_num = $row_forum['msg_num'] . ' ' . declWord($row_forum['msg_num'], 'msg');

                        $last_date = megaDate($row_forum['lastdate']);

                        $thems .= "<div class=\"forum_bg\"><div class=\"forum_title cursor_pointer\" onClick=\"Page.Go('/forum{$row['id']}?act=view&id={$row_forum['fid']}'); return false\">{$row_forum['title']}</div><div class=\"forum_bottom\">{$msg_num}. Последнее от <a href=\"/u{$row_forum['lastuser_id']}\" onClick=\"Page.Go(this.href); return false\">{$row_last_user['user_search_pref']}</a>, {$last_date}</div></div>";

                    }

                    $tpl->set('{thems}', $thems);

                } else
                    $tpl->set('{thems}', '<div class="wall_none">В сообществе ещё нет тем.</div>');

                //Статус
                $tpl->set('{status-text}', stripslashes($row['status_text']));

                if ($row['status_text']) {

                    $tpl->set('[status]', '');
                    $tpl->set('[/status]', '');
                    $tpl->set_block("'\\[no-status\\](.*?)\\[/no-status\\]'si", "");

                } else {

                    $tpl->set_block("'\\[status\\](.*?)\\[/status\\]'si", "");
                    $tpl->set('[no-status]', '');
                    $tpl->set('[/no-status]', '');

                }

                $tpl->set('{web}', $row['web']);

                if ($row['web']) {

                    $tpl->set('[web]', '');
                    $tpl->set('[/web]', '');

                } else

                    $tpl->set_block("'\\[web\\](.*?)\\[/web\\]'si", "");

                //Видеозаписи
                if ($row['videos_num']) {

                    $sql_videos = $db->super_query("SELECT id, title, photo, add_date, comm_num, owner_user_id FROM `videos` WHERE public_id = '{$row['id']}' ORDER by `add_date` DESC LIMIT 0, 2", true);
                    $videos = '';
                    foreach ($sql_videos as $row_video) {

                        $row_video['title'] = stripslashes($row_video['title']);
                        $date_video = megaDate(strtotime($row_video['add_date']));
                        $comm_num = $row_video['comm_num'] . ' ' . declWord($row_video['comm_num'], 'comments');

                        $videos .= "
<div class=\"profile_one_video\"><a href=\"/video{$row_video['owner_user_id']}_{$row_video['id']}\" onClick=\"videos.show({$row_video['id']}, this.href, '/{$row['adres']}'); return false\"><img src=\"{$row_video['photo']}\" alt=\"\" width=\"185\" /></a><div class=\"video_profile_title\"><a href=\"/video{$row_video['owner_user_id']}_{$row_video['id']}\" onClick=\"videos.show({$row_video['id']}, this.href, '/{$row['adres']}'); return false\">{$row_video['title']}</a></div><div class=\"nesubscriptstatus\">{$date_video} - <a href=\"/video{$row_video['owner_user_id']}_{$row_video['id']}\" onClick=\"videos.show({$row_video['id']}, this.href, '/{$row['adres']}'); return false\">{$comm_num}</a></div></div>
				";

                    }

                    $tpl->set('{videos}', $videos);
                    $tpl->set('{videos-num}', $row['videos_num']);
                    $tpl->set('[videos]', '');
                    $tpl->set('[/videos]', '');
                    $tpl->set('[yesvideo]', '');
                    $tpl->set('[/yesvideo]', '');
                    $tpl->set_block("'\\[novideo\\](.*?)\\[/novideo\\]'si", "");

                } else {

                    $tpl->set('{videos}', '');
                    $tpl->set('[novideo]', '');
                    $tpl->set('[/novideo]', '');
                    $tpl->set_block("'\\[yesvideo\\](.*?)\\[/yesvideo\\]'si", "");

                    if ($public_admin) {

                        $tpl->set('[videos]', '');
                        $tpl->set('[/videos]', '');

                    } else
                        $tpl->set_block("'\\[videos\\](.*?)\\[/videos\\]'si", "");

                }

                //Обложка
                if ($row['photo']) {

                    $avaImgIsinfo = getimagesize(ROOT_DIR . "/uploads/groups/{$row['id']}/{$row['photo']}");

                    if ($avaImgIsinfo[1] < 200) {

                        $rForme = 230 - $avaImgIsinfo[1];

                        $ava_marg_top = 'style="margin-top:-' . $rForme . 'px"';

                    } else {
                        $ava_marg_top = '';
                    }

                    $tpl->set('{cover-param-7}', $ava_marg_top);

                } else
                    $tpl->set('{cover-param-7}', "");

                //Записываем в статистику "Уникальные посетители"
                $stat_date = date('Y-m-d', $server_time);
                $stat_x_date = date('Y-m', $server_time);
                $stat_date = strtotime($stat_date);
                $stat_x_date = strtotime($stat_x_date);

                $check_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats` WHERE gid = '{$row['id']}' AND date = '{$stat_date}'");
                $check_user_stat = $db->super_query("SELECT COUNT(*) AS cnt FROM `communities_stats_log` WHERE gid = '{$row['id']}' AND user_id = '{$user_info['user_id']}' AND date = '{$stat_date}' AND act = '1'");

                if (!$check_user_stat['cnt']) {

                    if ($check_stat['cnt']) {

                        $db->query("UPDATE `communities_stats` SET cnt = cnt + 1 WHERE gid = '{$row['id']}' AND date = '{$stat_date}'");

                    } else {

                        $db->query("INSERT INTO `communities_stats` SET gid = '{$row['id']}', date = '{$stat_date}', cnt = '1', date_x = '{$stat_x_date}'");

                    }

                    $db->query("INSERT INTO `communities_stats_log` SET user_id = '{$user_info['user_id']}', date = '{$stat_date}', gid = '{$row['id']}', act = '1'");

                }

                //Записываем в статистику "Просмотры"
                $db->query("UPDATE `communities_stats` SET hits = hits + 1 WHERE gid = '{$row['id']}' AND date = '{$stat_date}'");

                $tpl->compile('content');


            } else {
                $user_speedbar = $lang['no_infooo'];
                msgbox('', $lang['no_upage'], 'info');

            }
            $tpl->render();

//    $tpl->clear();
//    $db->free();
        } else {
            $user_speedbar = $lang['no_infooo'];
            msgbox('', $lang['not_logged'], 'info');
            compile($tpl);
        }
    }
}