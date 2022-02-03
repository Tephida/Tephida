<?php
/* 
	Appointment: Видео
	File: videos.php
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
	$row = $db->super_query("SELECT owner_user_id, title, descr, video FROM `".PREFIX."_videos` WHERE id = '".$id."'");
	if($row){
		if(isset($_POST['save'])){
			$title = textFilter($_POST['title'], false, true);
			$descr = textFilter($_POST['descr']);

			if(isset($title) AND !empty($title) AND isset($descr) AND !empty($descr)){
				$db->query("UPDATE `".PREFIX."_videos` SET title = '".$title."', descr = '".$descr."' WHERE id = '".$id."'");
				
				//Чистим кеш
				mozg_mass_clear_cache_file("user_{$row['owner_user_id']}/page_videos_user|user_{$row['owner_user_id']}/page_videos_user_friends|user_{$row['owner_user_id']}/page_videos_user_all|user_{$row['owner_user_id']}/videos_num_all|user_{$row['owner_user_id']}/videos_num_friends");
					
				msgbox('Информация', 'Видеозапись успешно отредактирована', '?mod=videos');
			} else
				msgbox('Ошибка', 'Заполните все поля', '?mod=videos&act=edit&id='.$id);
		} else {
			$row['title'] = stripslashes($row['title']);
			$row['descr'] = stripslashes(myBrRn($row['descr']));
			$row['video'] = stripslashes(myBrRn($row['video']));

			echoheader();
			echohtmlstart('Редактирование видео');
			
			echo <<<HTML
<style type="text/css" media="all">
.inpu{width:447px;}
textarea{width:450px;height:100px;}
</style>

<form action="" method="POST">

<input type="hidden" name="mod" value="notes" />

<div class="fllogall" style="width:140px">Название:</div>
 <input type="text" name="title" class="inpu" value="{$row['title']}" />
<div class="mgcler"></div>

<div class="fllogall" style="width:140px">Описание:</div>
 <textarea name="descr" class="inpu">{$row['descr']}</textarea>
<div class="mgcler"></div>

<div class="fllogall" style="width:140px">&nbsp;</div>
 <input type="submit" value="Сохранить" class="inp" name="save" style="margin-top:0px" />
 <input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />

</form>
HTML;
			echohtmlend();
		}
	} else
		msgbox('Ошибка', 'Видео не найдено', '?mod=videos');
		
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
	if($se_uid) $where_sql .= "AND id = '".$se_uid."' ";
	if($se_user_id) $where_sql .= "AND owner_user_id = '".$se_user_id."' ";
	$query = strtr($se_name, array(' ' => '%')); //Замеянем пробелы на проценты чтоб тоиск был точнее
	if($se_name) $where_sql .= "AND title LIKE '%".$query."%' ";
	if($sort == 1) $order_sql = "`title` ASC";
	else if($sort == 2) $order_sql = "`add_date` ASC";
	else if($sort == 3) $order_sql = "`views` DESC";
	else if($sort == 4) $order_sql = "`comm_num` DESC";
	else $order_sql = "`add_date` DESC";
} else
	$order_sql = "`add_date` DESC";
	
//Выводим список людей
if($_GET['page'] > 0) $page = intval($_GET['page']); else $page = 1;
$gcount = 20;
$limit_page = ($page-1)*$gcount;

$sql_ = $db->super_query("SELECT tb1.id, title, comm_num, add_date, owner_user_id, views, tb2.user_name FROM `".PREFIX."_videos` tb1, `".PREFIX."_users` tb2 WHERE tb1.owner_user_id = tb2.user_id {$where_sql} ORDER by {$order_sql} LIMIT {$limit_page}, {$gcount}", 1);

//Кол-во людей считаем
$numRows = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_videos` WHERE id != '' {$where_sql}");

$selsorlist = installationSelected($sort, '<option value="1">по алфавиту</option><option value="2">по дате добавления</option><option value="3">по количеству просмотров</option><option value="4">по количеству комментариев</option>');

echo <<<HTML
<style type="text/css" media="all">
.inpu{width:300px;}
textarea{width:300px;height:100px;}
</style>

<form action="controlpanel.php" method="GET">

<input type="hidden" name="mod" value="videos" />

<div class="fllogall">Поиск по ID видео:</div>
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

echohtmlstart('Список видео ('.$numRows['cnt'].')');

foreach($sql_ as $row){
	$row['title'] = stripslashes($row['title']);
	$row['add_date'] = langdate('j M Y в H:i', strtotime($row['add_date']));

	$users .= <<<HTML
<div style="background:#fff;float:left;padding:5px;width:100px;text-align:center;"><a href="/u{$row['owner_user_id']}" target="_blank">{$row['user_name']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:243px;text-align:center;margin-left:1px"><a href="?mod=videos&act=edit&id={$row['id']}" title="Комментариев: {$row['comm_num']}">{$row['title']}</a></div>
<div style="background:#fff;float:left;padding:5px;width:75px;text-align:center;margin-left:1px">{$row['views']}</div>
<div style="background:#fff;float:left;padding:5px;width:110px;text-align:center;margin-left:1px">{$row['add_date']}</div>
<div style="background:#fff;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-left:1px"><input type="checkbox" name="massaction_list[]" style="float:right;" value="{$row['id']}" /></div>
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
<form action="?mod=massaction&act=videos" method="post" name="edit">
<div style="background:#f0f0f0;float:left;padding:5px;width:100px;text-align:center;font-weight:bold;margin-top:-5px">Добавил</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:243px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Название</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:75px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Просмотров</div>
<div style="background:#f0f0f0;float:left;padding:5px;width:110px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px">Дата добавления</div>
<div style="background:#f0f0f0;float:left;padding:4px;width:20px;text-align:center;font-weight:bold;margin-top:-5px;margin-left:1px"><input type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:ckeck_uncheck_all()" style="float:right;"></div>
<div class="clr"></div>
{$users}
<div style="float:right">
<select name="mass_type" class="inpu" style="width:260px">
 <option value="0">- Действие -</option>
 <option value="1">Удалить видеозаписи</option>
 <option value="2">Очистить комментарии</option>
 <option value="3">Очистить просмотры</option>
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