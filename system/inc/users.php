<?php
/* 
	Appointment: Пользователи
	File: users.php
 
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

echoheader();	

$se_uid = intval($_GET['se_uid']);
if(!$se_uid) $se_uid = '';

$sort = intval($_GET['sort']);
$se_name = textFilter($_GET['se_name'], false, true);
$se_email = textFilter($_GET['se_email'], false, true);
$ban = $_GET['ban'];
$delet = $_GET['delet'];

if($se_uid OR $sort OR $se_name OR $se_email OR $ban OR $delet OR $_GET['regdate']){
	$where_sql .= "WHERE user_email != ''";
	if($se_uid) $where_sql .= "AND user_id = '".$se_uid."' ";
	if($se_name) $where_sql .= "AND user_search_pref LIKE '%".$se_name."%' ";
	if($se_email) $where_sql .= "AND user_email LIKE '%".$se_email."%' ";
	if($ban){$where_sql .= "AND user_ban = 1 ";$checked_ban = "checked";}
	if($delet){$where_sql .= "AND user_delet = 1 ";$checked_delet = "checked";}
	if($sort == 1) $order_sql = "`user_search_pref` ASC";
	else if($sort == 2) $order_sql = "`user_reg_date` ASC";
	else if($sort == 3) $order_sql = "`user_last_visit` DESC";
	else $order_sql = "`user_reg_date` DESC";
} else
	$order_sql = "`user_reg_date` DESC";
	
$selsorlist = installationSelected($sort, '<option value="1">по алфавиту</option><option value="2">по дате регистрации</option><option value="3">по дате посещения</option>');

//Выводим список людей
if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
$gcount = 20;
$limit_page = ($page-1)*$gcount;

$sql_ = $db->super_query("SELECT user_group, user_search_pref, user_id, user_real, user_reg_date, user_last_visit, user_email, user_delet, user_ban, user_balance FROM `".PREFIX."_users`  {$where_sql} ORDER by {$order_sql} LIMIT {$limit_page}, {$gcount}", 1);

//Кол-во людей считаем
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_users` {$where_sql}");

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form action="{$admin_index}" method="GET">

<input type="hidden" name="mod" value="users" />

<div class="fllogall">Поиск по ID:</div>
 <input type="text" name="se_uid" class="inpu" value="{$se_uid}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по имени:</div>
 <input type="text" name="se_name" class="inpu" value="{$se_name}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по email:</div>
 <input type="text" name="se_email" class="inpu" value="{$se_email}" />
<div class="mgcler"></div>

<div class="fllogall">Бан:</div>
 <input type="checkbox" name="ban" style="margin-bottom:10px" {$checked_ban} />
<div class="mgcler"></div>

<div class="fllogall">Удалены:</div>
 <input type="checkbox" name="delet" style="margin-bottom:10px" {$checked_delet} />
<div class="mgcler"></div>

<div class="fllogall">Сортировка:</div>
 <select name="sort" class="inpu">
  <option value="0"></option>
  {$selsorlist}
 </select>
<div class="mgcler"></div>

<div class="fllogall">&nbsp;</div>
 <input type="submit" value="Найти" class="inp" style="margin-top:0px" />

</form>
HTML;

echohtmlstart('Список пользователей ('.$numRows['cnt'].')');

foreach($sql_ as $row){
	$format_reg_date = date('Y-m-d', $row['user_reg_date']);
	$lastvisit = date('Y-m-d', $row['user_last_visit']);
	
	$row['user_reg_date'] = langdate('j M Y в H:i', $row['user_reg_date']);
	$row['user_last_visit'] = langdate('j M Y в H:i', $row['user_last_visit']);

	if($row['user_delet']) 
		$color = 'color:red';
	else if($row['user_ban'])
		$color = 'color:blue';
	else if($row['user_group'] == 4)
		$color = 'color:green';
	else if($row['user_real'] == 1)
		$color = 'color:purple';
	else
		$color = '';
	
	$users .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:170px;text-align:center;font-weight:bold;" title="Баланс: {$row['user_balance']} голосов"><a href="/u{$row['user_id']}" target="_blank" style="{$color}">{$row['user_search_pref']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:110px;text-align:center;margin-left:1px">{$row['user_reg_date']}</div>
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;margin-left:1px">{$row['user_last_visit']}</div>
<div style="background:#fff;float:left;padding:5px;width:148px;text-align:center;margin-left:1px">{$row['user_email']}</div>
<div style="background:#fff;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-left:1px"><input type="checkbox" name="massaction_users[]" style="float:right;" value="{$row['user_id']}" /></div>
<div class="mgcler"></div>
HTML;
}

echo <<<HTML
<script language="text/javascript" type="text/javascript">
function ckeck_uncheck_all() {
    var frm = document.editusers;
    for (var i=0;i<frm.elements.length;i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type=='checkbox') {
            if(frm.master_box.checked == true){ elmnt.checked=false; }
            else{ elmnt.checked=true; }
        }
    }
    if(frm.master_box.checked == true){ frm.master_box.checked = false; }
    else{ frm.master_box.checked = true; }
}
</script>
<form action="?mod=massaction&act=users" method="post" name="editusers">
<div style="background:#f0f0f0;float:left;padding:5px;width:170px;text-align:center;font-weight:bold;margin-top:-5px">Пользователь</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Дата регистрации</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Дата посещения</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:148px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">E-mail</div>
<div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px"><input type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:ckeck_uncheck_all()" style="float:right;"></div>
<div class="clr"></div>
{$users}
<div style="font:normal 11px Tahoma;padding:10px 5px;border-bottom:1px dashed #CCC;">
Цвета пользователей: <font color="purple">Проверенные</font> &middot; <font color="red">Удаленные</font> &middot; <font color="blue">Заблокированные</font> &middot; <font color="green">Техподдержка</font>
</div>
<div style="float:right">
<select name="mass_type" class="inpu" style="width:260px">
 <option value="0">- Выберите действие -</option>
 <option value="1">Удалить пользователей</option>
 <option value="7">Воостановить пользователей</option>
 <option value="2">Заблокировать пользователей</option>
 <option value="9">Разблокировать пользователей</option>
 <option value="3">Удалить отправленные сообщения</option>
 <option value="4">Удалить комментарии к фото</option>
 <option value="5">Удалить комментарии к видео</option>
 <option value="11">Удалить комментарии к заметкам</option>
 <option value="6">Удалить записи на стенах</option>
 <option value="12">Начислить голоса</option>
 <option value="13">Забрать голоса</option>
 <option value="16">Перевести в «Техподдержка»</option>
 <option value="17">Перевести в «Пользователи»</option>
 <option value="18">Подтвердить аккаунт</option>
 <option value="19">Снять подтверждение</option>
</select>
<input type="submit" value="Выолнить" class="inp" />
</div>
</form>
<div class="clr"></div>
HTML;

$query_string = preg_replace("/&page=[0-9]+/i", '', $_SERVER['QUERY_STRING']);
echo navigation($gcount, $numRows['cnt'], '?'.$query_string.'&page=');

htmlclear();

echohtmlend();
?>