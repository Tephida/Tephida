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
    $act = (new Request)->filter('act');
    $user_info = $user_info ?? Registry::get('user_info');
    $user_id = $user_info['user_id'];
    $db = Registry::get('db');
    $page = (new Request)->int('page', 1);

    $gcount = 70;
    $limit_page = ($page - 1) * $gcount;

    $metatags['title'] = $lang['fave'];

    switch ($act) {

        //################### Добавление юзера в закладки ###################//
        case "add":
            NoAjaxQuery();
            $fave_id = (new Request)->int('fave_id');
            //Проверяем на факт существования юзера которого добавляем в закладки
            $row = $db->super_query("SELECT `user_id` FROM `users` WHERE user_id = '{$fave_id}'");
            if ($row && $user_id != $fave_id) {

                //Проверяем на факт существование этого юзера в закладках, если нет то пропускаем
                $db->query("SELECT `user_id` FROM `fave` WHERE user_id = '{$user_id}' AND fave_id = '{$fave_id}'");
                if (!$db->num_rows()) {
                    $db->query("INSERT INTO `fave` SET user_id = '{$user_id}', fave_id = '{$fave_id}', date = NOW()");
                    $db->query("UPDATE `users` SET user_fave_num = user_fave_num+1 WHERE user_id = '{$user_id}'");
                } else {
                    echo 'yes_user';
                }
            } else {
                echo 'no_user';
            }

            break;

        //################### Удаление юзера из закладок ###################//
        case "delet":
            NoAjaxQuery();
            $fave_id = (new Request)->int('fave_id');

            //Проверяем на факт существование этого юзера в закладках, если есть то пропускаем
            $row = $db->super_query("SELECT `user_id` FROM `fave` WHERE user_id = '{$user_id}' AND fave_id = '{$fave_id}'");
            if ($row) {
                $db->query("DELETE FROM `fave` WHERE user_id = '{$user_id}' AND fave_id = '{$fave_id}'");
                $db->query("UPDATE `users` SET user_fave_num = user_fave_num-1 WHERE user_id = '{$user_id}'");
            } else {
                echo 'yes_user';
            }

            break;

        default:

            //################### Вывод людей которые есть в закладках ###################//
            $mobile_speedbar = 'Закладки';

            //Выводим кол-во людей в закладках
            $user = $db->super_query("SELECT user_fave_num FROM `users` WHERE user_id = '{$user_id}'");

            //Если кто-то есть в закладках то выводим
            if ($user['user_fave_num']) {

                $user_speedbar = '<span id="fave_num">' . $user['user_fave_num'] . '</span> ' . declWord($user['user_fave_num'], 'fave');

                //Загружаем поиск на странице
                $tpl->load_template('fave_search.tpl');
                $tpl->compile('content');

                //Выводи из базы
                $sql_ = $db->super_query("SELECT tb1.fave_id, tb2.user_search_pref, user_photo, user_last_visit, user_logged_mobile FROM `fave` tb1, `users` tb2 WHERE tb1.user_id = '{$user_id}' AND tb1.fave_id = tb2.user_id ORDER by `date` ASC LIMIT {$limit_page}, {$gcount}", true);
                $tpl->load_template('fave.tpl');
                $tpl->result['content'] .= '<table class="food_planner" id="fave_users">';
                foreach ($sql_ as $row) {
                    $config = settings_get();
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', $config['home_url'] . 'uploads/users/' . $row['fave_id'] . '/100_' . $row['user_photo']);
                    } else {
                        $tpl->set('{ava}', '/images/100_no_ava.png');
                    }

                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{user-id}', $row['fave_id']);

                    OnlineTpl($row['user_last_visit'], $row['user_logged_mobile']);

                    $tpl->compile('content');
                }
                $tpl->result['content'] .= '</table>';
                navigation($gcount, $user['user_fave_num'], $config['home_url'] . 'fave/page/');
            } else {
                $user_speedbar = $lang['no_infooo'];
                msgbox('', $lang['no_fave'], 'info_2');
            }

            compile($tpl);
    }
//    $tpl->clear();
//    $db->free();
} else {
    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);
}