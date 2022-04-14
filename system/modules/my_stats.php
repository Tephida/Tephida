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
    $month = (new Request)->int('m');
    if ($month and $month <= 0 or $month > 12) {
        $month = 1;
    }

    $year = (new Request)->int('y');
    if ($year and $year < 2022 or $year > 2030) $year = 2022;

    if ($month and $year) {

        if ($month > 1 and $month < 10) {

            $t_date = langdate('F', strtotime($year . '-' . $month));

            $stat_date = $year . '0' . $month;
            $r_month = '0' . $month;

        } else {

            $stat_date = $year . $month;
            $r_month = $month;

            $t_date = langdate('F', strtotime($year . '-' . $month));

        }

    } else {

        $stat_date = date('Ym', $server_time);
        $r_month = date('m', $server_time);

        $month = date('n', $server_time);

        $t_date = langdate('F', strtotime($stat_date));

    }

    //Составляем массив для вывода за этот месяц
    /** fixme limit */
    $sql_ = $db->super_query("SELECT users, views, date FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `date` ASC", true);

    if ($sql_) {

        foreach ($sql_ as $row) {

            $dat_exp = date('j', strtotime($row['date']));

            $arr_r_unik[$dat_exp] = $row['users'];

            $arr_r_money[$dat_exp] = $row['views'];

        }

    }

    if ($r_month == '01' or $r_month == '03' or $r_month == '05' or $r_month == '07' or $r_month == '08' or $r_month == '10' or $r_month == '12' or $r_month == '1' or $r_month == '3' or $r_month == '5' or $r_month == '7' or $r_month == '8') $limit_day = 31;
    elseif ($r_month == '02') $limit_day = 28;
    else $limit_day = 30;

    $r_unik = '';
    $r_moneys = '';


    for ($i = 1; $i <= $limit_day; $i++) {

        if (!$arr_r_unik[$i]) $arr_r_unik[$i] = 0;
        $r_unik .= '[' . $i . ', ' . $arr_r_unik[$i] . '],';

        if (!$arr_r_money[$i]) $arr_r_money[$i] = 0;
        $r_moneys .= '[' . $i . ', ' . $arr_r_money[$i] . '],';

    }

    //Выводим максимальное кол-во юзеров за этот месяц
    $row_max = $db->super_query("SELECT users FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `users` DESC");

    $rNum = round($row_max['users'] / 15);
    if ($rNum < 1)
        $rNum = 1;

    $tickSize = $rNum;

    //Выводим максимальное кол-во просмотров за этот месяц
    $row_max_hits = $db->super_query("SELECT views FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `views` DESC");

    $rNum_moenys = round($row_max_hits['views'] / 15);
    if ($rNum_moenys < 1)
        $rNum_moenys = 1;

    $tickSize_moneys = $rNum_moenys;

    //Загружаем шаблон
    $tpl->load_template('profile_stats.tpl');

    $tpl->set('{r_unik}', $r_unik);
    $tpl->set('{r_moneys}', $r_moneys);
    $tpl->set('{t-date}', $t_date);
    $tpl->set('{tickSize}', $tickSize);
    $tpl->set('{tickSize_moneys}', $tickSize_moneys);
    $tpl->set('{uid}', $user_info['user_id']);

    $tpl->set('{months}', installationSelected($month, '<option value="1">Январь</option><option value="2">Февраль</option><option value="3">Март</option><option value="4">Апрель</option><option value="5">Май</option><option value="6">Июнь</option><option value="7">Июль</option><option value="8">Август</option><option value="9">Сентябрь</option><option value="10">Октябрь</option><option value="11">Ноябрь</option><option value="12">Декабрь</option>'));
    $tpl->set('{year}', installationSelected($year, '
<option value="2013">2022</option>
<option value="2014">2023</option>
<option value="2015">2024</option>
<option value="2016">2025</option>
<option value="2017">2026</option>
<option value="2018">2027</option>
<option value="2019">2028</option>
<option value="2019">2029</option>
<option value="2020">2030</option>'));

    $tpl->compile('content');

    $tpl->clear();
//    $db->free();

    compile($tpl);

} else {

    $user_speedbar = $lang['no_infooo'];
    msgbox('', $lang['not_logged'], 'info');
    compile($tpl);

}