<?php

/*
 * Copyright (c) 2022 Tephida
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Http\Request;

$se_uid = (new Request)->int('se_uid');
if (!$se_uid) {
    $se_uid = '';
}

$sort = (new Request)->int('sort');
//$se_name = $_GET['se_name'] ?? '';
//$se_email = $_GET['se_email'] ?? '';

$se_name = (new Request)->filter('se_name', 25000, true);
$se_email = (new Request)->filter('se_email', 25000, true);
$ban = (new Request)->filter('ban');
$delete = (new Request)->filter('delete');

$regdate = $_GET['regdate'] ?? null;//todo
$where_sql = '';

if ($se_uid || $sort || $se_name || $se_email || $ban || $delete || $regdate) {
    $where_sql .= " WHERE user_email != ''";
    if ($se_uid) {
        $where_sql .= " AND user_id = '" . $se_uid . "' ";
    }
    if ($se_name) {
        $where_sql .= " AND user_search_pref LIKE '%" . $se_name . "%' ";
    }
    if ($se_email) {
        $where_sql .= " AND user_email LIKE '%" . $se_email . "%' ";
    }
    if ($ban) {
        $where_sql .= " AND user_ban = 1 ";
        $checked_ban = "checked";
    } else {
        $checked_ban = '';
    }
    if ($delete === 'on') {
        $where_sql .= " AND user_delet = 1 ";
        $checked_delete = "checked";
    } else {
        $checked_delete = '';
    }
    if ($sort === 1) $order_sql = "`user_search_pref` ASC";
    else if ($sort === 2) $order_sql = "`user_reg_date` ASC";
    else if ($sort === 3) $order_sql = "`user_last_visit` DESC";
    else $order_sql = "`user_reg_date` DESC";
} else {
    $checked_ban = $checked_delete = '';
}
$order_sql = "`user_reg_date` DESC";

$selsorlist = installationSelected($sort, '<option value="1">по алфавиту</option><option value="2">по дате регистрации</option><option value="3">по дате посещения</option>');

//Выводим список людей
$page = (new \FluffyDollop\Http\Request)->int('page', 1);

$gcount = 20;
$limit_page = ($page - 1) * $gcount;

$sql_ = $db->super_query("SELECT user_group, user_search_pref, user_id, user_real, user_reg_date, user_last_visit, user_email, user_delet, user_ban, user_balance FROM `users`  {$where_sql} ORDER by {$order_sql} LIMIT {$limit_page}, {$gcount}", true);

//Кол-во людей считаем
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `users` {$where_sql}");
$admin_index = $admin_index ?? null;

$tpl = new TplCp(ADMIN_DIR . '/tpl/');
//echohtmlstart('Список пользователей (' . $numRows['cnt'] . ')');
$users = '';
$toltip_num = 0;
foreach ($sql_ as $row) {
//    $format_reg_date = date('Y-m-d', $row['user_reg_date']);
//    $lastvisit = date('Y-m-d', $row['user_last_visit']);

    $row['user_balance'] = $row['user_balance'] ?? null;
    $row['user_search_pref'] = $row['user_search_pref'] ?? null;
    $row['user_id'] = $row['user_id'] ?? null;
    $row['user_reg_date'] = $row['user_reg_date'] ?? null;
    $row['user_last_visit'] = $row['user_last_visit'] ?? null;
    $row['user_email'] = $row['user_email'] ?? null;

    $row['user_reg_date'] = langdate('j M Y в H:i', $row['user_reg_date']);
    $row['user_last_visit'] = langdate('j M Y в H:i', $row['user_last_visit']);

    $row['user_status'] = '';

    $row['user_group'] = (int)$row['user_group'];

    if ($row['user_delet']) {
        $toltip_num++;
        $row['user_status'] .= "<div class=\"user_status\" style='background: red' onmouseover=\"myhtml.title('{$toltip_num}', 'Удален', 'statusTitle', '{$toltip_num}')\" id=\"statusTitle{$toltip_num}\"></div>";
    } else if ($row['user_ban']) {
        $toltip_num++;
        $row['user_status'] .= "<div class=\"user_status\" style='background: blue' onmouseover=\"myhtml.title('{$toltip_num}', 'Заблокирован', 'statusTitle', '{$toltip_num}')\" id=\"statusTitle{$toltip_num}\"></div>";
    } else if ($row['user_group'] === 1) {
        $toltip_num++;
        $row['user_status'] .= "<div class=\"user_status\" style='background: yellow' onmouseover=\"myhtml.title('{$toltip_num}', 'Администратор', 'statusTitle', '{$toltip_num}')\" id=\"statusTitle{$toltip_num}\"></div>";
    } else if ($row['user_group'] === 4) {
        $toltip_num++;
        $row['user_status'] .= "<div class=\"user_status\" style='background: green' onmouseover=\"myhtml.title('{$toltip_num}', 'Техподдержка', 'statusTitle', '{$toltip_num}')\" id=\"statusTitle{$toltip_num}\"></div>";
    } else if ($row['user_real'] === 1) {
        $toltip_num++;
        $row['user_status'] .= "<div class=\"user_status\" style='background: purple' onmouseover=\"myhtml.title('{$toltip_num}', 'Проверен', 'statusTitle', '{$toltip_num}')\" id=\"statusTitle{$toltip_num}\"></div>";
    }

    $users .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:170px;text-align:center;font-weight:bold;" 
title="Баланс: {$row['user_balance']} голосов">
<a href="/u{$row['user_id']}" target="_blank" >{$row['user_search_pref']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:110px;text-align:center;margin-left:1px">{$row['user_reg_date']}</div>
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;margin-left:1px">{$row['user_last_visit']}</div>
<div style="background:#fff;float:left;padding:5px;width:148px;text-align:center;margin-left:1px">{$row['user_email']}</div>
<div style="background:#fff;float:left;padding:5px;width:148px;text-align:center;margin-left:1px">
<div></div>
<div style="display: flex">{$row['user_status']}</div>
</div>
<div style="background:#fff;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-left:1px"><input type="checkbox" name="massaction_users[]" style="float:right;" value="{$row['user_id']}" /></div>
<div class="mgcler"></div>
HTML;
}

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);

$tpl->load_template('users/main.tpl');
$config = settings_get();
$tpl->set('{admin_index}', $config['admin_index']);
$tpl->set('{se_uid}', $se_uid);
$tpl->set('{se_name}', $se_name);
$tpl->set('{se_email}', $se_email);
$tpl->set('{checked_ban}', $checked_ban);
$tpl->set('{checked_delete}', $checked_delete);
$tpl->set('{selsorlist}', $selsorlist);

$tpl->set('{users}', $users);
$tpl->set('{navigation}', navigationNew($gcount, $numRows['cnt'], '?' . $query_string . '&page='));
$tpl->compile('content');
$tpl->render();
