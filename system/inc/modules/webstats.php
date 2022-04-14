<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;

echoheader();
echohtmlstart('Общая статистика сайта');

echo <<<HTML
<style>
/* STATS GROUPS */
.graph-container{position: relative;width:730px;height:400px;padding-left:20px;padding-right:20px;padding-top:5px;border:1px solid #d3dee8;margin-top:-11px;background:#ffffff;background: -moz-linear-gradient(top, #ffffff 0%, #f9f9f9 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f9f9f9));background: -webkit-linear-gradient(top, #ffffff 0%,#f9f9f9 100%);background: -o-linear-gradient(top, #ffffff 0%,#f9f9f9 100%);background: -ms-linear-gradient(top, #ffffff 0%,#f9f9f9 100%);background: linear-gradient(to bottom, #ffffff 0%,#f9f9f9 100%)}
.graph-container > div{position:absolute;width:inherit;height:inherit;margin-top:-30px}
.tickLabel{font-size: 11px;color:#52779a;font-family:Tahoma}
#tooltip_1{position:absolute;display:none;padding:5px 10px;background:rgba(0,0,0,0.79);color:#fff;font-size:11px;font-family:Tahoma;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;line-height:15px;z-index:2;margin-top:-6px}

.search_form_tab {
    color: #21578b;
    background: #f7f7f7;
    margin: -15px;
    padding: 10px;
    margin-top: -9px;
}
.buttonsprofileSec a {
    background: #2b9eb3;
    color: #fff;
    border-radius: 2px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    -khtml-border-radius: 2px;
    padding: 5px 7px 4px 7px;
}
.fl_r {
    float: right;
}
.inpst {
    border: 1px solid #c6d4dc;
    padding: 3px 4px;
    box-shadow: inset 0px 1px 3px 0px #e1e1e1;
    -moz-box-shadow: inset 0px 1px 3px 0px #e1e1e1;
    -webkit-box-shadow: inset 0px 1px 3px 0px #e1e1e1;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    resize: none;
}
.button_div {
    border: 1px solid #717b0e;
    display: block;
    border-radius: 3px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
}
.button_div button {
    background: -webkit-linear-gradient(top, #b7c42d, #8d991b);
    background: -moz-linear-gradient(top, #b7c42d, #8d991b);
    background: -ms-linear-gradient(top, #b7c42d, #8d991b);
    background: -o-linear-gradient(top, #b7c42d, #8d991b);
    background: linear-gradient(top, #b7c42d, #8d991b);
    color: #fff;
    font-size: 11px;
    font-family: Tahoma, Verdana, Arial, sans-serif, Lucida Sans;
    text-shadow: 0px 1px 0px #767f18;
    border: 0px;
    border-top: 1px solid #cdd483;
    padding: 4px 15px 4px 15px;
    cursor: pointer;
    margin: 0px;
    font-weight: bold;
    border-radius: 2px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
}
.allbar_title {
    padding: 5px;
    padding-left: 0px;
    padding-top: 10px;
    font-weight: bold;
    color: #5081b1;
    border-bottom: 1px solid #e0eaef;
    margin-bottom: 10px;
}
.margin_top_10 {
    margin-top: 10px;
}
.clear {
    clear: both;
}
.buttonsprofileSecond a {
    border: 0px;
    padding: 5px 7px 4px 7px;
}

.buttonsprofile a {
    float: left;
    padding: 5px 8px 4px 8px;
    margin-right: 10px;
    font-weight: bold;
}
.buttonsprofileSec a {
    background: #2b9eb3;
    color: #fff;
    border-radius: 2px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    -khtml-border-radius: 2px;
    padding: 5px 7px 4px 7px;
}


</style>
<script type="text/javascript" src="/js/jquery.lib.js"></script>
<script type="text/javascript" src="/js/main.js"></script>
<script type="text/javascript" src="/js/audio.js"></script>
HTML;


//$db = Registry::get('db');
//$user_info = $user_info ?? Registry::get('user_info');
//$user_info['user_id'] = 0;
$server_time = time();
$month = (new Request)->int('m');
if ($month and $month <= 0 or $month > 12) $month = 1;

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
//$sql_ = $db->super_query("SELECT users, views, date FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `date` ASC", true);
$sql_ = $db->super_query("SELECT users, views, date FROM `users_stats` WHERE date_x = '{$stat_date}' ORDER by `date` ASC", true);

if ($sql_) {

    foreach ($sql_ as $row) {
        $dat_exp = date('j', strtotime($row['date']));

        $arr_r_unik[$dat_exp] = $arr_r_unik[$dat_exp] ?? 0;
        $arr_r_money[$dat_exp] = $arr_r_money[$dat_exp] ?? 0;

        $arr_r_unik[$dat_exp] += $row['users'];
        $arr_r_money[$dat_exp] += $row['views'];
    }

//    var_dump($sql_);
//    exit();

} else {
    $arr_r_unik = 0;
    $arr_r_money = 0;
}

if ($r_month == '01' or $r_month == '03' or $r_month == '05' or $r_month == '07' or $r_month == '08' or
    $r_month == '10' or $r_month == '12' or $r_month == '1' or $r_month == '3' or
    $r_month == '5' or $r_month == '7' or $r_month == '8')
    $limit_day = 31;
elseif ($r_month == '02')
    $limit_day = 28;
else
    $limit_day = 30;

$r_unik = array();
$r_moneys = array();


for ($i = 1; $i <= $limit_day; $i++) {

    if (!$arr_r_unik[$i])
        $arr_r_unik[$i] = 0;
    $r_unik .= '[' . $i . ', ' . $arr_r_unik[$i] . '],';

    if (!$arr_r_money[$i])
        $arr_r_money[$i] = 0;
    $r_moneys .= '[' . $i . ', ' . $arr_r_money[$i] . '],';

}

//Выводим максимальное кол-во юзеров за этот месяц
//$row_max = $db->super_query("SELECT users FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `users` DESC");
$row = $db->super_query("SELECT users FROM `users_stats` WHERE date_x = '{$stat_date}' ORDER by `users` DESC", true);

$row_max['users'] = 0;

foreach ($row as $item) {
    $row_max['users'] += $item['users'];
}

$rNum = round($row_max['users'] / 15);
if ($rNum < 1)
    $rNum = 1;

$tickSize = $rNum;

//Выводим максимальное кол-во просмотров за этот месяц
//$row_max_hits = $db->super_query("SELECT views FROM `users_stats` WHERE user_id = '{$user_info['user_id']}' AND date_x = '{$stat_date}' ORDER by `views` DESC");
$row = $db->super_query("SELECT views FROM `users_stats` WHERE date_x = '{$stat_date}' ORDER by `views` DESC", true);
//$db->free();
$row_max_hits['views'] = 0;

foreach ($row as $item) {
    $row_max_hits['views'] += $item['views'];
}

$rNum_moenys = round($row_max_hits['views'] / 15);
if ($rNum_moenys < 1)
    $rNum_moenys = 1;

$tickSize_moneys = $rNum_moenys;

//Загружаем шаблон
$tpl->load_template('stats/all_users.tpl');

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

//echo $tpl->result['content'];
echo str_replace('{theme}', '/templates/' . $config['temp'], $tpl->result['content']);


echohtmlend();