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

NoAjaxQuery();

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $server_time = Registry::get('server_time');
    $user_info = $user_info ?? Registry::get('user_info');
    $act = (new Request)->filter('act');
    $mid = (new Request)->int('id');
    $type_report = (new Request)->int('type_report');
    $text_report = (new Request)->filter('text_report');
    $arr_act = array('photo', 'video', 'note', 'wall');
    if ($act == 'wall') {
        $type_report = 6;
    }
    if (in_array($act, $arr_act) && $mid && $type_report <= 6 && $type_report > 0) {
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `report` WHERE ruser_id = '" . $user_info['user_id'] . "' AND mid = '" . $mid . "' AND act = '" . $act . "'");
        if (!$check['cnt']) {
            $db->query("INSERT INTO `report` SET act = '" . $act . "', type = '" . $type_report . "', text = '" . $text_report . "', mid = '" . $mid . "', date = '" . $server_time . "', ruser_id = '" . $user_info['user_id'] . "'");
        }
    }
}