<?php
/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Registry;
use Mozg\classes\Cache;

NoAjaxQuery();

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $server_time = Registry::get('server_time');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $cntCacheUp = Cache::mozgCache("user_{$user_id}/updates");
    if ($cntCacheUp) {
        $update_time = $server_time - 70;
        $row = $db->super_query("SELECT id, type, from_user_id, text, lnk, user_search_pref, user_photo FROM `updates` WHERE for_user_id = '{$user_id}' AND date > '{$update_time}' ORDER by `date` ASC");
        if ($row) {
            if ($row['user_photo']) {
                $ava = "/uploads/users/{$row['from_user_id']}/50_{$row['user_photo']}";
            } else {
                $ava = "/images/no_ava_50.png";
            }
            $row['text'] = str_replace("|", "&#124;", $row['text']);
            echo $row['type'] . '|' . $row['user_search_pref'] . '|' . $row['from_user_id'] . '|' . stripslashes($row['text']) . '|' . $server_time . '|' . $ava . '|' . $row['lnk'];
            $db->query("DELETE FROM `updates` WHERE id = '{$row['id']}'");
        } else {
            Cache::mozgCreateCache("user_{$user_id}/updates", '');
        }

    }

}