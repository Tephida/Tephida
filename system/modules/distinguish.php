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

NoAjaxQuery();

if (Registry::get('logged')) {
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $server_time = Registry::get('server_time');
    $db = Registry::get('db');
    switch ($act) {

        //################### Отмечаем человека на фото ###################//
        case "mark":
            $i_left = (new Request)->int('i_left');
            if ($i_left < 0) {
                $i_left = 0;
            }
            $i_top = (new Request)->int('i_top');
            if ($i_top < 0) {
                $i_top = 0;
            }
            $i_width = (new Request)->int('i_width');
            if ($i_width < 0) {
                $i_width = 0;
            }
            $i_height = (new Request)->int('i_height');
            if ($i_height < 0) {
                $i_height = 0;
            }
            $photo_id = (new Request)->int('photo_id');
            $muser_id = (new Request)->int('user_id');
            $mphoto_name = strip_data((new Request)->filter('user_name', 25000, true));
            $msettings_pos = $i_left . ", " . $i_top . ", " . $i_width . ", " . $i_height;
            if ($user_id == $muser_id) {
                $approve = 1;
            } else {
                $approve = 0;
            }

            if ($mphoto_name && $muser_id == 0) {
                $row_no = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND mphoto_name = '" . $mphoto_name . "'");
                $row = null;
            } else {
                $row = $db->super_query("SELECT COUNT(*) AS cnt FROM `photos_mark` WHERE muser_id = '" . $muser_id . "' AND mphoto_id = '" . $photo_id . "'");
                $row_no = null;
            }

            if ($row['cnt']) {
                $db->query("UPDATE `photos_mark` SET msettings_pos = '" . $msettings_pos . "' WHERE muser_id = '" . $muser_id . "' AND mphoto_id = '" . $photo_id . "'");
            } elseif ($row_no['cnt']) {
                $db->query("UPDATE `photos_mark` SET msettings_pos = '" . $msettings_pos . "' WHERE mphoto_id = '" . $photo_id . "' AND mphoto_name = '" . $mphoto_name . "'");
            } else
                if ((new Request)->filter('user_ok') == 'yes') {
                    $db->query("INSERT INTO `photos_mark` SET muser_id = '" . $muser_id . "', mphoto_id = '" . $photo_id . "', mdate = '" . $server_time . "', msettings_pos = '" . $msettings_pos . "', mapprove = '" . $approve . "', mmark_user_id = '" . $user_id . "'");

                    if ($user_id != $muser_id) {
                        $db->query("UPDATE `users` SET user_new_mark_photos = user_new_mark_photos+1 WHERE user_id = '" . $muser_id . "'");
                    }
                } else {
                    $db->query("INSERT INTO `photos_mark` SET muser_id = '" . random_int(0, 100000) . "', mphoto_id = '" . $photo_id . "', mdate = '" . $server_time . "', msettings_pos = '" . $msettings_pos . "', mphoto_name = '" . $mphoto_name . "', mmark_user_id = '" . $user_id . "', mapprove = 1");
                }

            Cache::mozgClearCacheFile('photos_mark/p' . $photo_id);
            break;

        //################### Удаление отметки ###################//
        case "mark_del":
            $photo_id = (new Request)->int('photo_id');
            $muser_id = (new Request)->int('user_id');
            $mphoto_name = strip_data((new Request)->filter('user_name', 25000, true));
            $row = $db->super_query("SELECT user_id FROM `photos` WHERE id = '" . $photo_id . "'");

            if ($mphoto_name && $muser_id == 0) {
                $row_mark = $db->super_query("SELECT mmark_user_id FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND mphoto_name = '" . $mphoto_name . "'");
            } else {
                $row_mark = $db->super_query("SELECT mmark_user_id, mapprove FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND muser_id = '" . $muser_id . "'");
            }

            if ($row['user_id'] == $user_id or $user_id == $muser_id or $user_id == $row_mark['mmark_user_id']) {
                if ($mphoto_name && $muser_id == 0) {
                    $db->query("DELETE FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND mphoto_name = '" . $mphoto_name . "'");
                } else {
                    $db->query("DELETE FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND muser_id = '" . $muser_id . "' AND mphoto_name = ''");

                    if (!$row_mark['mapprove']) {
                        $db->query("UPDATE `users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '" . $muser_id . "'");
                    }
                }
                Cache::mozgClearCacheFile('photos_mark/p' . $photo_id);
            }
            break;

        //################### Подтверждение отметки ###################//
        case "mark_ok":
            $photo_id = (new Request)->int('photo_id');
            $row = $db->super_query("SELECT mapprove FROM `photos_mark` WHERE mphoto_id = '" . $photo_id . "' AND muser_id = '" . $user_id . "'");
            if ($row && !$row['mapprove']) {
                $db->query("UPDATE `photos_mark` SET mapprove = '1' WHERE mphoto_id = '" . $photo_id . "' AND muser_id = '" . $user_id . "'");
                $db->query("UPDATE `users` SET user_new_mark_photos = user_new_mark_photos-1 WHERE user_id = '" . $user_id . "'");
                Cache::mozgClearCacheFile('photos_mark/p' . $photo_id);
            }
            break;

        //################### Загрузка 110 друзей из списка ###################//
        case "load_friends":
            $photo_id = (new Request)->int('photo_id');
            $all_limit = 110;
            if ((new Request)->filter('page') == 2) {
                $limit = $all_limit . ", " . ($all_limit * 2);
            } else {
                $limit = "0, " . $all_limit;
            }

            $sql_ = $db->super_query("SELECT tb1.friend_id, tb2.user_search_pref FROM `friends` tb1, `users` tb2 WHERE tb1.user_id = '" . $user_id . "' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0 ORDER by `user_search_pref` ASC LIMIT " . $limit, true);

            $myRow = $db->super_query("SELECT user_search_pref FROM `users` WHERE user_id = '" . $user_id . "'");

            if ($sql_) {
                $cnt = 0;
                $friend = '';
                foreach ($sql_ as $row) {
                    $friend .= '<tr id="user_' . $row['friend_id'] . '" class="echoUsersList"><td width="170"><div onClick="Distinguish.SelectUser(' . $row['friend_id'] . ', \'' . $row['user_search_pref'] . '\', ' . $photo_id . ')">' . $row['user_search_pref'] . '</div></td></tr>';
                    $cnt++;
                }

                if ($cnt == $all_limit && !isset($_POST['page'])) {
                    $added_script = "setTimeout('Distinguish.FriendPage(2, " . $photo_id . "')', 2500)";
                } else {
                    $added_script = null;
                }

            } else {
                $friend = null;
                $added_script = null;
            }
            $config = settings_get();
            echo <<<HTML
<script type="text/javascript" src="/templates/{$config['temp']}/js/fave.filter.js"></script>
<script type="text/javascript">
{$added_script}
</script>
<table class="food_planner" id="fave_users">
<tr id="user_{$user_id}"><td width="170"><div onClick="Distinguish.SelectUser({$user_id}, '{$myRow['user_search_pref']}', {$photo_id})">Я</div></td></tr>
{$friend}
</table>
HTML;

            break;
    }
    $tpl->clear();
//    $db->free();
} else {
    echo 'no_log';
}