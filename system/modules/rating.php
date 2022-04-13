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
    $act = (new Request)->filter('act');
    $server_time = Registry::get('server_time');

    switch ($act) {

        //################### История повышения рейтинга ###################//
        case "view":

            $limit_news = 10;
            $page_cnt = (new Request)->int('page_cnt');
            if ($page_cnt > 0) {
                $page_cnt *= $limit_news;
            } else {
                $page_cnt = 0;
            }

            //Выводим список
            $sql_ = $db->super_query("SELECT tb1.user_id, addnum, date, tb2.user_search_pref, user_photo FROM `users_rating` tb1, `users` tb2 WHERE tb1.user_id = tb2.user_id AND for_user_id = '{$user_id}' ORDER by `date` DESC LIMIT {$page_cnt}, {$limit_news}", true);

            if ($sql_) {

                $i = 0;

                $tpl->load_template('rating/user.tpl');
                foreach ($sql_ as $row) {
                    $i++;
                    if ($row['user_photo']) {
                        $tpl->set('{ava}', "/uploads/users/{$row['user_id']}/50_{$row['user_photo']}");
                    } else {
                        $tpl->set('{ava}', "/images/no_ava_50.png");
                    }

                    $tpl->set('{user-id}', $row['user_id']);
                    $tpl->set('{name}', $row['user_search_pref']);
                    $tpl->set('{rate}', $row['addnum']);
                    $date_str = megaDate($row['date']);
                    $tpl->set('{date}', $date_str);
                    $tpl->compile('users');

                }

            } else
                if (!$page_cnt) {
                    $tpl->result['users'] = '<div class="info_center"><br /><br />Пока что никто не повышал Ваш рейтинг.<br /><br /><br /></div>';
                }

            if (!$page_cnt) {

                $tpl->load_template('rating/view.tpl');
                $tpl->set('{users}', $tpl->result['users']);

                if ($i == 10) {
                    $tpl->set('[prev]', '');
                    $tpl->set('[/prev]', '');
                } else {
                    $tpl->set_block("'\\[prev\\](.*?)\\[/prev\\]'si", "");
                }
                $tpl->compile('content');
            } else {
                $tpl->result['content'] = $tpl->result['users'];
            }
            AjaxTpl($tpl);
            break;

        //################### Начисление рейтинга ###################//
        case "add":

            $for_user_id = (new Request)->int('for_user_id');
            $num = (new Request)->int('num');
            if ($num < 0) {
                $num = 0;
            }
            //Выводим текущий баланс свой
            $row = $db->super_query("SELECT user_balance FROM `users` WHERE user_id = '{$user_id}'");

            //Проверка что такой юзер есть
            $check = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` WHERE user_id = '{$for_user_id}'");

            if ($row['user_balance'] < 0) {
                $row['user_balance'] = 0;
            }
            if ($check['cnt'] && $num > 0) {
                if ($row['user_balance'] >= $num) {
                    //Обновляем баланс у того кто повышал
                    $db->query("UPDATE `users` SET user_balance = user_balance - {$num} WHERE user_id = '{$user_id}'");
                    //Начисляем рейтинг
                    $db->query("UPDATE `users` SET user_rating = user_rating + {$num} WHERE user_id = '{$for_user_id}'");
                    //Вставляем в лог
                    $db->query("INSERT INTO `users_rating` SET user_id = '{$user_id}', for_user_id = '{$for_user_id}', addnum = '{$num}', date = '{$server_time}'");
                    //Чистим кеш
                    Cache::mozgClearCacheFile("user_{$for_user_id}/profile_{$for_user_id}");

                } else {
                    echo 1;
                }
            } else {
                echo 1;
            }

            break;

        //################### Страница начисления рейтинга ###################//
        default:

            //Выводим текущий баланс свой
            $row = $db->super_query("SELECT user_balance FROM `users` WHERE user_id = '{$user_id}'");
            $tpl->load_template('rating/main.tpl');
            $tpl->set('{user-id}', (new Request)->int('for_user_id'));
            $tpl->set('{num}', $row['user_balance'] - 1);
            $tpl->set('{balance}', $row['user_balance']);
            $tpl->compile('content');
            AjaxTpl($tpl);
    }
}