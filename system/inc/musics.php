<?php
/* 
	Appointment: Музыка
	File: musics.php
	Author: f0rt1 
	Engine: Vee Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

//Редактирование
if($_GET['act'] == 'edit'){
	$id = intval($_GET['id']);
	
	//SQL Запрос на вывод информации 
	$row = $db->super_query("SELECT auser_id, artist, name FROM `".PREFIX."_audio` WHERE aid = '".$id."'");
	if($row){
		if(isset($_POST['save'])){
			$artist = textFilter($_POST['artist'], false, true);
			$name = textFilter($_POST['name'], false, true);

			if(isset($artist) AND !empty($artist) AND isset($name) AND !empty($name)){
				$db->query("UPDATE `".PREFIX."_audio` SET artist = '".$artist."', name = '".$name."' WHERE aid = '".$id."'");
				
				mozg_clear_cache_file('user_'.$row['auser_id'].'/audios_profile');
				
				msgbox('Информация', 'Аудиозапись успешно отредактирована', '?mod=musics');
			} else
				msgbox('Ошибка', 'Заполните все поля', '?mod=musics&act=edit&id='.$aid);
		} else {
			$row['artist'] = stripslashes($row['artist']);
			$row['name'] = stripslashes($row['name']);

			echoheader();
			echohtmlstart('Редактирование аудиозаписи');
			
			echo <<<HTML
<style type="text/css" media="all">
.inpu{width:447px;}
textarea{width:450px;height:100px;}
</style>

<form action="" method="POST">

<input type="hidden" name="mod" value="notes" />

<div class="fllogall" style="width:140px">Исполнитель:</div>
 <input type="text" name="artist" class="inpu" value="{$row['artist']}" />
<div class="mgcler"></div>
<input type="hidden" name="mod" value="notes" />

<div class="fllogall" style="width:140px">Название:</div>
 <input type="text" name="name" class="inpu" value="{$row['name']}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:140px">&nbsp;</div>
 <input type="submit" value="Сохранить" class="inp" name="save" style="margin-top:0px" />
 <input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />

</form>
HTML;
			echohtmlend();
		}
	} else
		msgbox('Ошибка', 'Аудиозапись не найдена', '?mod=musics');
		
	die();
}

echoheader();	

$se_uid = intval($_GET['se_uid']);
if(!$se_uid) $se_uid = '';

$se_user_id = intval($_GET['se_user_id']);
if(!$se_user_id) $se_user_id = '';

$sort = intval($_GET['sort']);
$se_name = textFilter($_GET['se_name'], false, true);

if($se_uid OR $sort OR $se_name OR $se_user_id){
	if($se_uid) $where_sql .= "AND aid = '".$se_uid."' ";
	if($se_user_id) $where_sql .= "AND auser_id = '".$se_user_id."' ";
	$query = strtr($se_name, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
	if($se_name) $where_sql .= "AND name LIKE '%".$query."%' ";
	if($sort == 1) $order_sql = "`artist` ASC";
	else if($sort == 2) $order_sql = "`adate` ASC";
	else $order_sql = "`adate` DESC";
} else
	$order_sql = "`adate` DESC";
	
//Выводим список людей
if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
$gcount = 20;
$limit_page = ($page-1)*$gcount;

$sql_ = $db->super_query("SELECT tb1.aid, artist, name, adate, auser_id, tb2.user_name FROM `".PREFIX."_audio` tb1, `".PREFIX."_users` tb2 WHERE tb1.auser_id = tb2.user_id {$where_sql} ORDER by {$order_sql} LIMIT {$limit_page}, {$gcount}", 1);

//Кол-во людей считаем
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_audio` WHERE aid != '' {$where_sql}");

$selsorlist = installationSelected($sort, '<option value="1">по алфавиту</option><option value="2">по дате добавления</option>');

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form action="controlpanel.php" method="GET">

<input type="hidden" name="mod" value="musics" />

<div class="fllogall">Поиск по ID аудиозаписи:</div>
 <input type="text" name="se_uid" class="inpu" value="{$se_uid}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по названию:</div>
 <input type="text" name="se_name" class="inpu" value="{$se_name}" />
<div class="mgcler"></div>

<div class="fllogall">Поиск по ID автора:</div>
 <input type="text" name="se_user_id" class="inpu" value="{$se_user_id}" />
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

echohtmlstart('Список аудиозаписей ('.$numRows['cnt'].')');

foreach($sql_ as $row){
	$row['artist'] = stripslashes($row['artist']);
	$row['name'] = stripslashes($row['name']);
	$row['adate'] = langdate('j M Y в H:i', $row['adate']);

	$users .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;"><a href="/u{$row['auser_id']}" target="_blank">{$row['user_name']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:329px;text-align:center;margin-left:1px"><a href="?mod=musics&act=edit&id={$row['aid']}">{$row['artist']} – &nbsp;{$row['name']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:110px;text-align:center;margin-left:1px">{$row['adate']}</div>
<div style="background:#fff;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-left:1px"><input type="checkbox" name="massaction_list[]" style="float:right;" value="{$row['aid']}" /></div>
<div class="mgcler"></div>
HTML;
}

echo <<<HTML
<script language="text/javascript" type="text/javascript">
function ckeck_uncheck_all() {
    var frm = document.edit;
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
<form action="?mod=massaction&act=musics" method="post" name="edit">
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px">Добавил</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:329px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Название</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Дата добавления</div>
<div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px"><input type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:ckeck_uncheck_all()" style="float:right;"></div>
<div class="clr"></div>
{$users}
<div style="float:right">
<select name="mass_type" class="inpu" style="width:260px">
 <option value="0">- Действие -</option>
 <option value="1">Удалить аудиозаписи</option>
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