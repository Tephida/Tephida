<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\{Filesystem, Registry};
use Mozg\classes\Cache;

NoAjaxQuery();
if (Registry::get('logged')) {
    $db = Registry::get('db');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $uploaddir = ROOT_DIR . '/uploads/users/' . $user_id . '/';
    $row = $db->super_query("SELECT user_photo, user_wall_id FROM `users` WHERE user_id = '" . $user_id . "'");
    if ($row['user_photo']) {
        $check_wall_rec = $db->super_query("SELECT COUNT(*) AS cnt FROM `wall` WHERE id = '" . $row['user_wall_id'] . "'");
        if ($check_wall_rec['cnt']) {
            $update_wall = ", user_wall_num = user_wall_num-1";
            $db->query("DELETE FROM `wall` WHERE id = '" . $row['user_wall_id'] . "'");
            $db->query("DELETE FROM `news` WHERE obj_id = '" . $row['user_wall_id'] . "'");
        } else {
            $update_wall = '';
        }
        $db->query("UPDATE `users` SET user_delet = 1, user_photo = '', user_wall_id = '' " . $update_wall . " WHERE user_id = '" . $user_id . "'");
        Filesystem::delete($uploaddir . $row['user_photo']);
        Filesystem::delete($uploaddir . '50_' . $row['user_photo']);
        Filesystem::delete($uploaddir . '100_' . $row['user_photo']);
        Filesystem::delete($uploaddir . 'o_' . $row['user_photo']);
        Filesystem::delete($uploaddir . 'c_' . $row['user_photo']);
    } else {
        $db->query("UPDATE `users` SET user_delet = 1, user_photo = '' WHERE user_id = '" . $user_id . "'");
    }
    Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
}