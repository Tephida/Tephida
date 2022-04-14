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
    $user_info = $user_info ?? Registry::get('user_info');
    $server_time = Registry::get('server_time');
    $alt_name = to_translit((new Request)->filter('page'));
    $row = $db->super_query("SELECT title, text FROM `static` WHERE alt_name = '" . $alt_name . "'");
    if ($row) {
        $tpl->load_template('static.tpl');
        $tpl->set('{alt_name}', $alt_name);
        $tpl->set('{title}', stripslashes($row['title']));
        $tpl->set('{text}', stripslashes($row['text']));
        $tpl->compile('content');
    } else {
        msgbox('', 'Страница не найдена.', 'info_2');
    }
    compile($tpl);

//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}