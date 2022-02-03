<?php
/* 
	Appointment: Города
	File: city.php
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
	$country = intval($_POST['country']);
	$city = textFilter($_POST['city'], false, true);
	if(isset($city) AND !empty($city) AND $country){
		$row = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_city` WHERE name = '".$city."' AND id_country = '".$country."'");
		if(!$row['cnt']){
			$db->query("INSERT INTO `".PREFIX."_city` SET name = '".$city."', id_country = '".$country."'");
			system_mozg_clear_cache_file('country_city_'.$country);
			msgbox('Информация', 'Город успешно добавлен', '?mod=city');
		} else
			msgbox('Ошибка', 'Такой город уже добавлен', 'javascript:history.go(-1)');
	} else
		msgbox('Ошибка', 'Все поля объязательны', 'javascript:history.go(-1)');
	
	die();
}

//Удаление
if($_GET['act'] == 'del'){
	$id = intval($_GET['id']);
	$row = $db->super_query("SELECT id_country FROM `".PREFIX."_city` WHERE id = '".$id."'");
	if($row){
		$db->query("DELETE FROM `".PREFIX."_city` WHERE id = '".$id."'");
		system_mozg_clear_cache_file('country_city_'.$row['id_country']);
		header("Location: ?mod=city&country=".$row['id_country']);
	}
	die();
}

$sql_ = $db->super_query("SELECT * FROM `".PREFIX."_country` ORDER by `name` ASC", 1);
foreach($sql_ as $row){
	$row['name'] = stripslashes($row['name']);
	$countryes .= <<<HTML
<div style="margin-bottom:5px;border-bottom:1px dashed #ccc;padding-bottom:5px">&raquo;&nbsp; <a href="?mod=city&country={$row['id']}" style="font-size:13px"><b>{$row['name']}</b></a></div>
HTML;
	$all_country .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
	if($_GET['country'] == $row['id'])
		$pref = $row['name'];
}

echoheader();
echohtmlstart('Добавление города');
	
echo <<<HTML
<form method="POST" action="">
Введите название города: &nbsp;&nbsp; <input style="width:150px" type="text" class="inpu" name="city" /> &nbsp;<select name="country" class="inpu" style="width:150px"><option value="">- Укажите страну -</option>{$all_country}</select>
<input type="submit" value="Добавить" name="add" class="inp" style="margin-top:0px" />
</form>
HTML;

//Если запрос на вывод городов
if($_GET['country']){
	echohtmlstart('Города страны: '.$pref);
	$ncountry_id = intval($_GET['country']);
	$sql_c = $db->super_query("SELECT id, name FROM `".PREFIX."_city` WHERE id_country = '".$ncountry_id."'", 1);
	foreach($sql_c as $row_c){
		$row_c['name'] = stripslashes($row_c['name']);
		$cites .= <<<HTML
<div style="margin-bottom:5px;border-bottom:1px dashed #ccc;padding-bottom:5px">&raquo;&nbsp; <span style="font-size:13px"><b>{$row_c['name']}</b></span> &nbsp; <span style="color:#777">[ <a href="?mod=city&act=del&id={$row_c['id']}" style="color:#777">удалить</a> ]</span></div>
HTML;
	}
	echo $cites.'<input type="submit" value="Назад" class="inp" style="margin-top:0px" onClick="history.go(-1); return false" />';
} else {
	echohtmlstart('Выберите страну, к которой хотите просмотреть города:');
	echo $countryes;
}

echohtmlend();
?>