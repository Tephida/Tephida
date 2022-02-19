<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */
if (!defined('MOZG'))
    die('Hacking attempt!');

NoAjaxQuery();

if (Registry::get('logged')) {
    $db = Registry::get('db');
    $server_time = Registry::get('server_time');
    $user_info = $user_info ?? Registry::get('user_info');
    $act = requestFilter('act');
    $mid = intFilter('id');
    $type_report = intFilter('type_report');
    $text_report = requestFilter('text_report');
    $arr_act = array('photo', 'video', 'note', 'wall');
    if ($act == 'wall')
        $type_report = 6;
    if (in_array($act, $arr_act) and $mid and $type_report <= 6 and $type_report > 0) {
        $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `report` WHERE ruser_id = '" . $user_info['user_id'] . "' AND mid = '" . $mid . "' AND act = '" . $act . "'");
        if (!$check['cnt'])
            $db->query("INSERT INTO `report` SET act = '" . $act . "', type = '" . $type_report . "', text = '" . $text_report . "', mid = '" . $mid . "', date = '" . $server_time . "', ruser_id = '" . $user_info['user_id'] . "'");
    }
}