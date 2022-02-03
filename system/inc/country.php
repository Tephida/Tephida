<?php
/* 
	Appointment: Страны
	File: country.php
	Author: f0rt1 
	Engine: Vii Engine
	Copyright: NiceWeb Group (с) 2011
	e-mail: niceweb@i.ua
	URL: http://www.niceweb.in.ua/
	ICQ: 427-825-959
	Данный код защищен авторскими правами
*/
if(!defined('MOZG'))
	die('Hacking attempt!');

//Добавление
if(isset($_POST['add'])){
	$country = textFilter($_POST['country'], false, true);
	if(isset($country) AND !empty($country)){
		$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_country` WHERE name = '".$country."'");
		if(!$row['cnt']){
			$db->query("INSERT INTO `".PREFIX."_country` SET name = '".$country."'");
			system_mozg_clear_cache_file('country');
			msgbox('Информация', 'Страна успешно добавлена', '?mod=country');
		} else
			msgbox('Ошибка', 'Такая страна уже добавлена', 'javascript:history.go(-1)');
	} else
		msgbox('Ошибка', 'Введите название страницы', 'javascript:history.go(-1)');
	
	die();
}

//Удаление
if($_GET['act'] == 'del'){
	$id = intval($_GET['id']);
	$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_country` WHERE id = '".$id."'");
	if($row['cnt']){
		$db->query("DELETE FROM `".PREFIX."_country` WHERE id = '".$id."'");
		system_mozg_clear_cache_file('country');
		header("Location: ?mod=country");
	}
	die();
}

$sql_ = $db->super_query("SELECT * FROM `".PREFIX."_country` ORDER by `name` ASC", 1);
foreach($sql_ as $row){
	$countryes .= <<<HTML
<div style="margin-bottom:5px;border-bottom:1px dashed #ccc;padding-bottom:5px">&raquo;&nbsp; <span style="font-size:13px"><b>{$row['name']}</b></span> &nbsp; <span style="color:#777">[ <a href="?mod=country&act=del&id={$row['id']}" style="color:#777">удалить</a> ]</span></div>
HTML;
}

echoheader();
echohtmlstart('Добавление страны');

echo <<<HTML
<form method="POST" action="">
Введите название страны: &nbsp;&nbsp;<input type="text" class="inpu" name="country" />
<input type="submit" value="Добавить" name="add" class="inp" style="margin-top:0px" />
</form>
HTML;

echohtmlstart('Список стран');

echo <<<HTML
{$countryes}
HTML;

echohtmlend();
?>