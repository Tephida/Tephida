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
    $db = Registry::get('db');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $text = (new Request)->filter('text', 25000, true);
    $public_id = (new Request)->int('public_id');

    //Если обновляем статус группы
    if ((new Request)->filter('act') == 'public') {
        $row = $db->super_query("SELECT admin FROM `communities` WHERE id = '{$public_id}'");
        if (stripos($row['admin'], "u{$user_id}|") !== false) {
            $db->query("UPDATE `communities` SET status_text = '{$text}' WHERE id = '{$public_id}'");
            Cache::mozgClearCacheFolder('groups');
        }
        //Если пользователь
    } else {
        $db->query("UPDATE `users` SET user_status = '{$text}' WHERE user_id = '{$user_id}'");
        //Чистим кеш
        Cache::mozgClearCacheFile('user_' . $user_id . '/profile_' . $user_id);
        Cache::mozgClearCache();
    }
    echo (new Request)->filter('text');
}