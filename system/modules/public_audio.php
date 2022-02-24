<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use Mozg\classes\Registry;

if (!defined('MOZG'))
    die('Hacking attempt!');

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $act = requestFilter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');

    switch ($act) {

        //################### Добавление песни в список сообщества ###################//
        case "addlistgroup":

            NoAjaxQuery();

            $pid = intFilter('pid');
            $aid = intFilter('aid');

            $check = $db->super_query("SELECT url, artist, name FROM `audio` WHERE aid = '{$aid}'");

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
            else $public_admin = false;

            if ($public_admin) {

                $db->query("INSERT INTO `communities_audio` SET public_id = '{$pid}', url = '" . $check['url'] . "', artist = '" . $check['artist'] . "', name = '" . $check['name'] . "',  adate = '{$server_time}'");

                $db->query("UPDATE `communities` SET audio_num = audio_num+1 WHERE id = '{$pid}'");

                mozg_clear_cache_file("groups/audio{$pid}");

            }

            break;

        //################### Сохранение отредактированных данных ###################//
        case "editsave":

            NoAjaxQuery();

            $aid = intFilter('aid');
            $pid = intFilter('pid');
            $artist = requestFilter('artist', 25000, true);
            $name = requestFilter('name', 25000, true);

            if (empty($artist))
                $artist = 'Неизвестный исполнитель';
            if (empty($name))
                $name = 'Без названия';

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (stripos($infoGroup['admin'], "u{$user_id}|") !== false)
                $public_admin = true;
            else
                $public_admin = false;

            if ($public_admin) {
                $db->query("UPDATE `communities_audio` SET artist = '{$artist}', name = '{$name}' WHERE aid = '{$aid}'");
                mozg_clear_cache_file("groups/audio{$pid}");
            }

            break;

        //################### Удаление песни из БД ###################//
        case "del":

            NoAjaxQuery();

            $aid = intFilter('aid');
            $pid = intFilter('pid');

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
            else $public_admin = false;

            if ($public_admin) {

                $db->query("DELETE FROM `communities_audio` WHERE aid = '{$aid}'");

                $db->query("UPDATE `communities` SET audio_num = audio_num-1 WHERE id = '{$pid}'");

                mozg_clear_cache_file("groups/audio{$pid}");

            }

            break;

        //################### Поиск ###################//
        case "search":

            NoAjaxQuery();

            $sql_limit = 20;

            $page_cnt = intFilter('page');
            if ($page_cnt > 0) $page_cnt = $page_cnt * $sql_limit;
            else $page_cnt = 0;

            $pid = intFilter('pid');

            $query = strip_data(requestFilter('query'));
            $query = strtr($query, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее

            $adres = strip_tags(requestFilter('adres'));

            $row_count = $db->super_query("SELECT COUNT(*) AS cnt FROM `audio` WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%'");

            $sql_ = $db->super_query("SELECT audio.aid, url, artist, name, auser_id, users.user_search_pref FROM audio LEFT JOIN users ON audio.auser_id = users.user_id WHERE MATCH (name, artist) AGAINST ('%{$query}%') OR artist LIKE '%{$query}%' OR name LIKE '%{$query}%' ORDER by `adate` DESC LIMIT {$page_cnt}, {$sql_limit}", true);

            $infoGroup = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$pid}'");

            if (stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
            else $public_admin = false;

            $tpl->load_template('public_audio/search_result.tpl');

            $jid = intval($page_cnt);

            if ($sql_) {

                if (!$page_cnt)
                    $tpl->result['content'] .= "<script>langNumric('langNumric', '{$row_count['cnt']}', 'аудиозапись', 'аудиозаписи', 'аудиозаписей', 'аудиозапись', 'аудиозаписей');</script><div class=\"allbar_title\" style=\"margin-bottom:0\">В поиске найдено <span id=\"seAudioNum\">{$row_count['cnt']}</span> <span id=\"langNumric\"></span> | <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a> | <a href=\"/\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все аудиозаписи</a></div>";

                foreach ($sql_ as $row) {
                    $jid++;
                    $tpl->set('{jid}', $jid);
                    $tpl->set('{aid}', $row['aid']);
                    $tpl->set('{url}', $row['url']);
                    $tpl->set('{artist}', stripslashes($row['artist']));
                    $tpl->set('{name}', stripslashes($row['name']));
                    $tpl->set('{author-n}', iconv_substr($row['user_search_pref'], 0, 1, 'utf-8'));
                    $expName = explode(' ', $row['user_search_pref']);
                    $tpl->set('{author-f}', $expName[1]);
                    $tpl->set('{author-id}', $row['auser_id']);

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

            } else {

                if (!$page_cnt) {

                    $tpl->result['info'] .= "<div class=\"allbar_title\">Нет аудиозаписей | <a href=\"/{$adres}\" onClick=\"Page.Go(this.href); return false\" style=\"font-weight:normal\">К сообществу</a> | <a href=\"/\" onClick=\"Page.Go(location.href); return false\" style=\"font-weight:normal\">Все аудиозаписи</a></div>";

                    msgbox('', '<br /><br /><br />По запросу <b>' . stripslashes($query) . '</b> не найдено ни одной аудиозаписи<br /><br /><br />', 'info_2');

                }
            }

            AjaxTpl($tpl);

            break;

        //################### Страница всех аудио ###################//
        default:

            $metatags['title'] = 'Аудиозаписи сообщества';

            $pid = intFilter('pid');

            $sql_limit = 20;

            $page_cnt = intFilter('page');
            if ($page_cnt > 0) $page_cnt = $page_cnt * $sql_limit;
            else $page_cnt = 0;

            if ($page_cnt)
                NoAjaxQuery();

            $sql_ = $db->super_query("SELECT aid, url, artist, name FROM `communities_audio` WHERE public_id = '{$pid}' ORDER by `adate` DESC LIMIT {$page_cnt}, {$sql_limit}", true);

            $infoGroup = $db->super_query("SELECT audio_num, adres, admin FROM `communities` WHERE id = '{$pid}'");

            if (!$page_cnt) {
                $tpl->load_template('public_audio/top.tpl');
                $tpl->set('{pid}', $pid);

                if ($infoGroup['adres']) $tpl->set('{adres}', $infoGroup['adres']);
                else $tpl->set('{adres}', 'public' . $pid);

                if ($infoGroup['audio_num']) $tpl->set('{audio-num}', $infoGroup['audio_num'] . ' <span id="langNumricAll"></span>');
                else $tpl->set('{audio-num}', 'Нет аудиозаписей');

                $tpl->set('{x-audio-num}', $infoGroup['audio_num']);

                if (!$infoGroup['audio_num']) {
                    $tpl->set('[no]', '');
                    $tpl->set('[/no]', '');
                } else
                    $tpl->set_block("'\\[no\\](.*?)\\[/no\\]'si", "");

                $tpl->compile('info');
            }

            if ($sql_) {

                $jid = intval($page_cnt);

                if (stripos($infoGroup['admin'], "u{$user_id}|") !== false) $public_admin = true;
                else $public_admin = false;

                $tpl->load_template('public_audio/track.tpl');

                $tpl->result['content'] .= '<div id="allGrAudis">';

                foreach ($sql_ as $row) {
                    $jid++;
                    $tpl->set('{jid}', $jid);
                    $tpl->set('{pid}', $pid);
                    $tpl->set('{aid}', $row['aid']);
                    $tpl->set('{url}', $row['url']);
                    $tpl->set('{artist}', stripslashes($row['artist']));
                    $tpl->set('{name}', stripslashes($row['name']));

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


                if ($infoGroup['audio_num'] > $sql_limit and !$page_cnt)
                    $tpl->result['content'] .= '<div id="ListAudioAddedLoadAjax"></div><div class="cursor_pointer" style="margin-top:-4px" onClick="ListAudioAddedLoadAjax()" id="wall_l_href_se_audiox"><div class="public_wall_all_comm profile_hide_opne" style="width:754px" id="wall_l_href_audio_se_loadx">Показать больше аудиозаписей</div></div>';

                $tpl->result['content'] .= '</div>';

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